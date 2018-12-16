<?php
session_start();
require 'db_config.php';
$errors = array();
$mysqli=getConn();
$sql="SELECT itemNo from product where itemNo like 'SDR%';";
 
$res=$mysqli->query($sql);
$num=0;
while ($row=$res->fetch_assoc()) {
	$num=substr($row['itemNo'], 3);

	if(is_numeric($num) && $num>=8466 && $num<=8494)
	{
		$mysqli1=getConn();
		$sql1="update product set itemNo='SDR-".$num."' where itemNo='".$row['itemNo']."';";
 
		$res1=$mysqli1->query($sql1);

	}
}
?>