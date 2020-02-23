<?php
require 'db_config.php';
$mysqli=getConn();
$usertype = '';
$tablename=$_GET['table'];
$columns='*';
if ($tablename==null || $tablename=='') $tablename = 'product';
if($_GET['key']!='mastermac')
    return;
if($_GET['columns']!="")
    $columns=$_GET['columns'];
$sql = "SELECT ".$columns." from ".$tablename;
// echo $sql;
// return;
$result = $mysqli->query($sql);
$json= array();
// echo $result;
// return;
while ($row = $result->fetch_assoc())
{
   $json[] = $row;
}
$_SESSION['data']=$json;
echo json_encode($json);
return;
?>
