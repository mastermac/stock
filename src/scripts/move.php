<?php
session_start();
require 'db_config.php';
$errors = array();
$mysqli=getConn();
$sql="SELECT itemNo from product;";
 
$res=$mysqli->query($sql);
while ($row=$res->fetch_assoc()) {
    if(file_exists('pics1/'.$row['itemNo'].'.JPG'))
        continue;
    else if(file_exists('p1/'.$row['itemNo'].'.JPG'))
        copy('p1/'.$row['itemNo'].'.JPG', 'p4/'.$row['itemNo'].'.JPG');
    else if(file_exists('p2/'.$row['itemNo'].'.JPG'))
        copy('p2/'.$row['itemNo'].'.JPG', 'p5/'.$row['itemNo'].'.JPG');
    else
        echo $row['itemNo']."<br/>";
}
?>