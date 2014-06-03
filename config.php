<?php
/*****  CONFIGURATION FILE  *****/


/*** IRC ***/

//SERVER
define('IRC_SERVER', 'irc.quartznet.org');

//PORT
define('IRC_PORT', '6667');

//NICKNAME
define('IRC_NICKNAME', 'AN-MATA');

//PASSWORD
define('IRC_PASSWORD', '');

//CHANNEL
define('IRC_CHANNEL', '#canaletto');

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
define('TRANSFERS_BANDWIDTH', 0);

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

//HTTP server enabled?
define('HTTP_ENABLED', true);

//HTTP host address to bind to
define('HTTP_ADDRESS', '127.0.0.1');

//HTTP host port
define('HTTP_PORT', 8080);

//HTTP default page
define('HTTP_DEFAULT_PAGE', '/home/francesco/Documenti/iroffer/LiteDCC/webpage.php');

/*** ADMIN POWER ***/

//ADMIN PASSWORD
define('ADMIN_PASSWORD', 'PWD');


?>
