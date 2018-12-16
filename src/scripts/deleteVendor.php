<?php

require 'db_config.php';
session_start();
$id  = $_POST["id"];
$sql = "SELECT * FROM vendor WHERE vid = '".$id."'";
$mysqli=getConn();
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc())
{
   writeLog(5,	implode(',', $row));
}
$sql = "DELETE FROM vendor WHERE vid = '".$id."'";
$result = $mysqli->query($sql);
echo json_encode([$id]);
 
?>