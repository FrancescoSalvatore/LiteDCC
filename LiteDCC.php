<?php
/****  LiteDCC - an IRC Iroffer by Francesco Salvatore  ****/

error_reporting(0);

/** Includes Area **/
include_once("config.php");
include_once("version.php");
include_once("start_checkup.php");
include_once("admin-commands.php");
include_once("xdcc-send.php");
include_once("xdcc-commands.php");
include_once("class/IRCConnection.php");
include_once("class/ListManager.php");
include_once("class/DCCTransfer.php");
include_once("class/DCCListManager.php");
include_once("class/HTTPServer.php");
include_once("class/Colors.php");

/* --------------------------------------------------------------------------------- */

/** Testing CL options **/
$option = getopt("v");
foreach($option as $key => $val)
{
	switch($key)
	{
		case "v": define('VERBOSE', true); break;
	}
}


/** LiteDCC is starting... **/
$COLORS = new Colors();
echo "\t".$COLORS->getColoredString("LiteDCC ".VERSION." by Francesco Salvatore \n\n", "light_cyan");
echo "LiteDCC is starting...\n\n";


/** Testing requirements **/
echo "Testing requirements...";
$ret = start_checkup();
if($ret === TRUE)
	echo "\t\t\t[ ".$COLORS->getColoredString("OK", "light_green")." ]\n";
else
{
	echo "\t\t\t[ ".$COLORS->getColoredString("ERROR", "light_red")." ]\n";
	echo $ret."\n\nTerminated execution.\n";
	exit(1);
}


/** Setting up the connection **/
echo "Connecting to the server...";
$IRC = new IRCConnection();
$ret = $IRC->connect(IRC_SERVER, IRC_PORT, IRC_NICKNAME, IRC_PASSWORD);
if($ret === TRUE)
	echo "\t\t[ ".$COLORS->getColoredString("OK", "light_green")." ]\n";
else
{
	echo "\t\t[ ".$COLORS->getColoredString("ERROR", "light_red")." ]\n";
	echo $ret."\n\nTerminated execution.\n";
	exit(1);
}
$IRC->joinChannel(IRC_CHANNEL, IRC_CHANNEL_ENTRY_MESSAGE);



echo "Loading databases...";
/** Load List Manager **/
$LIST = new ListManager(DB_FILE);

/** Load DCC transfers list manager **/
$DCCLIST = new DCCListManager(TRANSFERS_FILE);

echo "\t\t\t[ ".$COLORS->getColoredString("OK", "light_green")." ]\n";


/** Launching HTTP server **/
if(HTTP_ENABLED)
{
	echo "Launching HTTP server on port ".HTTP_PORT."...";
	$HTTP = new HTTPServer(HTTP_DEFAULT_PAGE, HTTP_ADDRESS, HTTP_PORT);
	if( ($ret = $HTTP->start()) != TRUE) 
	{
		echo "\t[ ".$COLORS->getColoredString("ERROR", "light_red")." ]\n";
		echo $ret."\n\nTerminated execution.\n";
		exit(1);
	}
	else echo "\t[ ".$COLORS->getColoredString("OK", "light_green")." ]\n";
}


echo "\n\n";

echo $COLORS->getColoredString("BOT STARTED!\n", "light_blue");
echo "The bot might take a few seconds to join the channel\n\n";

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
		if(VERBOSE) echo "- (".date("Y-m-d H:i:s").") PING received from ".$data["sender"]."\n";
		continue;
	}
	
	if($data["type"] == "USERMSG")
	{
		$parts = explode(" ", $data["content"]);
		
		if(VERBOSE) echo "- (".date("Y-m-d H:i:s").") USERMSG received from ".$data["sender"].": ".$data["content"]."\n";
		
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
