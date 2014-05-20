<?php

class ListManager
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
	
	function addFile($filename, $filesize, $md5sum)
	{
		if($this->db->exec("INSERT INTO list (filename, filesize, add_date, md5, taken) 
		VALUES ('$filename', $filesize, '".date("Y-m-d H:i")."', '$md5sum', 0);"))
			return true;
		else	return false;
	}
	
	function removeFile($id)
	{
		if($this->db->exec("DELETE FROM list WHERE rowid = $id"))
			return true;
		else	return false;
	}
	
	function getFileInfo($id)
	{
		return $this->db->querySingle("SELECT rowid,* FROM list WHERE rowid = $id", TRUE);
	}
	
	function getPackageNumberByName($filename)
	{
		$pkgnum = $this->db->querySingle("SELECT rowid FROM list WHERE filename = '$filename';");
		if($pkgnum !== FALSE && $pkgnum !== NULL)
			return $pkgnum;
		else	return FALSE;
	}
	
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
	
	function incrementTaken($id)
	{
		$info = $this->getFileInfo($id);
		$this->db->exec("UPDATE list SET taken = ".++$info['taken']." WHERE rowid = $id");
	}
	
}
?>
