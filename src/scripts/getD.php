<?php
require 'db_config.php';
session_start();
$mysqli=getConn();
$usertype = '';
if ($_SESSION['usertype'] > 1) $usertype = ' where userid=' . $_SESSION['userid'];
$sql = "SELECT * from product".$usertype." Order By dt desc, sno desc ";
//echo $sql;
$result = $mysqli->query($sql);
$json= array();
while ($row = $result->fetch_assoc())
{
   $json[] = $row;
}
$_SESSION['nD']=1;
$_SESSION['newData']=$json;
echo json_encode($json);
?>
