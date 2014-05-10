<?php

function admin_commands_executor($command, $sender)
{
	global $LIST, $IRC;
	
	$parts = explode( " ", $command );
	
	if($parts[1] != ADMIN_PASSWORD) return;
	
	
	switch(trim(strtolower($parts[2])))
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
				$IRC->sendUserMessage($sender, "Comando ADMIN non identificato o non supportato");
				break;
	}
}

?>
