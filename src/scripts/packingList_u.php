<?php
require 'db_config.php';
session_start();

function updateSettings()
{
    $mysqli = getConn();
    $sql = "UPDATE settings set exchangeRt=".$_GET['exchangeRt'].", silverRt=".$_GET['silverRt'].", goldRt=".$_GET['goldRt'].", labourRt=".$_GET['labourRt'].", goldLabourRt=".$_GET['goldLabourRt'].", platingRt=".$_GET['platingRt'].", findingsRt=".$_GET['findingsRt'].", microDiaSettingRt=".$_GET['microDiaRt'].", roundStoneSettingRt=".$_GET['roundStoneRt'].", prongDiaSettingRt=".$_GET['prongDiaRt'].", baguetteDiaSettingRt=".$_GET['baguetteDiaRt'].";";
    $result = $mysqli->query($sql);
    $data['sql'] = $sql;
    echo json_encode($data);
}

function updatePLItem(){
    $mysqli = getConn();
    $stmt = $mysqli->prepare("UPDATE `pl-items` SET itemcode=?, mewarcode=?, qty=?, ringsize=?, metaltype=?, metalcolor=?, description=? where id=?");
    $stmt->bind_param("ssssssss", $_POST['itemcode'], $_POST['mewarcode'], $_POST['qty'], $_POST['ringsize'], $_POST['metaltype'], $_POST['metalcolor'], $_POST['description'], $_POST['itemid']);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
    $itemId=$_POST['itemid'];

    $metalArray= json_decode($_POST['metals'], true);
    $diamondArray= json_decode($_POST['diamonds'], true);
    $stoneArray= json_decode($_POST['stones'], true);
    $otherArray= json_decode($_POST['others'], true);
    
    for($i=0;$i<count($metalArray);$i++){
        $mysqli2 = getConn();
        if(empty($metalArray[$i]['id'])){
            $stmt2 = $mysqli2->prepare("INSERT INTO `pl-metal` VALUES (null, ?, ?, ?, ?)");
            $stmt2->bind_param("iiss", $_POST['pid'], $itemId, $metalArray[$i]['wt'], $metalArray[$i]['amt']);
        }
        else{
            $stmt2 = $mysqli2->prepare("UPDATE `pl-metal` SET wt=?, amt=? WHERE id=?");
            $stmt2->bind_param("ssi", $metalArray[$i]['wt'], $metalArray[$i]['amt'], $metalArray[$i]['id']);
        }
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($diamondArray);$i++){
        $mysqli2 = getConn();
        if(empty($diamondArray[$i]['id'])){
            $stmt2 = $mysqli2->prepare("INSERT INTO `pl-diamond` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("iiisssisss", $_POST['pid'], $itemId, $diamondArray[$i]['lot_id'], $diamondArray[$i]['shape'], $diamondArray[$i]['size'], $diamondArray[$i]['setting'], $diamondArray[$i]['qty'], $diamondArray[$i]['wt'], $diamondArray[$i]['rate'], $diamondArray[$i]['amt']);
        }
        else{
            $stmt2 = $mysqli2->prepare("UPDATE `pl-diamond` SET shape=?, size=?, setting=?, qty=?, wt=?, rate=?, amt=? where id=?");
            $stmt2->bind_param("sssssssi", $diamondArray[$i]['shape'], $diamondArray[$i]['size'], $diamondArray[$i]['setting'], $diamondArray[$i]['qty'], $diamondArray[$i]['wt'], $diamondArray[$i]['rate'], $diamondArray[$i]['amt'], $diamondArray[$i]['id'] );
        }
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($stoneArray);$i++){
        $mysqli2 = getConn();
        if(empty($stoneArray[$i]['id'])){
            $stmt2 = $mysqli2->prepare("INSERT INTO `pl-stone` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("iiisssisss", $_POST['pid'], $itemId, $stoneArray[$i]['lot_id'], $stoneArray[$i]['name'], $stoneArray[$i]['shape'], $stoneArray[$i]['size'], $stoneArray[$i]['qty'], $stoneArray[$i]['wt'], $stoneArray[$i]['rate'], $stoneArray[$i]['amt']);
        }
        else{
            $stmt2 = $mysqli2->prepare("UPDATE `pl-stone` SET name=?, shape=?, size=?, qty=?, wt=?, rate=?, amt=? WHERE id=?");
            $stmt2->bind_param("sssssssi", $stoneArray[$i]['name'], $stoneArray[$i]['shape'], $stoneArray[$i]['size'], $stoneArray[$i]['qty'], $stoneArray[$i]['wt'], $stoneArray[$i]['rate'], $stoneArray[$i]['amt'], $stoneArray[$i]['id']);
        }
        $stmt2->execute();
        $stmt2->close();
        $mysqli2->close();
    }
    for($i=0;$i<count($otherArray);$i++){
        $mysqli2 = getConn();
        if(empty($otherArray[$i]['id'])){
            $stmt2 = $mysqli2->prepare("INSERT INTO `pl-others` VALUES (null, ?, ?, ?, ?)");
            $stmt2->bind_param("iiss", $_POST['pid'], $itemId, $otherArray[$i]['description'], $otherArray[$i]['amt']);
        }
        else{
            $stmt2 = $mysqli2->prepare("UPDATE `pl-others` SET description=?, amt=? WHERE id=?");
            $stmt2->bind_param("ssi", $otherArray[$i]['description'], $otherArray[$i]['amt'], $otherArray[$i]['id']);
        }
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

$functionType=$_GET['func'];
if(empty($functionType))
    $functionType=$_POST['func'];

switch($functionType){
    case "updateSettings": updateSettings();
        break;
    case "updatePLItem": updatePLItem();
}

?>