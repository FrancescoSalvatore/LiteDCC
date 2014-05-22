<?php
/****  IRC Iroffer by Francesco Salvatore  ****/

error_reporting(E_ALL ^ E_NOTICE);

/** Includes Area **/
include_once("config.php");
include_once("admin-commands.php");
include_once("xdcc-send.php");
include_once("xdcc-commands.php");
include_once("class/IRCConnection.php");
include_once("class/ListManager.php");
include_once("class/DCCTransfer.php");
include_once("class/DCCListManager.php");

/** Setting up the connection **/
$IRC = new IRCConnection();
$IRC->connect(IRC_SERVER, IRC_PORT, IRC_NICKNAME, IRC_PASSWORD);
$IRC->joinChannel(IRC_CHANNEL, IRC_CHANNEL_ENTRY_MESSAGE);

/** Load List Manager **/
$LIST = new ListManager(DB_FILE);

/** Load DCC transfers list manager **/
$DCCLIST = new DCCListManager(DB_TRANSFERS_FILE);

/** Processes **/
$PIDS = Array();

/** Cleans up transfers list **/
$DCCLIST->clearDB();

/** Set startup time **/
define('STARTUP_TIME', time());

/** Enter in main loop **/
while($IRC->isConnected())
{
	$data = $IRC->getData();
	if($data == null) { continue; }
	
	if($data["type"] == "PING")
	{
		$IRC->pong($data["sender"]);
		continue;
	}
	
	if($data["type"] == "USERMSG")
	{
		$parts = explode(" ", $data["content"]);
		
		echo "\n";
		echo trim($parts[0], "0\x03")."\n";
		echo $parts[1]."\n";
		echo $parts[2]."\n";
		echo $parts[3]."\n";
		
		if(trim($parts[0], " \t\n\r\0\x0B\x030") == "admin")
		{
			admin_commands_executor($data["content"], $data["sender"]);
		}
		else if(trim($parts[0], " \t\n\r\0\x0B\x030") == "xdcc")
		{
			xdcc_commands_executor($data["content"], $data["sender"]);
		}
	}
}

?>
