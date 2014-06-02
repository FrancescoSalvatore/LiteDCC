<?php

/*! \mainpage LiteDCC - An easy PHP iroffer
 ####Version 0.3 beta
A simple IRC iroffer bot completely written in PHP.


=======
##Requirements

LiteDCC requires a Unix system with PHP up to 5.3.0 installed, and it works only in CLI mode (from command line).

LiteDCC DOES NOT WORK on Windows (because multitasking library *pcntl* is not compatible with Win systems).

=======

##Installation, configuration and start to use
! Keep in mind that LiteDCC is a work in progess project. So, if you are searching for a stable DCC bot please go away.

Copy and paste on your terminal (Debian/Ubuntu):

> sudo apt-get install php5-common php5-cli php5-sqlite

Edit configuration file (```config.php```) with your personal configurations and then launch **db_create.php** (```php db_create.php```) from terminal to build the List and Transfers file.

Now you can run directly the bot typing ```php LiteDCC.php```

Done!

=======
*/










/*! \page FAQ Frequently Asked Questions

###Why LiteDCC supports only file no much larger than 2GB?
LiteDCC supports only file with size inferior than 2GB because PHP built-in integer type is 32bit signed, so the filesize() function cannot return a value larger than 2147483648 byte, that is 2GB.

If you want to manage larger files you can use 64bit version of PHP (that is in beta) but anyway DCC protocol is limited at 4GB, so the maximum filesize results even so 4GB.

*/

?>
