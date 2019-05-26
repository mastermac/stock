<?php

require 'db_config.php';
session_start();
$id  = $_POST["id"];

$previousData=implode("#",getCurrentData($data[2]));
writeLog(5,	$previousData);

$mysqli=getConn();
$sql = "DELETE FROM vendor WHERE vid = '".$id."'";
$result = $mysqli->query($sql);
echo json_encode([$id]);
 
?>