<?php

//! DCCTransfer Class
/*!
This class manage a DCC transfer, in compliance with original [DCC](http://www.irchelp.org/irchelp/rfc/dccspec.html) protocol.
\author Francesco Salvatore
\copyright Apache License 2.0
*/
class DCCTransfer
{
	//! Block Size
	/*!
	Integer, the byte size of single blocks that will be sent in transfer.
	> Note: the default size (2048) is recommended because is compatible with most of clients.
	*/
	const block_size = 2048; //Bytes per block
	
	private $socket;
	
	private $file;
	
	private $filesize;
	
	//! Contructor
	/*!
	\param $socket Socket, the transfer socket already connected.
	\param $file String, the file to transfer.
	\param $filesize Integer, the byte size of file.
	\param $filepointer Integer, optional, the start position of transfer. The offset start from 0 (zero) and it is counted in byte.
	*/
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
	
	//! Send next block of file
	/*!
	Send the next block of the file to recipient.
	\return TRUE if data are sent correctly or a string containing socket error on failure.
	*/
	function sendNextBlock()
	{
		if(socket_write($this->socket, fread($this->file, self::block_size), self::block_size) === FALSE)
			return socket_last_error();
		else	return TRUE;
	}
	
	//! Get amount of sent data
	/*!
	\return An integer containing the amount of sent data in bytes.
	> **Warning:** sent data are calculated from filepointer actual position, so is not the really amount of data transfered if you are in a resumed transfer.
	*/
	function getSentData()
	{
		return ftell($this->file);
	}
	
	//! Get the block size
	/*!
	\return An integer containing the block size in bytes.
	*/
	function getBlockSize()
	{
		return $this::block_size;
	}
	
	//! Wait until the client close the connection
	/*!
	Wait until the connection is closed by client.
	*/
	function waitForClosing()
	{
		while( !(socket_read($this->socket, 2048) === "") ) {}
	}
	
	//! Check if you have reached the end of file
	/*!
	\return TRUE if you have reached the end of file or FALSE in other cases.
	*/
	function is_eof()
	{
		if(feof($this->file))
			return TRUE;
		else	return FALSE;
	}
	
	//! Close the connection
	function closeConnection()
	{
		socket_close($this->socket);
	}
}


?>
