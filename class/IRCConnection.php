<?php

//! IRCConnection Class
/*!
This is the class that gives methods to handle IRC connection. You can send and receive messages to/from users in IRC and manage the IRC in general.

This class works on BSD Sockets with sockets library of PHP - http://php.net/manual/en/book.sockets.php

The protocol used is the original IRC protocol [RFC 1419](http://tools.ietf.org/html/rfc1459), following [RFC 2812](http://tools.ietf.org/html/rfc2812) and the unofficial CTCP and DCC extensions introduced by ircII client [DCC](http://www.irchelp.org/irchelp/rfc/dccspec.html)

\author Francesco Salvatore
\copyright Apache License 2.0
*/
class IRCConnection
{
	private $socket;
	private $server;
	private $nickname;
	private $channel;
	
	private $connected;
	
	//! Connect to an IRC server
	/*!
	\param $server String, is the hostname of IRC server
	\param $port Integer, is the port of IRC server
	\param $nickname String, is the nickname of bot
	\param $password String, optional, is the password required to login into the IRC net
	\return TRUE on success and a string containing the socket error in case of failure
	*/
	function connect($server, $port, $nickname, $password = "")
	{
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if(!socket_connect($this->socket, $server, $port)) return socket_last_error();
		$this->nickname = $nickname;
		$this->server = $server;
		$this->setNickname($server, $nickname, $password);
		$this->connected = true;
		return true;
	}
	
	//! Join in an IRC channel
	/*!
	\param $channel String, is the channel of IRC server that you want to enter
	\param $entry_message String, optional, is a message wrote in the channel after joining (a welcome message)
	*/
	function joinChannel($channel, $entry_message = null)
	{
		$this->sendMessageToServer("JOIN $channel");
		$this->channel = $channel;
		if($entry_message != null) $this->sendChannelMessage($entry_message);
	}
	
	//! Send a message into the channel chat
	/*!
	\param $message String, the message you want to send
	*/
	function sendChannelMessage($message)
	{
		$this->sendMessage($this->channel, $message);
	}
	
	//! Send a message to a specific user
	/*!
	\param $user String, the user you want to send the message
	\param $message String, the message
	> **Warning:** you cannot send a message that contains EOF characters (end of line, \\n) because it will be truncate at this character (this is a limitation of IRC protocol). If you want to send this type of message split it into multiple sends.
	*/
	function sendUserMessage($user, $message)
	{
		$this->sendMessage($user, $message);
	}
	
	//! Send a notice to a specific user
	/*!
	\param $user String, the user you want to send the notice
	\param $message String, the message
	> **Warning:** you cannot send a message that contains EOF characters (end of line, \\n) because it will be truncate at this character (this is a limitation of IRC protocol). If you want to send this type of message split it into multiple sends.
	*/
	function sendNotice($user, $message)
	{
		$this->sendMessageToServer("NOTICE $user :$message");
	}
	
	//! Send a DCC response to a user
	/*!
	\param $user String, the user you want to send the DCC response
	\param $filename String, the name of requested file
	\param $host String, the host address
	> Note: You can use IPv6 address only if both requesting user system and your system support IPv6. For that, use of IPv4 is prefered.
	\param $port Integer, the port on requesting user will connect to
	\param $filesize Integer, the filesize of requested file
	> Note: DCC original protocol value used for filesize is a 32 bit unsigned integer, so the maximum filesize allowed is 4GB.
	*/
	function sendDCCResponse($user, $filename, $host, $port, $filesize)
	{
		$this->sendUserMessage($user, $this->format_ctcp_cmd("DCC SEND $filename ". ip2long($host) ." $port $filesize"));
	}
	
	//! Send a DCC accept after a RESUME request
	/*!
	\param $user String, the user you want to send the DCC accept
	\param $filename String, the name of requested file
	\param $port Integer, the port on requesting user will connect to
	\param $position Integer, the position that you have to move the file pointer for resuming an interrupted transfer
	*/
	function sendDCCAccept($user, $filename, $port, $position)
	{
		$this->sendUserMessage($user, $this->format_ctcp_cmd("DCC ACCEPT $filename $port $position"));
	}
	
