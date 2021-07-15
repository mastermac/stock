<?php
require 'db_config.php';
require 'po_pdf.php';
require 'so.php';

session_start();

$json = array();
function checkUserType(){
    if ($_SESSION['usertype'] > 1) return ' WHERE userid=' . $_SESSION['userid'];
    return '';
}

function getSettings()
{
    $mysqli = getConn();
    $sql = "SELECT * from settings LIMIT 1";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json[] = $row;

    returnData($json,$sql,$result);
}

function getPurchaseOrders(){
    $mysqli = getConn();
    $sql = "SELECT po_id, COUNT(*) as totalItems FROM `po_items` group by po_id";
    $result = $mysqli->query($sql);
    $dict=[];
    while ($row = $result->fetch_assoc()){
        $dict[$row['po_id']]=$row['totalItems'];
    }
    $sql = "SELECT * from po ".checkUserType()." order by id desc ";
    $result = $mysqli->query($sql);
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $json[$sno]['item_count'] = $dict[$row['id']];
        $sno++;
    }
    returnData($json,$sql,$result);
}

function getPdfId(){
    $mysqli = getConn();
    $sql = "SELECT id FROM `po_so_generated` ORDER BY id DESC LIMIT 1";
    $result = $mysqli->query($sql);
    $id=1;
    while($row = $result->fetch_assoc())
        $id=$row['id']+1;
    return $id;
}

function createPdfId($id){
    $mysqli = getConn();
    $stmt = $mysqli->prepare("INSERT INTO po_so_generated VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id, $_GET['id'], $_SESSION['userid'], date("Y-m-d H:i:s"));
    $stmt->execute();
    $stmt->close();
}


function getPurchaseOrderItems(){
    $mysqli = getConn();
    $sql = "SELECT * from `po_items` where po_id='".$_GET['po_id']."' order by id asc ";
    $result = $mysqli->query($sql);
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    returnData($json,$sql,$result);;
}

function deletePurchaseOrderItem(){
    $mysqli = getConn();
    $sql = "DELETE from `po_items` where po_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    $sql = "DELETE from `po` where id=".$_GET['id'];
    $result = $mysqli->query($sql);
    returnData('',$sql,$result);
}

function createPurchaseOrder(){
    $mysqli = getConn();
    if (empty($_POST['cust_code']))
        $_POST['cust_code'] = '';
    if (empty($_POST['discount']))
        $_POST['discount'] = '';
    
    $stmt = $mysqli->prepare("INSERT INTO `po` VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissssssddssss", $_POST['id'], $_SESSION['userid'], $_POST['cust_code'], date("Y-m-d", strtotime($_POST['entry_date'])), date("Y-m-d", strtotime($_POST['order_date'])), date("Y-m-d", strtotime($_POST['ship_date'])), date("Y-m-d", strtotime($_POST['cancel_date'])), $_POST['type'], date("Y-m-d H:i:s"), $_POST['discount'], round($_POST['total'], 2), $_POST['note'], $_POST['entered_by'], $_POST['ship_via'], $_POST['customer_ref']);
    $stmt->execute();
    $stmt->close();

    $itemArray= json_decode($_POST['items'], true);
    for($i=0;$i<count($itemArray);$i++){
        $sql = "INSERT INTO `po_items` SELECT null, itemNo,vendor,vendorCode,itemPic,description,itemTypeCode,grossWt,diaWt,cstoneWt,goldWt,noOfDia,sellPrice,curStock,ringSize,stoneSize,userid,styleCode,dt,comments,mu,costPrice,dimensions,vendorPO,brand,goldPrice,silverPrice, '".$itemArray[$i]['po_qty']."', 0, '".$_POST['id']."', '".$itemArray[$i]['discount']."', '".$itemArray[$i]['unit_price']."','".$itemArray[$i]['note']."','".date("Y-m-d H:i:s")."' FROM product where itemNo='".$itemArray[$i]['itemNo']."';";
        $mysqli->query($sql);
    }
    $mysqli->close();
    $json['result']='done';
    echo json_encode($json);
}

function updatePurchaseOrder(){
    $mysqli = getConn();

    $stmt = $mysqli->prepare("UPDATE `po` SET cust_code=?, entry_date=?, order_date=?, ship_date=?, cancel_date=?, type=?, last_modified_date=?, discount=?, total=?, note=?, entered_by=?, ship_via=?, customer_ref=? where id=? and userid=?");
    $stmt->bind_param("issssssddssssii", $_POST['cust_code'], date("Y-m-d", strtotime($_POST['entry_date'])), date("Y-m-d", strtotime($_POST['order_date'])), date("Y-m-d", strtotime($_POST['ship_date'])), date("Y-m-d", strtotime($_POST['cancel_date'])), $_POST['type'], date("Y-m-d H:i:s"), $_POST['discount'], round($_POST['total'], 2), $_POST['note'], $_POST['entered_by'], $_POST['ship_via'], $_POST['customer_ref'], $_POST['id'], $_SESSION['userid']);
    $stmt->execute();
    $stmt->close();

    $sql = "SELECT * from `po_items` where po_id=".$_POST['id']." order by id asc";
    $existingPOItems = array();

    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()){
        $existingPOItems[] = $row;
    }
    $itemArray= json_decode($_POST['items'], true);
    for($i=0;$i<count($itemArray);$i++){
        if(isExistingItem($existingPOItems, $itemArray[$i]['itemNo'])){
            $stmt = $mysqli->prepare("UPDATE `po_items` SET po_qty=?, discount=?, unit_price=?, note=?, last_modified_date=? where po_id=? and id=?");
            $stmt->bind_param("sssssss", $itemArray[$i]['po_qty'], $itemArray[$i]['discount'], $itemArray[$i]['unit_price'], $itemArray[$i]['note'], date("Y-m-d H:i:s"), $itemArray[$i]['po_id'], $itemArray[$i]['id']);
            $stmt->execute();
            $stmt->close();
        }
        else{
            $sql = "INSERT INTO `po_items` SELECT null, itemNo,vendor,vendorCode,itemPic,description,itemTypeCode,grossWt,diaWt,cstoneWt,goldWt,noOfDia,sellPrice,curStock,ringSize,stoneSize,userid,styleCode,dt,comments,mu,costPrice,dimensions,vendorPO,brand,goldPrice,silverPrice, '".$itemArray[$i]['po_qty']."', 0, '".$_POST['id']."', '".$itemArray[$i]['discount']."', '".$itemArray[$i]['unit_price']."','".$itemArray[$i]['note']."','".date("Y-m-d H:i:s")."' FROM product where itemNo='".$itemArray[$i]['itemNo']."';";
            $mysqli->query($sql);
        }
    }
    for($i=0;$i<count($existingPOItems);$i++){
        if(!isExistingItem($itemArray, $existingPOItems[$i]['itemNo'])){
            $sql = "DELETE FROM `po_items` WHERE id=".$existingPOItems[$i]['id'].";";
            $mysqli->query($sql);
        }
    }
    $mysqli->close();
    $json['result']='done';
    echo json_encode($json);
}

