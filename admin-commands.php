<?php

function admin_commands_executor($command, $sender)
{
	global $LIST, $IRC, $DCCLIST;
	
	$parts = explode( " ", $command );
	
	if($parts[1] != ADMIN_PASSWORD) return;
	
	if(trim(strtolower($parts[2])) == "file")
	{
		switch(trim(strtolower($parts[3])))
		{
			case "add":
					$filesize = filesize(FILE_PATH . trim($parts[3]));
					$md5sum = md5_file(FILE_PATH . trim($parts[3]));
					if($LIST->addFile(trim($parts[3]), $filesize, $md5sum))
						$IRC->sendUserMessage($sender, "FILE ".trim($parts[3])." AGGIUNTO ALLA LISTA!");
					break;
				
			case "remove":
					$LIST->removeFile(trim($parts[3]));
					$IRC->sendUserMessage($sender, "PACK #".trim($parts[3])." RIMOSSO DALLA LISTA!");
					break;
				
			default:
					$IRC->sendUserMessage($sender, "Comando ADMIN FILE non identificato o non supportato");
					break;
		}
	}
	elseif(trim(strtolower($parts[2])) == "transfer")
	{
		switch(trim(strtolower($parts[3])))
		{
			case "list":
					$transf = $DCCLIST->getAllTransfersData();
					if(empty($transf))
					{
						$IRC->sendUserMessage($sender, "Non c'è nessun trasferimento attivo al momento.");
						break;
					}
					foreach($transf as $key => $value)
					{
						$message = "ID: ".$key."  -";
						$message .= "  Utente: ".$value['recipient']."  -";
						$message .= "  File: #".$value['package']." (".$LIST->getFileName($value['package']).")  -";
						$percentage = (($LIST->getFileSize($value['package']) / $value['byte_sent'])) * 100;
						settype($percentage, 'integer');
						$message .= "  Completato: ".$percentage."%";
						$IRC->sendUserMessage($sender, $message);
					}
					break;
					
					
			case "stop":
					if($DCCLIST->removeTransfer(trim($parts[4])))
					$IRC->sendUserMessage($sender, "Trasferimento #".trim($parts[4])." fermato con successo.");
					break;
			
			default:
					$IRC->sendUserMessage($sender, "Comando ADMIN TRANSFER non identificato o non supportato");
					break;
		}
	}
}

?>
