<?php

//! HTTPServer Class
/*!
This class launch and manage a simple HTTP server.

The server can run only the default page passed to it and it supports only PHP pages.

> **Warning:** this server does not support other files than the default page, so if you want to insert some customization in default page likes images or stylesheets you have to refer to a remote server.

Every requests will be treatened like a request pointed to default page.

\author Francesco Salvatore
\copyright Apache License 2.0
*/
class HTTPServer
{
	private $socket;
	
	private $default_page;
	
	private $host;
	
	private $port;
	
	private $pid;
	
	private $response_header;
	
	//! Contructor
	/*!
	\param $default_page String, is the path of default file that will be executed by the server when it receive a request.
	\param $host String, is the binding host
	\param $port Integer, optional, is the binding port
	*/
	function __construct($default_page, $host, $port = 80)
	{
		$this->default_page = $default_page;
		$this->initResponseHeader();
		$this->host = $host;
		$this->port = $port;	
	}
	
	function __destruct()
	{
		socket_close($this->socket);
	}
	
	//! Start the HTTP server
	/*!
	This method start the HTTP server on a different process using *pcntl*.
	\return TRUE if server started successfully or a string containing the error if something goes wrong.
	*/
	function start()
	{
		if(!file_exists($this->default_page)) return "The default page $default_page does not exists.";
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if(!$this->socket) return socket_strerror(socket_last_error());
		if(!socket_bind($this->socket, $this->host, $this->port)) return socket_strerror(socket_last_error());
		if(!socket_listen($this->socket, 2)) return socket_strerror(socket_last_error());
		
		$pid = pcntl_fork();
		if($pid == 0) { $this->loop(); exit(); }
		
		if($pid == -1) return "An error occured while forking processes.";
		else 
		{
			$this->pid = $pid;
			return TRUE;
		}
	}
	
	//! Stop the running server
	/*!
	\return TRUE in case of success or FALSE otherwise.
	*/
	function stop()
	{
		if(posix_kill($this->pid, SIGINT)) return TRUE;
		else return FALSE;
	}
	
	
	private function loop()
	{
		while( ($socket = $this->isRequestReceived()) != FALSE )
		{
			exec("php ".$this->default_page, $output);
			$response = $this->response_header;
			foreach($output as $val)
				$response .= $val;
			socket_write($socket, $response, strlen($response));
			socket_close($socket);
			unset($response);
			unset($output);
		}
	}
	
	private function isRequestReceived()
	{
		$socket = socket_accept($this->socket);
		if(!$socket) return FALSE;
		$request = socket_read($socket, 4096, PHP_BINARY_READ);
		return $socket;
	}
	
	private function initResponseHeader()
	{
		$this->response_header = "HTTP/1.0 200 OK\n";
		$this->response_header .= "Server: LiteDCC internal server (Unix), PHP ".PHP_VERSION."\n";
		$this->response_header .= "Content-Type: text/html; charset=utf-8\n";
		$this->response_header .= "Connection: close\n";
		$this->response_header .= "\n";
	}
	
}


?>
