<?php
require 'db_config.php';
session_start();

function checkUserType(){
    if ($_SESSION['usertype'] > 1) return ' WHERE userid=' . $_SESSION['userid'];
    return '';
}

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
    $sql = "SELECT * from packinglist ".checkUserType()." order by id desc ";
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
        // $json[$sno]['diamond']=getItemDiamonds($_GET['id'],$row['id']);
        // $json[$sno]['stone']=getItemStones($_GET['id'],$row['id']);
        // $json[$sno]['metal']=getItemMetals($_GET['id'],$row['id']);
        // $json[$sno]['others']=getItemOthers($_GET['id'],$row['id']);
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    returnData($json,$sql,$result);
}

function getPackingListItemById(){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-items` where pid=".$_GET['id']." and id=".$_GET['itemId']." order by id asc LIMIT 1";
    $result = $mysqli->query($sql);
    $json;
    while ($row = $result->fetch_assoc()){
        $json = $row;
        $json['diamond']=getItemDiamonds($_GET['id'],$row['id']);
        $json['stone']=getItemStones($_GET['id'],$row['id']);
        $json['metal']=getItemMetals($_GET['id'],$row['id']);
        $json['others']=getItemOthers($_GET['id'],$row['id']);
    }
    returnData($json,$sql,$result);
}


function getItemDiamonds($pid, $id){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-diamond` where pl_id=".$pid." AND item_id=".$id." order by id asc ";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}
function getItemStones($pid, $id){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-stone` where pl_id=".$pid." AND item_id=".$id." order by id asc ";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}
function getItemMetals($pid, $id){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-metal` where pl_id=".$pid." AND item_id=".$id." order by id asc ";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}
function getItemOthers($pid, $id){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-others` where pl_id=".$pid." AND item_id=".$id." order by id asc ";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}

function getStoneById(){
    $mysqli = getConn();
    $sql = "SELECT * from `stone-inventory` where lot_no=".$_GET['lotId'];
    $result = $mysqli->query($sql);
    $json;
    while ($row = $result->fetch_assoc())
        $json = $row;
    returnData($json,$sql,$result);
}

function getPackingListCounts(){
    $mysqli = getConn();
    $sql = "SELECT SUM(wt) as total FROM `pl-diamond` where pl_id in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc())
        $json['dia_total'] = (float)$row['total'];

    $sql = "SELECT SUM(wt) as total FROM `pl-metal` where pl_id in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json['metal_total'] = (float)$row['total'];

    $sql = "SELECT SUM(wt) as total FROM `pl-stone` where pl_id in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json['stone_total'] = (float)$row['total'];

    $sql = "SELECT SUM(total) as total FROM `pl-items` where pid in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json['item_total'] = (float)$row['total'];

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
    case "getPackingListCounts": getPackingListCounts();
        break;
    case "getPackingListItems": getPackingListItems();
        break;
    case "getPackingListItemById": getPackingListItemById();
        break;
    case "getStoneById": getStoneById();
        break;
}

?>