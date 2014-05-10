<?php

$db = new SQLite3('list.db');

$db->exec("CREATE TABLE IF NOT EXISTS list (filename TEXT, filesize INTEGER, add_date TEXT, md5 TEXT, taken INTEGER);");

$db->open('transfers.db');

$db->exec("CREATE TABLE IF NOT EXISTS transfers (recipient TEXT, package TEXT, byte_sent INTEGER, timestamp TEXT);");

$db->close();

?>
