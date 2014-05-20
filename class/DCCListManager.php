<?php

class DCCListManager
{

	private $db;

	function __construct($db_file)
	{
		$this->db = new SQLite3($db_file);
	}
	
	function __destruct()
	{
		$this->db->close();
	}
	
	
	function createNewTransfer($recipient, $package_number, $byte_sent = 0)
	{
		if($this->db->exec("INSERT INTO transfers (recipient, package, byte_sent, timestamp) VALUES (
		'$recipient',
		'$package_number',
		$byte_sent,
		'".time()."'
		);"))
			return $this->db->lastInsertRowID();
		else	return FALSE;
	}
	
	function removeTransfer($id)
	{
		if($this->db->exec("DELETE FROM transfers WHERE rowid = $id;"))
			return TRUE;
		else	return FALSE;
	}
	
	function removeUserTransfers($recipient)
	{
		if($this->db->exec("DELETE FROM transfers WHERE recipient = '$recipient';"))
			return TRUE;
		else	return FALSE;
	}
	
	function removeAllTransfers()
	{
		if($this->db->exec("DELETE FROM transfers;"))
			return TRUE;
		else	return FALSE;
	}
	
	function updateSentData($id, $byte_sent)
	{
		if($this->db->exec("UPDATE transfers SET byte_sent = $byte_sent WHERE rowid = $id;"))
			return TRUE;
		else	return FALSE;
	}
	
	function getAllTransfersData()
	{
		$list = Array();
		$result = $this->db->query("SELECT rowid,* FROM transfers");
		while($row = $result->fetchArray())
		{
			$list[$row['rowid']]["recipient"] = $row["recipient"];
			$list[$row['rowid']]["package"] = $row["package"];
			$list[$row['rowid']]["byte_sent"] = $row["byte_sent"];
			$list[$row['rowid']]["timestamp"] = $row["timestamp"];
		}
		return $list;
	}
	
	function getTransferData($id)
	{
		return $this->db->querySingle("SELECT rowid,* FROM transfers WHERE rowid = $id", TRUE);
	}
	
	function isTransferAlive($id)
	{
		return $this->db->querySingle("SELECT EXISTS(SELECT * FROM transfers WHERE rowid = $id);");
	}
}


?>
