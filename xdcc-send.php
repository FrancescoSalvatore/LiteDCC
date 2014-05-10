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
	
	$DCC = new DCCTransfer($socket, $requested_file, $filesize);
	while(!$DCC->is_eof())
	{
		if( $DCC->sendNextBlock() !== TRUE)
		{
			$IRC->sendNotice($applicant, "Errore di connessione, il trasferimento è stato chiuso.");
			$DCC->closeConnection();
			return;
		}
		
	}
	$DCC->waitForClosing();
	$DCC->closeConnection();
	$IRC->sendNotice($applicant, "Trasferimento del file \"".basename($requested_file)."\" completato.");
	return;
}

?>
