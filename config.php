<?php
/*****  CONFIGURATION FILE  *****/


/*** IRC ***/

//SERVER
define('IRC_SERVER', 'YOUR_SERVER');

//PORT
define('IRC_PORT', '6667');

//NICKNAME
define('IRC_NICKNAME', 'BOT_NICKNAME');

//PASSWORD
define('IRC_PASSWORD', '');

//CHANNEL
define('IRC_CHANNEL', 'YOUR_CHANNEL');

//CHANNEL ENTRY MESSAGE - set to NULL if you don't want an entry message
define('IRC_CHANNEL_ENTRY_MESSAGE', 'ENTRY_MESSAGE');

//SQLite List File
define('DB_FILE', 'db/list.db');

//SQLite Transfers File
define('TRANSFERS_FILE', 'db/transfers.db');

//Timetick for transfers list updating (in seconds)
//ATTENTION! A value too low ​​may significantly slow down the bot
define('TRANSFERS_UPDATE_TIME', 3);

//Maximum bandwidth reserved for each file transfers in KB/s (set 0 for unlimited speed)
//This value is "virtual" (and it is minor precise with higher speeds) because real transfer speed is established by a lot of factors
define('TRANSFERS_BANDWIDTH', 500);

//Maximum simultaneous transfers (set 0 for unlimited)
define('TRANSFERS_SIMUL_MAX', 1);

//File's directory path
define('FILE_PATH', 'file/');

//DCC Transfer port to bind to, set 0 (zero) to bind to a random available port
//If you are behind a NAT please refer to NAT port and not to system port
define('DCC_PORT', 9999);

//DCC host address
//If you are behind a NAT please refer to NAT address
define('DCC_ADDRESS', '192.168.25.128');

/*** ADMIN POWER ***/

//ADMIN PASSWORD
define('ADMIN_PASSWORD', 'PWD');


?>
