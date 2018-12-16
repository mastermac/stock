<?php
require 'db_config.php';
session_start();
$mysqli=getConn();
if ($_SESSION['usertype'] == 1) return;
$vcon="";
if(trim($_GET['vid'])!="")
$vcon=" and vendor.vid='".$_GET['vid']."' ";
$sql = "SELECT vendor.*, count(*) as tot from product,vendor WHERE product.vendor=vendor.code".$vcon." group by product.vendor ORDER by code asc;";
$data['sql']=$sql;
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc())
	$json[]=$row;
if(!isset($json))
	$json[0]['def']="null";
$data['data'] = $json;
echo json_encode($data);
?>
