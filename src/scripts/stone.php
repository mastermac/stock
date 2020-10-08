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

function getStoneLists(){
    $mysqli = getConn();
    $sql = "SELECT * from `stone-inventory` order by id desc";
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


function addStone()
{
    $mysqli = getConn();

    $less=((float)clean(trim($_GET['less'])));
    if($less>=1)    $less = $less/100;
    $rate = ( (float)clean(trim($_GET['cost'])) * (1-$less) );
    $datetime = date('Y/m/d H:i:s');

    $sql = "INSERT INTO `stone-inventory` VALUES (null,'" . trim($_GET['lot_no']) . "','" . trim($_GET['name']) . "','" .
    trim($_GET['size']) . "', '" . $_GET['shape'] . "','" . trim($_GET['seller']) . "','" .
    clean(trim($_GET['purchased_qty'])) . "','" . clean(trim($_GET['purchased_wt'])) . "','" . clean(trim($_GET['current_qty'])) . "','" .
    clean(trim($_GET['current_wt'])) . "', '" . trim($_GET['unit']) . "','" . trim($_GET['box']) . "','" .
    clean(trim($_GET['cost'])) . "', '" . $less . "','" . $rate . "','" .
    round($rate * (float)clean(trim($_GET['purchased_wt'])),2) . "', '" . round($rate * clean(trim($_GET['current_wt'])),0) . "','" . trim($_GET['description']) . "','" .
    $datetime . "', '" . $datetime . "','" . $_SESSION['userid'] . "') ON DUPLICATE KEY UPDATE lot_no='" .
    trim($_GET['lot_no']) . "', name='" . trim($_GET['name']) . 
    "', size='" . $_GET['size'] . "', shape='" . trim($_GET['shape']) . "', seller='" . trim($_GET['seller']) . 
    "',purchased_qty='" . clean($_GET['purchased_qty']) . "',purchased_wt='" . clean($_GET['purchased_wt']) . "',current_qty='" . clean($_GET['current_qty']) . "',current_wt='" . clean($_GET['current_wt']) . 
    "',unit='" . trim($_GET['unit']) . "',box='" . trim($_GET['box']) . "',cost='" . clean($_GET['cost']) . "',less='" . $less . 
    "', rate='" . $rate . "', total_amount='" . round($rate * (float)clean(trim($_GET['purchased_wt'])),2) . "', current_value='" . round($rate * clean(trim($_GET['current_wt'])),0) . "', description='" . 
    $_GET['description'] . "', last_update_date='" . $datetime . "', userid='" . $_SESSION['userid'] . "' ;";

    $result = $mysqli->query($sql);
    $data['sql'] = $sql;
    echo json_encode($data);
}

function deleteStone(){
    $mysqli = getConn();
    $sql="DELETE from `stone-inventory` where id='".$_GET['id']."';";
    $result = $mysqli->query($sql);

    $data['sql'] = $sql;
    echo json_encode($data);
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
    case "getStoneLists": getStoneLists();
        break;
    case "addStone": addStone();
        break;
    case "deleteStone": deleteStone();
        break;
}

?>