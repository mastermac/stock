<?php
require 'db_config.php';

session_start();

function deletePackingList(){
    $mysqli = getConn();
    $sql = "DELETE from packinglist where id=".$_GET['id'];
    $result = $mysqli->query($sql);
    returnData($sql,$result);
}

function deletePLItem(){
    $mysqli = getConn();
    $sql = "DELETE from `pl-items` where id=".$_GET['id'];
    $result = $mysqli->query($sql);
    $sql = "DELETE from `pl-diamond` where item_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    $sql = "DELETE from `pl-stone` where item_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    $sql = "DELETE from `pl-metal` where item_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    $sql = "DELETE from `pl-others` where item_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    returnData($sql,$result);
}

function returnData($sql,$result){
    $data['sql'] = $sql;
    $data['result'] = $result;
    echo json_encode($data);
}

switch($_GET["func"]){
    case "deletePackingList": deletePackingList();
        break;
    case "deletePLItem": deletePLItem();
        break;
}

?>