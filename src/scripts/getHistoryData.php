<?php
require 'db_config.php';
session_start();

$mysqli=getConn();
$usertype = '';

if ($_SESSION['usertype'] >= 1) $usertype = ' and userid=' . $_SESSION['userid'];

$sql = "SELECT * FROM producthistory where itemNo='".$_GET["itemNo"]."' ". $usertype . " Order By timestamp desc";
//echo $sql;
$result = $mysqli->query($sql);
$count=0;
while ($row = $result->fetch_assoc())
{
    $count++;
   $json[] = $row;
   $json['type']=$_SESSION['usertype'];
}
if(!isset($json))
	$json[0]['def']="null";
$data['data'] = $json;
$data['total'] = $count;
$data['sql']=$sql;
echo json_encode($data);
?>
