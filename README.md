LiteDCC
=======
####Version 0.1 alpha
A simple IRC iroffer completely written in PHP.


=======
##Requirements

LiteDCC requires a Unix system with PHP up to 5.3.0 installed.

LiteDCC DOES NOT WORK on Windows (because multitasking library ``` pcntl ``` is not compatible with Win systems).

=======

##Installation
! Keep in mind that LiteDCC is a work in progess project and i have no finished it. So, if you are searching for a stable DCC bot please go away.

Copy and paste on your terminal (Debian/Ubuntu):
```
sudo apt-get install php5-common php5-cli php5-sqlite
```

Done!

=======

##Today Features List

* DCC SEND (and Fast DCC) with transfer speed up to 8 MB/s per transfer
* Files list managed by SQLite, so very easy to move beetween different machines and installations
* Admin commands supported likes file adding, file removing, listing current transfers, shutdown bot etc... with password protection
* XDCC traditional commands supported like LIST, INFO, SEND, SEARCH etc...
* High configurable options

=======

##Tomorrow Features List

* Support for differents DBMS for file-list and transfer-list
* Support for queues
* Support for bandwidth controls
* Support for RESUME
* Incorporated HTTP server for bot statistics (active transfers, weekly stats etc...)
* Switch to another multitasking library also supported by Windows
