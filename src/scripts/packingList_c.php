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
        $stmt->bind_param("sssssssssssss", $_GET['date'], $_GET['name'], $row['exchangeRt'], $row['silverRt'], $row['goldRt'], $row['labourRt'], $row['platingRt'], $row['findingsRt'], $_SESSION['userid'], $row['microDiaSettingRt'], $row['prongDiaSettingRt'], $row['baguetteDiaSettingRt'], $row['roundStoneSettingRt']);
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
    $data['sql'] = $stmt;

    $stmt->close();
    $mysqli->close();
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