<?php
/*****  CONFIGURATION FILE  *****/


/*** IRC ***/

//SERVER
define('IRC_SERVER', 'irc.quartznet.org');

//PORT
define('IRC_PORT', '6667');

//NICKNAME
define('IRC_NICKNAME', 'ANAL-MASTER');

//PASSWORD
define('IRC_PASSWORD', '');

//CHANNEL
define('IRC_CHANNEL', '#anal');

//CHANNEL ENTRY MESSAGE - set to NULL if you don't want an entry message
define('IRC_CHANNEL_ENTRY_MESSAGE', 'ECCOMI CAZZO!');

//SQLite List File
define('DB_FILE', 'db/list.db');

//SQLite Transfers File
define('DB_FILE', 'db/transfers.db');

//File's directory path
define('FILE_PATH', 'file/');

//DCC Transfer port to bind to, set 0 (zero) to bind to a random available port
//If you are behind a NAT please refer to NAT port and not to system port
define('DCC_PORT', 9999);

//DCC host address
define('DCC_ADDRESS', '192.168.25.128');

/*** ADMIN POWER ***/

//ADMIN PASSWORD
define('ADMIN_PASSWORD', 'ciao');


?>