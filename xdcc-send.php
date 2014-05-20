<?php

//Initialize the transfer
function xdcc_send($requested_file, $filesize, $applicant)
{
	global $IRC;
	
	$listen_socket = socket_create_listen(DCC_PORT);
	
	//Send DCC answer to applicant
	$IRC->sendDCCResponse($applicant, basename($requested_file), DCC_ADDRESS, DCC_PORT, $filesize);
	
	$socket = socket_accept($listen_socket);
	
	$pid = pcntl_fork();
	
	if($pid > 0)
	{
		xdcc_transfer($requested_file, $filesize, $applicant, $socket);
		die();
	}
	
	socket_close($listen_socket);
}




function xdcc_transfer($requested_file, $filesize, $applicant, $socket)
{
	global $IRC;
	global $DCCLIST;
	global $LIST;
	
	$DCC = new DCCTransfer($socket, $requested_file, $filesize);
	$transfer_id = $DCCLIST->createNewTransfer($applicant, $LIST->getPackageNumberByName(basename($requested_file)));
	$time = time() + TRANSFERS_UPDATE_TIME;
	while(!$DCC->is_eof())
	{
		if( $DCC->sendNextBlock() !== TRUE )
		{
			$IRC->sendNotice($applicant, "Errore di connessione, il trasferimento è stato chiuso.");
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
		
	}
	$DCC->waitForClosing();
	$DCC->closeConnection();
	$DCCLIST->removeTransfer($transfer_id);
	$IRC->sendNotice($applicant, "Trasferimento del file \"".basename($requested_file)."\" completato.");
	return;
}

?>
