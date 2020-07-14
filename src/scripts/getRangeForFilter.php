<?php
require 'db_config.php';
session_start();
$mysqli=getConn();
$usertype = '';
if ($_SESSION['usertype'] >= 1) $usertype = ' where userid=' . $_SESSION['userid'];
$sql = "SELECT min(grossWt) as minWt, max(grossWt) as maxWt, min(sellPrice) as minPrice, max(sellPrice) as maxPrice, min(curStock) as minStock, max(curStock) as maxStock FROM `product`".$usertype;
//echo $sql;
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc())
{
   $json[] = $row;
   $json['type']=$_SESSION['usertype'];
}
if(!isset($json))
	$json[0]['def']="null";
$data['data'] = $json;
$data['sql']=$sql;
echo json_encode($data);
?>
