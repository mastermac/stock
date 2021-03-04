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

function upsertData()
{
    $mysqli = getConn();
    foreach ($_GET['data'] as $data){
        if(isset($data['id'])){
            $stmt = $mysqli->prepare("UPDATE `metal-inventory` SET dt=?, description=?, type=?, purity=?, qty=?, rate=?, amt=?, timestamp=? where id=?");
            $stmt->bind_param("sssssssss", $data['dt'], $data['description'], strtoupper($data['type']), strtoupper($data['purity']), $data['qty'], $data['rate'], $data['amt'], date("Y-m-d H:i:s"), $data['id'] );
            $stmt->execute();
            $stmt->close();
        }
        else{
            $stmt = $mysqli->prepare("INSERT INTO `metal-inventory` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $_SESSION['userid'], $data['dt'], $data['description'], strtoupper($data['type']), strtoupper($data['purity']), $data['qty'], $data['rate'], $data['amt'], date("Y-m-d H:i:s") );
            $stmt->execute();
            $stmt->close();
        }
    }    
    $mysqli->close();
    $data['result']="Done";
    echo json_encode($data);
}

function upsertSoldData()
{
    $mysqli = getConn();
    foreach ($_GET['data'] as $data){
        if(isset($data['id'])){
            $stmt = $mysqli->prepare("UPDATE `metal-sold-inventory` SET dt=?, description=?, type=?, purity=?, qty=?, timestamp=? where id=?");
            $stmt->bind_param("sssssss", $data['dt'], $data['description'], strtoupper($data['type']), strtoupper($data['purity']), $data['qty'], date("Y-m-d H:i:s"), $data['id'] );
            $stmt->execute();
            $stmt->close();
        }
        else{
            $stmt = $mysqli->prepare("INSERT INTO `metal-sold-inventory` VALUES (null, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $_SESSION['userid'], $data['dt'], $data['description'], strtoupper($data['type']), strtoupper($data['purity']), $data['qty'], date("Y-m-d H:i:s") );
            $stmt->execute();
            $stmt->close();
        }
    }    
    $mysqli->close();
    $data['result']="Done";
    echo json_encode($data);
}

function moveToInvoice(){
    $mysqli = getConn();
    $count=0;
    foreach ($_GET['data'] as $data){
        $stmt = $mysqli->prepare("INSERT INTO `pl-items` VALUES (null, ?, ?, ?, ?, ?, ?, null, ?)");
        $stmt->bind_param("sssssss", $_GET['packing'], $data['vendorCode'], $data['mewarCode'], $data['qty'], $data['size'], strtoupper(substr($data['type'],0,3)), $data['comments'] );
        $stmt->execute();
        $itemId = $mysqli->insert_id;
        $stmt->close();
        $mysqli1 = getConn();

        foreach ($data['stones'] as $stone){
            $stmt1 = $mysqli1->prepare("INSERT INTO `pl-stone` VALUES (null, ?, ?, ?, ?, null, null, ?, ?, null,null)");
            $stmt1->bind_param("ssssss", $_GET['packing'], $itemId, $stone['lotNo'], $stone['stoneName'], $stone['dia_stone_pcs'], $stone['wt_in_cts'] );
            $stmt1->execute();
            $stmt1->close();
        }

        foreach ($data['diamonds'] as $diamond){
            $stmt1 = $mysqli1->prepare("INSERT INTO `pl-diamond` VALUES (null, ?, ?, ?, null, null, null, ?, ?, null, null)");
            $stmt1->bind_param("sssss", $_GET['packing'], $itemId, $diamond['lotNo'], $diamond['dia_stone_pcs'], $diamond['wt_in_cts'] );
            $stmt1->execute();
            $stmt1->close();
        }
        $mysqli1->close();
        $stmt = $mysqli->prepare("UPDATE `manufacturing` SET invoice_id=? where vendorCode=?");
        $stmt->bind_param("ss", $_GET['packing'], $data['vendorCode'] );
        $stmt->execute();
        $stmt->close();

        $count++;
    }
    $mysqli->close();
    $data['result']=$count." Items moved to selected Packing List";
    echo json_encode($data);
}

function delete(){
    $mysqli = getConn();
    $sql="DELETE from `metal-inventory` where id='".$_GET['id']."';";
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

function getMetalList(){
    $mysqli = getConn();
    $sql = "SELECT * from `metal-inventory` order by id asc";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $json[$sno]['qty']=(float)$row['qty'];
        $json[$sno]['rate']=(int)$row['rate'];
        $json[$sno]['amt']=(int)$row['amt'];
        $sno++;
    }
    returnData($json,$sql,$result);
}

function getSoldMetalList(){
    $mysqli = getConn();
    $sql = "SELECT * from `metal-sold-inventory` order by id asc";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $json[$sno]['qty']=(float)$row['qty'];
        $sno++;
    }
    returnData($json,$sql,$result);
}


switch($_GET["func"]){
    case "getSettings": getSettings();
        break;
    case "getMetalList": getMetalList();
        break;
    case "getSoldMetalList": getSoldMetalList();
        break;
    case "delete": delete();
        break;
    case "upsertData": upsertData();
        break;
    case "upsertSoldData": upsertSoldData();
        break;
    case "moveToInvoice": moveToInvoice();
        break;
}

?>