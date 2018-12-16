<?php
require 'db_config.php';
session_start();
$mysqli=getConn();
$q1 = "select stylecode from itemtype where concat( purity,' ', category,' ',iType)='" . $_POST['itemtype']."';";
$result = $mysqli->query($q1);
while ($row = $result->fetch_assoc())
{
  echo $row['stylecode'];
}
?>