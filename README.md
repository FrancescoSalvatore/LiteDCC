LiteDCC
=======
####Version 0.4 beta
A simple IRC iroffer bot completely written in PHP.


=======
##Requirements

LiteDCC requires a Unix system with PHP up to 5.3.0 installed, and it works only in CLI mode (from command line).

LiteDCC DOES NOT WORK on Windows (because multitasking library ``` pcntl ``` is not compatible with Win systems).

=======

##Installation, configuration and start to use
! Keep in mind that LiteDCC is a work in progess project. So, if you are searching for a stable DCC bot please go away.

Copy and paste on your terminal (Debian/Ubuntu):
```
sudo apt-get install php5-common php5-cli php5-sqlite
```

Edit configuration file (```config.php```) with your personal configurations and then launch **db_create.php** (```php db_create.php```) from terminal to build the List and Transfers file.

Now you can run directly the bot typing ```php LiteDCC.php```

Done!

=======

##Today Features List

* DCC SEND (with support for Turbo DCC) with transfer speed up to 8 MB/s per transfer
* RESUME supported
* Files list managed by SQLite, so very easy to move beetween different machines and installations
* Admin commands supported likes file adding, file removing, listing current transfers, shutdown bot etc... with password protection
* XDCC traditional commands supported like LIST, INFO, SEND, SEARCH etc...
* Support for bandwidth controls
* Incorporated HTTP server for tracking active transfers and file list
* High configurable options

=======

##Tomorrow Features List

* Support for differents DBMS for file-list and transfer-list
* Support for queues
* Switch to another multitasking library also supported by Windows
