<?php
session_start();
require 'db_config.php';
$post    = $_POST;
$session = $_SESSION['userid'];
$date = date('Y/m/d H:i:s');
$sql     = "INSERT INTO product VALUES ( NULL,
  '" . $post['itemId'] . "','" . $post['vendor'] . "',
  '" . $post['vendorCode'] . "','',
  '" . $post['description'] . "','" . $post['itemTypeCode'] . "',
  '" . clean($post['grossWt']) . "','" . clean($post['diaWt']) . "',
  '" . clean($post['cstoneWt']) . "','" . clean($post['goldWt']) . "',
  '" . clean($post['noOfDia']) . "','" . clean($post['sellPrice']) . "',
  '" . clean($post['curStock']) . "','" . $post['ringSize'] . "',
  '','" . $session . "','" . clean($post['styleCode']) . "','".$date."','".$post['comments']."','".$post['mu']."','".$post['costPrice']."','".$post['dimensions']."','".$post['vendorPO']."','".$post['brand']."','".$post['goldPrice']."','".$post['silverPrice']."'
  )";
  //echo $sql;
$logsql="";
$mysqli=getConn();
$result  = $mysqli->query($sql);
$newData=implode("#",getCurrentData($post['itemId']));

$sql     = "SELECT * FROM product where itemNo='" . $post['itemId'] . "' and vendor='" . $post['vendor'] . "' and vendorCode='" . $post['vendorCode'] . "' and description='" . $post['description'] . "' and itemTypeCode='" . $post['itemTypeCode'] . "' and grossWt='" . clean($post['grossWt']) . "' and diaWt='" . clean($post['diaWt']) . "' and  cstoneWt = '" . clean($post['cstoneWt']) . "' and goldWt='" . clean($post['goldWt']) . "' and noOfDia='" . clean($post['noOfDia']) . "' and sellPrice='" . clean($post['sellPrice']) . "' and curStock= '" . clean($post['curStock']) . "' and ringSize='" . $post['ringSize'] . "' and userid='" . $session . "' and styleCode='" . clean($post['styleCode']) . "';";
$result  = $mysqli->query($sql);
$totRows = mysqli_num_rows($result);
if ($totRows == 1) {
    writeLog(3, $newData);
    if (!empty($_FILES['itemPic']['name'])) {
        $target_dir    = $_SERVER['DOCUMENT_ROOT'] . "/stock/pics/";
        $imageFileType = pathinfo(basename($_FILES["itemPic"]["name"]), PATHINFO_EXTENSION);
        $target_file   = $target_dir . $post['itemId'] . '.' . $imageFileType;
        $img           = $_FILES['itemPic']['tmp_name'];
        $dst           = $target_dir . $post['itemId'];
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
        $tmp = imagecreatetruecolor(500, 500);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, 500, 500, $width, $height);
        imagejpeg($tmp, $dst . ".JPG",80);
        imagedestroy($tmp);
        imagedestroy($src);
    }
    if (!empty($_FILES['itemPic1']['name'])) {
        $target_dir    = $_SERVER['DOCUMENT_ROOT'] . "/stock/pics/pic1/";
        $imageFileType = pathinfo(basename($_FILES["itemPic1"]["name"]), PATHINFO_EXTENSION);
        $target_file   = $target_dir . $post['itemId'] . '.' . $imageFileType;
        $img           = $_FILES['itemPic1']['tmp_name'];
        $dst           = $target_dir . $post['itemId'];
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
        $tmp = imagecreatetruecolor(500, 500);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, 500, 500, $width, $height);
        imagejpeg($tmp, $dst . ".JPG",80);
        imagedestroy($tmp);
        imagedestroy($src);
    }
    if (!empty($_FILES['itemPic2']['name'])) {
        $target_dir    = $_SERVER['DOCUMENT_ROOT'] . "/stock/pics/pic2/";
        $imageFileType = pathinfo(basename($_FILES["itemPic2"]["name"]), PATHINFO_EXTENSION);
        $target_file   = $target_dir . $post['itemId'] . '.' . $imageFileType;
        $img           = $_FILES['itemPic2']['tmp_name'];
        $dst           = $target_dir . $post['itemId'];
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
        $tmp = imagecreatetruecolor(500, 500);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, 500, 500, $width, $height);
        imagejpeg($tmp, $dst . ".JPG",80);
        imagedestroy($tmp);
        imagedestroy($src);
    }
    $res['success']=1;
    echo json_encode($res);
} else {
    $res['success']=0;
    echo json_encode($res);
}
?>