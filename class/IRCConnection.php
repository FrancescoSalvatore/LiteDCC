<?php

class IRCConnection
{
	private $socket;
	private $server;
	private $nickname;
	private $channel;
	
	private $connected;
	
	/** PUBLIC **/
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
	
	function joinChannel($channel, $entry_message = null)
	{
		$this->sendMessageToServer("JOIN $channel");
		$this->channel = $channel;
		if($entry_message != null) $this->sendChannelMessage($entry_message);
	}
	
	function sendChannelMessage($message)
	{
		$this->sendMessage($this->channel, $message);
	}
	
	function sendUserMessage($user, $message)
	{
		$this->sendMessage($user, $message);
	}
	
	function sendNotice($user, $message)
	{
		$this->sendMessageToServer("NOTICE $user :$message");
	}
	
	function sendDCCResponse($user, $filename, $host, $port, $filesize)
	{
		$this->sendUserMessage($user, $this->format_ctcp_cmd("DCC SEND $filename ". ip2long($host) ." $port $filesize"));
	}
	
	function isConnected()
	{
		if($this->connected) return true;
		else		     return false;
	}
	
	//Return an array containing data informations about the message, the sender etc...
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
	
	function pong($recipient)
	{
		$this->sendMessageToServer("PONG $recipient");
	}
	
	function quit($exit_message = "")
	{
		$this->sendMessageToServer("QUIT $exit_message");
	}
	
	
	
	
	
	/** PRIVATE **/
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
		return socket_read($this->socket, 2048, PHP_NORMAL_READ);
	}
	
	private function format_ctcp_cmd($cmd)
	{
		return chr(1) . $cmd . chr(1);
	}
}

?>
