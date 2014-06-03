<?php
include_once("config.php");
include_once("version.php");
include_once("class/ListManager.php");
include_once("class/DCCListManager.php");
$LISTMANAGER = new ListManager(DB_FILE);
$list = $LISTMANAGER->getList();
$TRANSFERMANAGER = new DCCListManager(TRANSFERS_FILE);
$transfers = $TRANSFERMANAGER->getAllTransfersData();

function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

?>
<html>

<head>
	<title><?php echo IRC_NICKNAME; ?> | LiteDCC version <?php echo VERSION; ?></title>
	<meta http-equiv="cache-control" content="no-cache" />
</head>

<body>
	<center>
		<h1>LiteDCC v<?php echo VERSION; ?></h1>
		<table>
		
		<tr>
		<td><b>Nickname:</b></td>
		<td><?php echo IRC_NICKNAME; ?></td>
		</tr>
		
		<tr>
		<td><b>Server:</b></td>
		<td><?php echo IRC_SERVER; ?></td>
		</tr>
		
		<tr>
		<td><b>Channel:</b></td>
		<td><?php echo IRC_CHANNEL; ?></td>
		</tr>
		
		</table>
		
		<br /><br />
		
		<h3><?php echo count($transfers); ?> active transfers</h3>
		<table border=1>
		<tr>
		<td><strong>ID</strong></td>
		<td><strong>User</strong></td>
		<td><strong>Package Number</strong></td>
		<td><strong>Package Filename</strong></td>
		<td><strong>Package Filesize</strong></td>
		<td><strong>Completed</strong></td>
		<td><strong>Started at</strong></td>
		</tr>
		
		<?php
		foreach($transfers as $key => $value)
		{
			echo "<tr>";
			echo "<td>".$key."</td>";
			echo "<td>".$value["recipient"]."</td>";
			echo "<td>".$value["package"]."</td>";
			echo "<td>".$LISTMANAGER->getFileName($value["package"])."</td>";
			echo "<td>".human_filesize($LISTMANAGER->getFileSize($value["package"]))."</td>";
			echo "<td>".( ($value["byte_sent"]*100) / $LISTMANAGER->getFileSize($value["package"]) )."%</td>";
			echo "<td>".$value["timestamp"]."</td>";
			echo "</tr>";
		}
		?>
		
		</table>
		
		<br /><br />
		
		<h3><?php echo count($list); ?> files in list</h3>
		<table border=1>
		<tr>
		<td><strong>ID</strong></td>
		<td><strong>Filename</strong></td>
		<td><strong>Filesize</strong></td>
		<td><strong>Add date</strong></td>
		<td><strong>MD5 checksum</strong></td>
		<td><strong>Taken</strong></td>
		</tr>
		<?php
		foreach($list as $key => $value)
		{
			echo "<tr>";
			echo "<td>".$key."</td>";
			echo "<td>".$value["filename"]."</td>";
			echo "<td>".human_filesize($value["filesize"])."</td>";
			echo "<td>".$value["add_date"]."</td>";
			echo "<td>".$value["md5"]."</td>";
			echo "<td>".$value["taken"]."</td>";
			echo "</tr>";
		}
		?>
		
		</table>
	</center>
</body>

</html>
