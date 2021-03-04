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

function getManufacturingList(){
    $mysqli = getConn();
    $sql = "SELECT * from `manufacturing` order by id asc";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $json[$sno]['lotNo']=(int)$row['lotNo'];
        $json[$sno]['qty']=(int)$row['qty'];
        $json[$sno]['wt_in_grms']=(float)$row['wt_in_grms'];
        $json[$sno]['wt_in_cts']=(float)$row['wt_in_cts'];
        $json[$sno]['gold_in_grms']=(float)$row['gold_in_grms'];
        $json[$sno]['gold_in_cts']=(float)$row['gold_in_cts'];
        $json[$sno]['grossWt']=(float)$row['grossWt'];
        $json[$sno]['dia_stone_pcs']=(int)$row['dia_stone_pcs'];
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

function upsertData()
{
    $mysqli = getConn();
    foreach ($_GET['data'] as $data){
        if(isset($data['id'])){
            $stmt = $mysqli->prepare("UPDATE `manufacturing` SET other_metal_grm=?, type=?, mewarCode=?, vendorCode=?, lotNo=?, stoneName=?, qty=?, wt_in_grms=?, wt_in_cts=?, gold_in_grms=?, gold_in_cts=?, grossWt=?, dia_stone_pcs=?, size=?, comments=?, timestamp=?, d_or_s=? where id=?");
            $stmt->bind_param("ssssssssssssssssss", $data['other_metal_grm'], $data['type'], $data['mewarCode'], $data['vendorCode'], $data['lotNo'], $data['stoneName'], $data['qty'], $data['wt_in_grms'], $data['wt_in_cts'], $data['gold_in_grms'], $data['gold_in_cts'], $data['grossWt'], $data['dia_stone_pcs'], $data['size'], $data['comments'], date("Y-m-d H:i:s"), strtoupper($data['d_or_s']), $data['id'] );
            $stmt->execute();
            $stmt->close();
        }
        else{
            $stmt = $mysqli->prepare("INSERT INTO `manufacturing` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, null, ?, ?, null, ?, ?)");
            $stmt->bind_param("sssssssssssssssss", $data['type'], $data['mewarCode'], $data['vendorCode'], $data['lotNo'], $data['stoneName'], $data['qty'], $data['wt_in_grms'], $data['wt_in_cts'], $data['other_metal_grm'], $data['gold_in_grms'], $data['gold_in_cts'], $data['grossWt'], $data['dia_stone_pcs'], $data['size'], $data['comments'], date("Y-m-d H:i:s"), strtoupper($data['d_or_s']) );
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
    $sql="DELETE from `manufacturing` where id='".$_GET['id']."';";
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
    case "getManufacturingList": getManufacturingList();
        break;
    case "addStone": addStone();
        break;
    case "delete": delete();
        break;
    case "upsertData": upsertData();
        break;
    case "moveToInvoice": moveToInvoice();
        break;
}

?>