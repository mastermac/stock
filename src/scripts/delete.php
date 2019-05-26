<?php

require 'db_config.php';
session_start();
$id  = $_POST["id"];

$previousData=implode("#",getCurrentData($id));
writeLog(5,	$previousData);

$mysqli=getConn();
$sql = "DELETE FROM product WHERE itemNo = '".$id."'";
$result = $mysqli->query($sql);
echo json_encode([$id]);
 
?>