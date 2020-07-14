<?php
require 'db_config.php';
session_start();
$mysqli=getConn();
if ($_SESSION['usertype'] == 1) return;
$sql="";
if(trim($_GET['vid'])!="")
$sql="SELECT * FROM vendor WHERE vendor.vid='".$_GET['vid']."';";
else
$sql = "SELECT vendor.*, count(*) as tot from vendor LEFT JOIN product ON product.vendor=vendor.code group by vendor.code ORDER by code asc;";
$data['sql']=$sql;
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc())
	$json[]=$row;
if(!isset($json))
	$json[0]['def']="null";
$data['data'] = $json;
$data['sql'] = $sql;
echo json_encode($data);
?>
