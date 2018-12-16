<?php
require 'db_config.php';
session_start();
$mysqli=getConn();
$srch="";
$sql = "SELECT * from vendor order by vid asc;";
$result = $mysqli->query($sql);
$data="";
while ($row = $result->fetch_assoc())
{
	$sqlTotal = "Update product set userid='".$row["vid"]."' where vendor='".$row["code"]."';";
	$mysqli1=getConn();
   	$result1 = $mysqli1->query($sqlTotal);
}
?>
