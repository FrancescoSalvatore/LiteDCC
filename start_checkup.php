<?php

function start_checkup()
{
	$required_extensions = Array(
				'pcntl',
				'sqlite3',
				'posix',
				'sockets'
				);
				
	$required_php_version = '5.3.0';
	
	
	//Test PHP Version
	if(version_compare(PHP_VERSION, $required_php_version, '<'))
	{
		return "Your version of PHP (".PHP_VERSION.") is too old. You require at least PHP ".$required_php_version;
	}
	
	//Test if launching interface is CLI
	if(PHP_SAPI != "cli") return "The script seems to be launched in a different interface instead of CLI mode";
	
	//Test required extensions
	foreach($required_extensions as $value)
	{
		if(!extension_loaded($value))
			return "A required extension of PHP is missing (".$value.")";
	}
	
	//Test List and Transfers files
	if(!file_exists(DB_FILE)) return "The List file (".DB_FILE.") doesn't exist.";
	if(!file_exists(TRANSFERS_FILE)) return "The Transfers file (".TRANSFERS_FILE.") doesn't exist.";
	
	
	
	
	return TRUE; //Everything is fine!
}

?>
