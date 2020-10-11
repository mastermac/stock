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
    $stmt->bind_param("ississss", $_POST['pid'], $_POST['itemcode'], $_POST['mewarcode'], $_POST['qty'], $_POST['ringsize'], $_POST['metaltype'], $_POST['metalcolor'], $_POST['description']);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();

    $mysqli1 = getConn();
    $sql = "SELECT id from `pl-items` WHERE pid='".$_POST['pid']."' AND itemcode='".$_POST['itemcode']."' AND mewarcode='".$_POST['mewarcode']."' AND qty='".$_POST['qty']."' AND metaltype='".$_POST['metaltype']."' AND metalcolor='".$_POST['metalcolor']."' AND description='".$_POST['description']."' ORDER BY id DESC LIMIT 1";
    $data['sql']=$sql;
    $result = $mysqli1->query($sql);
    $itemId='';
    while ($row = $result->fetch_assoc()){
        $itemId = $row['id'];
        $data['itemId']=$itemId;
    }
    $metalArray= json_decode($_POST['metals'], true);
    $diamondArray= json_decode($_POST['diamonds'], true);
    $stoneArray= json_decode($_POST['stones'], true);
    $otherArray= json_decode($_POST['others'], true);
    // print_r($metalArray[0]);
    for($i=0;$i<count($metalArray);$i++){
        $mysqli2 = getConn();
        $stmt2 = $mysqli2->prepare("INSERT INTO `pl-metal` VALUES (null, ?, ?, ?, ?)");
        $stmt2->bind_param("iiss", $_POST['pid'], $itemId, $metalArray[$i]['wt'], $metalArray[$i]['amt']);
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($diamondArray);$i++){
        $mysqli2 = getConn();
        $stmt2 = $mysqli2->prepare("INSERT INTO `pl-diamond` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiisssisss", $_POST['pid'], $itemId, $diamondArray[$i]['lot_id'], $diamondArray[$i]['shape'], $diamondArray[$i]['size'], $diamondArray[$i]['setting'], $diamondArray[$i]['qty'], $diamondArray[$i]['wt'], $diamondArray[$i]['rate'], $diamondArray[$i]['amt']);
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($stoneArray);$i++){
        $mysqli2 = getConn();
        $stmt2 = $mysqli2->prepare("INSERT INTO `pl-stone` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiisssisss", $_POST['pid'], $itemId, $stoneArray[$i]['lot_id'], $stoneArray[$i]['name'], $stoneArray[$i]['shape'], $stoneArray[$i]['size'], $stoneArray[$i]['qty'], $stoneArray[$i]['wt'], $stoneArray[$i]['rate'], $stoneArray[$i]['amt']);
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($otherArray);$i++){
        $mysqli2 = getConn();
        $stmt2 = $mysqli2->prepare("INSERT INTO `pl-others` VALUES (null, ?, ?, ?, ?)");
        $stmt2->bind_param("iiss", $_POST['pid'], $itemId, $otherArray[$i]['description'], $otherArray[$i]['amt']);
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }

    if (!empty($_FILES['itemPic']['name'])) {
        $target_dir    = $_SERVER['DOCUMENT_ROOT'] . "/stock/pack/pics/";
        $imageFileType = pathinfo(basename($_FILES["itemPic"]["name"]), PATHINFO_EXTENSION);
        $target_file   = $target_dir . $_POST['itemcode'] . '.' . $imageFileType;
        $img           = $_FILES['itemPic']['tmp_name'];
        $dst           = $target_dir . $_POST['itemcode'];
        if (($img_info = getimagesize($img)) === FALSE)
            die("Image not found or not an image");
        $width  = $img_info[0];
        $height = $img_info[1];
        switch ($img_info[2]) {
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
        $tmp = imagecreatetruecolor(200, 200);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, 200, 200, $width, $height);
        imagejpeg($tmp, $dst . ".JPG",80);
        imagedestroy($tmp);
        imagedestroy($src);
    }
    echo json_encode($data);
}

function returnData($json,$sql,$result){
    $data['data'] = $json;
    $data['sql'] = $sql;
    $data['total'] = mysqli_num_rows($result);
    echo json_encode($data);
}

$functionType=$_GET['func'];
if(empty($functionType))
    $functionType=$_POST['func'];

switch($functionType){
    case "createPackingList": createPackingList();
        break;
    case "createPLItem": createPLItem();
        break;
}

?>