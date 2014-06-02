<?php

//! DCCListManager Class
/*!
This class manage the DCC transfers list database.

This class works on SQLite DBMS with SQLite3 library of PHP - http://php.net/manual/en/book.sqlite3.php

\author Francesco Salvatore
\copyright Apache License 2.0
*/
class DCCListManager
{

	private $db;
	
	//! Contructor
	/*!
	\param $db_file String, is the path of SQLite file that contains the database.
	*/
	function __construct($db_file)
	{
		$this->db = new SQLite3($db_file);
	}
	
	function __destruct()
	{
		$this->db->close();
	}
	
	//! Add a new transfer to the list
	/*!
	\param $recipient String, is the user to send the file.
	\param $package_number Integer, is the file ID of file list.
	\param $byte_sent Integer, is the amount of bytes sent.
	\return The ID of transfer inserted or FALSE on failure.
	*/
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
	
	//! Remove a transfer from the list
	/*!
	\param $id Integer, is the transfer ID.
	\return TRUE if removing succeeded or FALSE on failure.
	*/
	function removeTransfer($id)
	{
		if($this->db->exec("DELETE FROM transfers WHERE rowid = $id;"))
			return TRUE;
		else	return FALSE;
	}
	
	//! Remove all transfers of a user
	/*!
	\param $recipient String, the user.
	\return TRUE if removing succeeded or FALSE on failure.
	*/
	function removeUserTransfers($recipient)
	{
		if($this->db->exec("DELETE FROM transfers WHERE recipient = '$recipient';"))
			return TRUE;
		else	return FALSE;
	}
	
	//! Remove all transfers from database
	/*!
	\return TRUE if removing succeeded or FALSE on failure.
	*/
	function removeAllTransfers()
	{
		if($this->db->exec("DELETE FROM transfers;"))
			return TRUE;
		else	return FALSE;
	}
	
	//! Alias of removeAllTransfers()
	function clearDB()
	{
		return $this->removeAllTransfers();
	}
	
	//! Update the sent data column in database
	/*!
	\param $id Integer, the ID of transfer.
	\param $byte_sent Integer, the byte sent.
	\return TRUE if updating succeeded or FALSE on failure.
	*/
	function updateSentData($id, $byte_sent)
	{
		if($this->db->exec("UPDATE transfers SET byte_sent = $byte_sent WHERE rowid = $id;"))
			return TRUE;
		else	return FALSE;
	}
	
	//! Get data about all transfers
	/*!
	\return A bidimensional array containing data about active transfers, in the form key=>column=>value, where the key is the ID of transfer into the list, column is the column name and the value is respective value into the table
	*/
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
	
	//! Get data about a specific transfer
	/*!
	\param $id Integer, the ID of transfer.
	\return An array containing data of transfer in form of key=>value, where the key is the column name and the value is corresponding value in table. If no one result is found the returned array is empty.
	*/
	function getTransferData($id)
	{
		return $this->db->querySingle("SELECT rowid,* FROM transfers WHERE rowid = $id", TRUE);
	}
	
	//! Get the number of active transfers into the list
	/*!
	\return An integer containing the number of active transfers.
	*/
	function getActiveTransfersNumber()
	{
		return $this->db->querySingle("SELECT COUNT(*) FROM transfers;");
	}
	
	//! Check if a transfer is alive (active) or not
	/*!
	\param $id Integer, the ID of transfer.
	\return TRUE if checked transfer is active or FALSE if it is not active (not present in the list).
	*/
	function isTransferAlive($id)
	{
		return $this->db->querySingle("SELECT EXISTS(SELECT * FROM transfers WHERE rowid = $id);");
	}
}


?>