function isExistingItem($existingItems, $itemNo){
    for($i=0;$i<count($existingItems);$i++){
        if($existingItems[$i]['itemNo']==$itemNo)
            return true;
    }
    return false;
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
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}

function getStockById(){
    $mysqli = getConn();
    $sql = "SELECT * from `product` where itemNo='".$_GET['itemNo']."'";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json = $row;
    $sql = "SELECT SUM(po_qty-po_qty_done) as itemSum from `po_items` where itemNo='".$_GET['itemNo']."'";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json['onOrder'] = (int)$row['itemSum'];

    returnData($json,$sql,$result);
}

function getNewPurchaseOrderId(){
    $mysqli = getConn();
    $sql = "SELECT SUM(wt) as total FROM `pl-diamond` where pl_id in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
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

function updateItemPic()
{
    if (!empty($_FILES['changeItemPic']['name']))
    {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/stock/pics/po/";
        $imageFileType = pathinfo(basename($_FILES["changeItemPic"]["name"]) , PATHINFO_EXTENSION);
        $target_file = $target_dir . $_POST['itemNo'] . '.' . $imageFileType;
        $img = $_FILES['changeItemPic']['tmp_name'];
        $dst = $target_dir . $_POST['itemNo'];
        if (($img_info = getimagesize($img)) === FALSE) die("Image not found or not an image");
        $width = $img_info[0];
        $height = $img_info[1];
        switch ($img_info[2])
        {
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($img);
                break;
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($img);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($img);
                break;
            default:
                die("Unknown filetype");
        }
        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
        imagejpeg($tmp, $dst . ".JPG");
    }
    $json['result']='done';
    echo json_encode($json);
}

$functionType=$_GET['func'];
if(empty($functionType))
    $functionType=$_POST['func'];

switch($functionType){
    case "getStockById": getStockById();
        break;
    case "getPurchaseOrders": getPurchaseOrders();
        break;
    case "getPurchaseOrderItems": getPurchaseOrderItems();
        break;
    case "getNewPurchaseOrderId": getNewPurchaseOrderId();
        break;
    case "deletePurchaseOrderItem": deletePurchaseOrderItem();
        break;
    case "createPurchaseOrder": createPurchaseOrder();
        break;
    case "updatePurchaseOrder": updatePurchaseOrder();
        break;
    case "updateItemPic": updateItemPic();
        break;
    case "generatePO": createPO();
        break;
    case "generateSO": createSO();
        break;
}
