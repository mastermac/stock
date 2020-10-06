<?php
require 'db_config.php';
session_start();

function getSettings()
{
    $mysqli = getConn();
    $sql = "SELECT * from settings LIMIT 1";
    $result = $mysqli->query($sql);
    $json = array();

    while ($row = $result->fetch_assoc())
        $json[] = $row;

    returnData($json,$sql,$result);
}

function getPackingLists(){
    $mysqli = getConn();
    $sql = "SELECT * from packinglist order by id desc ";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }

    returnData($json,$sql,$result);
}

function getPackingListItems(){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-items` where pid=".$_GET['id']." order by id asc ";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }

    returnData($json,$sql,$result);
}

function returnData($json,$sql,$result){
    $data['data'] = $json;
    $data['sql'] = $sql;
    $data['total'] = mysqli_num_rows($result);
    echo json_encode($data);
}

switch($_GET["func"]){
    case "getSettings": getSettings();
        break;
    case "getPackingLists": getPackingLists();
        break;
    case "getPackingListItems": getPackingListItems();
        break;
}

?>