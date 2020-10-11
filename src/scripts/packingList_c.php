<?php
require 'db_config.php';
session_start();

function createPackingList()
{
    $mysqli = getConn();
    $sql = "SELECT * FROM settings LIMIT 1";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()){
        $mysqli1 = getConn();
        $stmt = $mysqli1->prepare("INSERT INTO packinglist VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $_GET['date'], $_GET['name'], $row['exchangeRt'], $row['silverRt'], $row['goldRt'], $row['labourRt'], $row['platingRt'], $row['findingsRt'], $_SESSION['userid'], $row['microDiaSettingRt'], $row['prongDiaSettingRt'], $row['baguetteDiaSettingRt'], $row['roundStoneSettingRt'], $row['goldLabourRt']);
        $stmt->execute();
        $stmt->close();
        $mysqli1->close();
    }
    $data['sql'] = $sql;
    echo json_encode($data);
}

function createPLItem(){
    
    $mysqli = getConn();
    $stmt = $mysqli->prepare("INSERT INTO `pl-items` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississss", $_GET['pid'], $_GET['itemcode'], $_GET['mewarcode'], $_GET['qty'], $_GET['ringsize'], $_GET['metaltype'], $_GET['metalcolor'], $_GET['description']);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();

    $mysqli1 = getConn();
    $sql = "SELECT id from `pl-items` WHERE pid='".$_GET['pid']."' AND itemcode='".$_GET['itemcode']."' AND mewarcode='".$_GET['mewarcode']."' AND qty='".$_GET['qty']."' AND metaltype='".$_GET['metaltype']."' AND metalcolor='".$_GET['metalcolor']."' AND description='".$_GET['description']."' ORDER BY id DESC LIMIT 1";
    $data['sql']=$sql;
    $result = $mysqli1->query($sql);
    $itemId='';
    while ($row = $result->fetch_assoc()){
        $itemId = $row['id'];
        $data['itemId']=$itemId;
    }
    $data['metals']=count($_GET['metals']);
    
    for($i=0;$i<count($_GET['metals']);$i++){
        $mysqli2 = getConn();
        $stmt2 = $mysqli2->prepare("INSERT INTO `pl-metal` VALUES (null, ?, ?, ?, ?)");
        $stmt2->bind_param("iiss", $_GET['pid'], $itemId, $_GET['metals'][$i]['metal_wt'], $_GET['metals'][$i]['metal_amt']);
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($_GET['diamonds']);$i++){
        $mysqli2 = getConn();
        $stmt2 = $mysqli2->prepare("INSERT INTO `pl-diamond` VALUES (null, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiiisss", $_GET['pid'], $itemId, $_GET['diamonds'][$i]['dia_lot_id'], $_GET['diamonds'][$i]['dia_qty'], $_GET['diamonds'][$i]['dia_wt'], $_GET['diamonds'][$i]['dia_rate'], $_GET['diamonds'][$i]['dia_amt']);
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($_GET['stones']);$i++){
        $mysqli2 = getConn();
        $stmt2 = $mysqli2->prepare("INSERT INTO `pl-stone` VALUES (null, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiiisss", $_GET['pid'], $itemId, $_GET['stones'][$i]['stone_lot_id'], $_GET['stones'][$i]['stone_qty'], $_GET['stones'][$i]['stone_wt'], $_GET['stones'][$i]['stone_rate'], $_GET['stones'][$i]['stone_amt']);
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($_GET['others']);$i++){
        $mysqli2 = getConn();
        $stmt2 = $mysqli2->prepare("INSERT INTO `pl-others` VALUES (null, ?, ?, ?, ?)");
        $stmt2->bind_param("iiss", $_GET['pid'], $itemId, $_GET['others'][$i]['other_desc'], $_GET['others'][$i]['other_amt']);
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    echo json_encode($data);
}

function returnData($json,$sql,$result){
    $data['data'] = $json;
    $data['sql'] = $sql;
    $data['total'] = mysqli_num_rows($result);
    echo json_encode($data);
}

switch($_GET["func"]){
    case "createPackingList": createPackingList();
        break;
    case "createPLItem": createPLItem();
        break;
}

?>