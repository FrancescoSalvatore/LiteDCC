<?php

//! ListManager Class
/*!
This is the class that gives methods to handle the file list. You can add file into the list or removing it, or get file info.

This class works on SQLite DBMS with SQLite3 library of PHP - http://php.net/manual/en/book.sqlite3.php

\author Francesco Salvatore
\copyright Apache License 2.0
*/
class ListManager
{

	private $db;
	
	//! Constructor
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
	
	//! A method that allow to add file into the list
	/*!
	\param $filename String, is the real filename of file (without path)
	\param $filesize Integer, is the byte filesize of file
	\param $md5sum String, is the MD5 hash of your file
	\return TRUE if adding succeeded, FALSE on failure
	*/
	function addFile($filename, $filesize, $md5sum)
	{
		if($this->db->exec("INSERT INTO list (filename, filesize, add_date, md5, taken) 
		VALUES ('$filename', $filesize, '".date("Y-m-d H:i")."', '$md5sum', 0);"))
			return true;
		else	return false;
	}
	
	//! A method that allow to remove file from the list
	/*!
	\param $id Integer, is the ID of file in the list
	\return TRUE if removing succeeded, FALSE on failure
	*/
	function removeFile($id)
	{
		if($this->db->exec("DELETE FROM list WHERE rowid = $id"))
			return true;
		else	return false;
	}
	
	//! A method that allow to get the informations about a file into the list
	/*!
	\param $id Integer, is the ID of file in the list
	\return An array containing file informations in format key => value, where the key is the column name and the value is respective value into the table.
	If the ID was not found, the returned array is empty.
	*/
	function getFileInfo($id)
	{
		return $this->db->querySingle("SELECT rowid,* FROM list WHERE rowid = $id", TRUE);
	}
	
	//! A method that allow to get the filename of a file from its ID
	/*!
	\param $id Integer, is the ID of file in the list
	\return A string containing the name of file. If the ID was not found, the returned string is empty.
	*/
	function getFileName($id)
	{
		return $this->db->querySingle("SELECT filename FROM list WHERE rowid = $id");
	}
	
	//! A method that allow to get the filesize of a file from its ID
	/*!
	\param $id Integer, is the ID of file in the list
	\return An integer containing the filesize. If the ID was not found, the returned value is an empty string.
	*/
	function getFileSize($id)
	{
		return $this->db->querySingle("SELECT filesize FROM list WHERE rowid = $id");
	}
	
	//! A method that allow to get the package number (ID) of a file from its filename
	/*!
	\param $filename String, is the name of file in the list
	\return An integer containing the package number (ID). If the filename was not found, the function returns FALSE.
	*/
	function getPackageNumberByName($filename)
	{
		$pkgnum = $this->db->querySingle("SELECT rowid FROM list WHERE filename = '$filename';");
		if($pkgnum !== FALSE && $pkgnum !== NULL)
			return $pkgnum;
		else	return FALSE;
	}
	
	//! A method that allow to get the entire file list
	/*!
	\return A double-dimension array containing the entire file list in the form key=>column=>value, where the key is the ID of file into the list, column is the column name and the value is respective value into the table
	*/
	function getList()
	{
		$list = Array();
		$result = $this->db->query("SELECT rowid,* FROM list");
		while($row = $result->fetchArray())
		{
			$list[$row['rowid']]["filename"] = $row["filename"];
			$list[$row['rowid']]["filesize"] = $row["filesize"];
			$list[$row['rowid']]["add_date"] = $row["add_date"];
			$list[$row['rowid']]["md5"] = $row["md5"];
			$list[$row['rowid']]["taken"] = $row["taken"];
		}
		return $list;
	}
	
	//! A method that allow to search a file into the list from a keyword
	/*!
	\param $keyword String, is the searched value.
	> Note: the keyword will be searched only into filename.
	\return A double-dimension array containing the entire file list in the form key=>column=>value, where the key is the ID of file into the list, column is the column name and the value is respective value into the table.
	If there aren't occurences the returned array is empty.
	*/
	function search($keyword)
	{
		$list = Array();
		$result = $this->db->query("SELECT rowid,* FROM list WHERE filename LIKE '%$keyword%';");
		while($row = $result->fetchArray())
		{
			$list[$row['rowid']]["filename"] = $row["filename"];
			$list[$row['rowid']]["filesize"] = $row["filesize"];
			$list[$row['rowid']]["add_date"] = $row["add_date"];
			$list[$row['rowid']]["md5"] = $row["md5"];
			$list[$row['rowid']]["taken"] = $row["taken"];
		}
		return $list;
	}
	
	//! A method that allow to increment the "taken" value in the database
	/*!
	\param $id Integer, is the ID of file in the list
	*/
	function incrementTaken($id)
	{
		$info = $this->getFileInfo($id);
		$this->db->exec("UPDATE list SET taken = ".++$info['taken']." WHERE rowid = $id");
	}
	
}
?>
