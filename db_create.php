<?php

include_once("config.php");
include_once("class/Colors.php");
$COLORS = new Colors();

echo "\nLiteDCC DB Creator\n\n";

echo "Creating databases...";
$list = new SQLite3(DB_FILE, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
$transfers = new SQLite3(TRANSFERS_FILE, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
echo "\t[ ".$COLORS->getColoredString("OK", "light_green")." ]\n";

echo "Building tables...";
if(!$list->exec("CREATE TABLE IF NOT EXISTS list (filename TEXT, filesize INTEGER, add_date TEXT, md5 TEXT, taken INTEGER);"))
{
	echo "\t[ ".$COLORS->getColoredString("ERROR", "light_red")." ]\n";
	echo $list->lastErrorMsg()."\n\nTerminated execution.\n";
	exit(1);
}
if(!$transfers->exec("CREATE TABLE IF NOT EXISTS transfers (recipient TEXT, package TEXT, byte_sent INTEGER, timestamp TEXT);"))
{
	echo "\t[ ".$COLORS->getColoredString("ERROR", "light_red")." ]\n";
	echo $list->lastErrorMsg()."\n\nTerminated execution.\n";
	exit(1);
}
echo "\t[ ".$COLORS->getColoredString("OK", "light_green")." ]\n";

echo "\nDB CREATED!\n";

$list->close();
$transfers->close();

?>
