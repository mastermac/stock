<?php
require 'db_config.php';
session_start();

function updateSettings()
{
    $mysqli = getConn();
    $sql = "UPDATE settings set currentDrawback=".$_GET['currentDrawback'].", gst=".$_GET['gst'].", exchangeRt=".$_GET['exchangeRt'].", silverRt=".$_GET['silverRt'].", goldRt=".$_GET['goldRt'].", labourRt=".$_GET['labourRt'].", goldLabourRt=".$_GET['goldLabourRt'].", platingRt=".$_GET['platingRt'].", findingsRt=".$_GET['findingsRt'].", microDiaSettingRt=".$_GET['microDiaRt'].", roundStoneSettingRt=".$_GET['roundStoneRt'].", prongDiaSettingRt=".$_GET['prongDiaRt'].", baguetteDiaSettingRt=".$_GET['baguetteDiaRt'].";";
    $result = $mysqli->query($sql);
    $data['sql'] = $sql;
    echo json_encode($data);
}



function updatePLItem(){
    $mysqli = getConn();
    $stmt = $mysqli->prepare("UPDATE `pl-items` SET itemcode=?, mewarcode=?, qty=?, ringsize=?, metaltype=?, metalcolor=?, description=?, total=? where id=?");
    $stmt->bind_param("sssssssss", $_POST['itemcode'], $_POST['mewarcode'], $_POST['qty'], $_POST['ringsize'], $_POST['metaltype'], $_POST['metalcolor'], $_POST['description'], $_POST['total'], $_POST['itemid']);
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
            $stmt2 = $mysqli2->prepare("INSERT INTO `pl-metal` VALUES (null, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("iissss", $_POST['pid'], $itemId, $metalArray[$i]['wt'], $metalArray[$i]['loss'], $metalArray[$i]['price'], $metalArray[$i]['amt']);
            }
        else{
            $stmt2 = $mysqli2->prepare("UPDATE `pl-metal` SET wt=?, loss=?, price=?, amt=? WHERE id=?");
            $stmt2->bind_param("ssssi", $metalArray[$i]['wt'], $metalArray[$i]['loss'], $metalArray[$i]['price'], $metalArray[$i]['amt'], $metalArray[$i]['id']);
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

function lockPackingList()
{
    $data['error']=validatePackingList();
    if($data['error']==""){
        $data['sql']=updateStoneDiamondQty("-");
        updateSoldMetal("enter");
        $mysqli = getConn();
        $c0=0; $c1=1;
        $stmt = $mysqli->prepare("UPDATE packinglist set finalise_date=null, status=?, lock_date=? where id=? and status=?");
        $stmt->bind_param("ssss", $c1, date("Y-m-d H:i:s"), $_GET['id'], $c0 );
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }
    $data['result']="Done";
    echo json_encode($data);
}

function unlockPackingList()
{
    $data['sql']=updateStoneDiamondQty("+");
    updateSoldMetal("delete");
    $mysqli = getConn();
    $c0=0; $c1=1;
    $stmt = $mysqli->prepare("UPDATE packinglist set finalise_date=null, lock_date=null, status=? where id=? and status=?");
    $stmt->bind_param("sss", $c0, $_GET['id'], $c1 );
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
    $data['result']="Done";
    echo json_encode($data);
}

function validatePackingList(){
    $mysqli = getConn();
    $mysqli1 = getConn();
    $sql = "SELECT * from `pl-stone` where pl_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()){
        $sql1 = "SELECT * from `stone-inventory` where lot_no=".$row['lot_id']." AND current_qty>=".$row['qty']." AND current_wt>=".$row['wt'];
        $result1 = mysqli_query($mysqli1, $sql1);
        $totRows = mysqli_num_rows($result1);
        if ($totRows < 1)
            return "Validation Error for Stone #".$row['lot_id'];
    }

    $sql = "SELECT * from `pl-diamond` where pl_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()){
        $sql1 = "SELECT * from `stone-inventory` where lot_no=".$row['lot_id']." AND current_qty>=".$row['qty']." AND current_wt>=".$row['wt'];
        $result1 = mysqli_query($mysqli1, $sql1);
        $totRows = mysqli_num_rows($result1);
        if ($totRows < 1)
            return "Validation Error for Diamond #".$row['lot_id'];
    }

    return "";
}

function updateStoneDiamondQty($op="-"){
    $mysqli = getConn();
    $mysqli1 = getConn();
    $sql = "SELECT * from `pl-stone` where pl_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    $lotids="";
    while ($row = $result->fetch_assoc()){
        $sql1 = "UPDATE `stone-inventory` SET current_qty=current_qty".$op.$row['qty'].", current_wt=current_wt".$op.$row['wt']." where lot_no=".$row['lot_id'];
        $result1 = $mysqli1->query($sql1);
        $sql1 = "UPDATE `stone-inventory` SET current_value=current_wt*cost where lot_no = ".$row['lot_id'];
        $result1 = $mysqli1->query($sql1);
    }

    $sql = "SELECT * from `pl-diamond` where pl_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()){
        $sql1 = "UPDATE `stone-inventory` SET current_qty=current_qty".$op.$row['qty'].", current_wt=current_wt".$op.$row['wt']." where lot_no=".$row['lot_id'];
        $result1 = $mysqli1->query($sql1);
        $sql1 = "UPDATE `stone-inventory` SET current_value=current_wt*cost where lot_no = ".$row['lot_id'];
        $result1 = $mysqli1->query($sql1);
    }
    return "";
}

function updateSoldMetal($op="enter"){
    $mysqli = getConn();
    if($op=="enter"){
        $mysqli1 = getConn();
        $sql = "SELECT packinglist.name, metaltype, metalcolor, SUM(wt) as qty FROM `pl-items`,`pl-metal`,packinglist WHERE `pl-metal`.item_id=`pl-items`.id and `pl-items`.pid=packinglist.id AND pl_id=".$_GET['id']." GROUP BY metaltype";
        $result = $mysqli->query($sql);
        $lotids="";
        while ($row = $result->fetch_assoc()){
            $stmt = $mysqli->prepare("INSERT INTO `metal-sold-inventory` VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $_SESSION['userid'], date("Y-m-d"), $row['name'], getMetalType(strtoupper($row['metaltype'])), strtoupper($row['metaltype']), $row['qty'], date("Y-m-d H:i:s"), $_GET['id'] );
            $stmt->execute();
            $stmt->close();
        }
    }
    else if($op=="delete"){
        $sql = "DELETE from `metal-sold-inventory` where pl_id=".$_GET['id'];
        $result = $mysqli->query($sql);
    }
}
function getMetalType($purity){
    if($purity=="10K" || $purity=="14K" || $purity=="18K" )
        return "G";
    else if($purity=="925")
        return "S";
    else
        return "O";
}
function finalizePackingList()
{

    $mysqli = getConn();
    $c1=1; $c2=2;
    $stmt = $mysqli->prepare("UPDATE packinglist set status=?, finalise_date=? where id=? and status=?");
    $stmt->bind_param("ssss", $c2, date("Y-m-d H:i:s"), $_GET['id'], $c1 );
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
    $data['result']="Done";
    echo json_encode($data);
}


$functionType=$_GET['func'];
if(empty($functionType))
    $functionType=$_POST['func'];

switch($functionType){
    case "updateSettings": updateSettings();
        break;
    case "updatePLItem": updatePLItem();
        break;
    case "lock": lockPackingList();
        break;
    case "unlock": unlockPackingList();
        break;
    case "finalize": finalizePackingList();
        break;
}

?>