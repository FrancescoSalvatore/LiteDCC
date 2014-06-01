<?php

//Initialize the transfer
function xdcc_send($requested_file, $filesize, $applicant)
{
	global $IRC;
	
	$listen_socket = socket_create_listen(DCC_PORT);
	
	$position = 0;
	socket_set_nonblock($listen_socket);
	$time = time();
	
	//Send DCC answer to applicant
	$IRC->sendDCCResponse($applicant, basename($requested_file), DCC_ADDRESS, DCC_PORT, $filesize);
	
	$IRC->setNonBlockingSocket();
	
	//Test if a RESUME is requested and wait for a connection
	while(time() <= ($time+15))
	{
		$socket = socket_accept($listen_socket);
		if($socket) break;
		$resume = $IRC->isResumeRequested();
		if($resume !== FALSE)
		{
			$position = $resume;
			$IRC->sendDCCAccept($applicant, basename($requested_file), DCC_PORT, $position);
		}
	}
	
	$IRC->setBlockingSocket();
	
	//Connection timed-out
	if($socket == FALSE)
	{
		echo "QUIIIIIIIIIII";
		$IRC->sendNotice($applicant, "Connessione in timeout. Controllare i settaggi del proprio client e riprovare.");
		return;
	}
	
	$pid = pcntl_fork();
	
	if($pid > 0)
	{
		xdcc_transfer($requested_file, $filesize, $position, $applicant, $socket);
		die();
	}
	
	socket_close($listen_socket);
}




function xdcc_transfer($requested_file, $filesize, $filepoint, $applicant, $socket)
{
	global $IRC;
	global $DCCLIST;
	global $LIST;
	
	$DCC = new DCCTransfer($socket, $requested_file, $filesize, $filepoint);
	$transfer_id = $DCCLIST->createNewTransfer($applicant, $LIST->getPackageNumberByName(basename($requested_file)));
	$time = time() + TRANSFERS_UPDATE_TIME;
	
	//Set max bandwidth for this transfer
	if(TRANSFERS_BANDWIDTH == 0)
		$time_to_sleep = 0;
	else
	{
		$time_to_sleep = 1000000 / ( (TRANSFERS_BANDWIDTH*1024 ) / $DCC->getBlockSize());
		$time_to_sleep -= ( ($time_to_sleep / 100) * (TRANSFERS_BANDWIDTH * 0.04) ); //CPU ticks adjust
	}
		
	while(!$DCC->is_eof())
	{
		if( $DCC->sendNextBlock() !== TRUE )
		{
			$IRC->sendNotice($applicant, "Errore di connessione, il trasferimento è stato chiuso.");
			$DCCLIST->removeTransfer($transfer_id);
			$DCC->closeConnection();
			return;
		}
		
		//Update time and check if i'm also alive
		if($time < time())
		{
			if(!$DCCLIST->isTransferAlive($transfer_id))
			{
				$DCC->closeConnection();
				$IRC->sendNotice($applicant, "Il trasferimento del file è stato interrotto da un amministratore oppure è stato riscontrato un errore interno.");
				return;
			}
			
			$DCCLIST->updateSentData($transfer_id, $DCC->getSentData());
			$time = time() + TRANSFERS_UPDATE_TIME;
		}
		usleep($time_to_sleep);
		
	}
	$DCC->waitForClosing();
	$DCC->closeConnection();
	$DCCLIST->removeTransfer($transfer_id);
	$IRC->sendNotice($applicant, "Trasferimento del file \"".basename($requested_file)."\" completato.");
	return;
}

?>