	//! Get the status of IRC connection
	/*!
	> **Warning:** the internal status of connection is based on a periodic check, so it can be no really accurate. Use with caution.
	*/
	function isConnected()
	{
		if($this->connected) return true;
		else		     return false;
	}
	
	//! Reads available data on connection buffer
	/*!
	> Note: default operating mode is blocking. If you want to change the operating mode see the relative methods setBlockingSocket()  and setNonBlockingSocket().
	\return An array in the form key=>value containing informations about the read data.

	~~~~~~~~~~~~~{.php}
	$data = $IRC->getData();
	echo $data["type"]; //PING - CHANMSG - USERMSG
	echo $data["sender"]; //the username of the sender
	echo $data["content"]; //Data content
	~~~~~~~~~~~~~
	     
	*/
	function getData()
	{
		$resp = $this->readDataFromServer();
		if($resp === "") { $this->connected = false; }
		$data = Array();
		$parts_from_spaces = explode(" ", $resp);
		$parts_from_colon = explode(":", $resp);
		
		if($parts_from_spaces[0]=="PING")
		{
			$data["type"] = "PING";
			$data["sender"] = rtrim( substr($parts_from_spaces[1], 1) );
			return $data;
		}
		elseif($parts_from_spaces[1]=="PRIVMSG" && $parts_from_spaces[2]==$this->channel)
		{
			$data["type"] = "CHANMSG";
			$data["content"] = $parts_from_colon[2];
			$data["sender"] = $this->getUserFromPrivMsg($resp);
			return $data;
		}
		elseif($parts_from_spaces[1]=="PRIVMSG" && $parts_from_spaces[2]!=$this->channel)
		{
			$data["type"] = "USERMSG";
			$data["content"] = $parts_from_colon[2];
			$data["sender"] = $this->getUserFromPrivMsg($resp);
			return $data;
		}
		else return null;
		
		return $data;
	}
	
	//! Check if a resume is requested for transfer
	/*!
	\return An integer containing the file pointer position for resuming or FALSE if no resume is found.
	*/
	function isResumeRequested()
	{
		$resp = $this->readDataFromServer();
		if(trim($resp) == "")
		{
			return FALSE;
		}
		else
		{
			$parts_from_spaces = explode(" ", $resp);
			if(trim($parts_from_spaces[4]) == "RESUME")
			{
				$resume_position = trim($parts_from_spaces[7], " \x01\r\n");
			}
			else
			{
				return FALSE;
			}
		}
		return $resume_position;
	}
	
	//! Change the socket operating mode in blocking
	function setBlockingSocket()
	{
		socket_set_block($this->socket);
	}
	
	//! Change the socket operating mode in non-blocking
	function setNonBlockingSocket()
	{
		socket_set_nonblock($this->socket);
	}
	
	//! Answer to a PING request
	/*!
	\param $recipient String, the hostname that send the PING
	*/
	function pong($recipient)
	{
		$this->sendMessageToServer("PONG $recipient");
	}
	
	//! Quit connection
	/*!
	\param $exit_message String, optional, an exit message
	*/
	function quit($exit_message = "")
	{
		$this->sendMessageToServer("QUIT $exit_message");
	}
	
	
	
	
	
	/*** PRIVATE ***/
	
	private function setNickname($server, $nickname, $password = "")
	{
		$this->sendMessageToServer("PASS $password");
		$this->sendMessageToServer("NICK $nickname");
		$this->sendMessageToServer("USER $nickname ".$server." bla :Mario Rossi");
		$this->nickname = $nickname;
	}
	
	private function getUserFromPrivMsg($string)
	{
		return substr( $string, 1, (strpos($string, "!")-1) );
	}
	
	private function sendMessageToServer($message)
	{
		$message .= PHP_EOL;
		if(socket_write($this->socket, $message, strlen($message)) === FALSE)
			return false;
		else    return true;
	}
	
	private function sendMessage($recipient, $message)
	{
		$this->sendMessageToServer("PRIVMSG $recipient :$message");
	}
	
	private function readDataFromServer()
	{
		return socket_read($this->socket, 2048, PHP_BINARY_READ);
	}
	
	private function format_ctcp_cmd($cmd)
	{
		return chr(1) . $cmd . chr(1);
	}
}

?>
