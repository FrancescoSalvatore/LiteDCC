<?php

function xdcc_commands_executor($command, $sender)
{
	global $LIST, $IRC;
	
	$parts = explode( " ", $command );
	
	switch(trim(strtolower($parts[1])))
	{
		case "list":
				$list = $LIST->getList();
				$IRC->sendNotice($sender, "Lista dei file disponibili:");
				foreach($list as $key => $value)
				{
					$message = "- Pack #".$key.", \"".$value['filename']."\"";
					$IRC->sendNotice($sender, $message);
				}
				break;
				
		case "info":
				$info = $LIST->getFileInfo(trim($parts[2]));
				$IRC->sendNotice($sender, "Informazioni per il pack #".trim($parts[2]));
				$IRC->sendNotice($sender, "Nome File:         ".$info['filename']);
				$IRC->sendNotice($sender, "Grandezza:         ".$info['filesize']." [".human_filesize($info['filesize'])."]");
				$IRC->sendNotice($sender, "Data Inserimento:  ".$info['add_date']);
				$IRC->sendNotice($sender, "MD5:               ".$info['md5']);
				$IRC->sendNotice($sender, "Preso:             ".$info['taken']);
				break;
				
		case "send":
				$info = $LIST->getFileInfo(trim($parts[2]));
				xdcc_send(FILE_PATH . $info["filename"], $info["filesize"], $sender);
				$LIST->incrementTaken(trim($parts[2]));
				break;
				
		default:
				$IRC->sendNotice($sender, "Comando XDCC non identificato o non supportato");
				break;
	}
}


function human_filesize($bytes, $decimals = 2) {
  $sz = ' BKBMBGBTBPB';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor*2];
}

?>
