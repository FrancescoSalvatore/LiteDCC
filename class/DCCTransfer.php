<?php

class DCCTransfer
{
	const block_size = 2048; //Bytes per block
	
	private $socket;
	
	private $file;
	
	private $filesize;

	function __construct($socket, $file, $filesize, $filepointer = 0)
	{
		$this->socket = $socket;
		$this->filesize = $filesize;
		$this->file = fopen($file, "rb");
		fseek($this->file, $filepointer);
	}
	
	function __destruct()
	{
		fclose($this->file);
	}
	
	function sendNextBlock()
	{
		if(socket_write($this->socket, fread($this->file, self::block_size), self::block_size) === FALSE)
			return socket_last_error();
		else	return TRUE;
	}
	
	function getSentData()
	{
		return ftell($this->file);
	}
	
	/*function waitForAck()
	{
		$response = socket_read($this->socket, 4);
		if(trim($response) == $this->getSentData())
			return TRUE;
		else	return FALSE;
	}*/
	
	function waitForClosing()
	{
		while( !(socket_read($this->socket, 2048) === "") ) {}
	}
	
	
	function is_eof()
	{
		if(feof($this->file))
			return TRUE;
		else	return FALSE;
	}
	
	function closeConnection()
	{
		socket_close($this->socket);
	}
}


?>
