<?php
require 'db_config.php';
session_start();
$post = $_POST;
$lineError = "";
if (!empty($_FILES['edit_itemPic']['name']))
{
   $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/stock/pics/";
   $imageFileType = pathinfo(basename($_FILES["edit_itemPic"]["name"]) , PATHINFO_EXTENSION);
   $target_file = $target_dir . $post['edit_itemId'] . '.' . $imageFileType;
   $img = $_FILES['edit_itemPic']['tmp_name'];
   $dst = $target_dir . $post['edit_itemId'];
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
$previousData="";
$newData="";
$mysqli=getConn();
$previousData=implode("#",getCurrentData($post['edit_id']));

$usertype="";
if ($_SESSION['usertype'] >= 1) $usertype = " and userid='" . $_SESSION['userid']."'";
$sql = "UPDATE product SET comments='".$post['edit_comments']."', itemNo='" . $post['edit_itemId'] . "', vendor='" . strtoupper($post['edit_vendor']) . "', 
      vendorCode='" . vendorCheck($post['edit_vendorCode']) . "', description='" . $post['edit_description'] . "', itemTypeCode='" . 
      getStyleCodeVal($post['edit_styleCode']) . "', grossWt='" . clean($post['edit_grossWt']) . "',diaWt='" . clean($post['edit_diaWt']) . "',
      cstoneWt='" . clean($post['edit_cstoneWt']) . "',goldWt='" . clean($post['edit_goldWt']) . "',noOfDia='" . clean($post['edit_noOfDia']) . "',
      sellPrice='" . clean($post['edit_sellPrice']) . "',curStock='" . clean($post['edit_curStock']) . "',ringSize='" . $post['edit_ringSize'] . "',
      styleCode='" . clean($post['edit_styleCode']) . "', mu='".$post['edit_mu']."', costPrice='".$post['edit_costPrice']."', dimensions='".$post['edit_dimensions']."', vendorPO='".$post['edit_vendorPO']."' where itemNo='" . $post['edit_id'] . "' ".$usertype;
$result = $mysqli->query($sql);
$mysqli1 = getConn();
$sqlTotal = "Select * from product where itemNo='" . $post['edit_itemId'] . "' and vendor='" . strtoupper($post['edit_vendor']) . "' and vendorCode='" . 
            vendorCheck($post['edit_vendorCode']) . "' and description='" . $post['edit_description'] . "' and itemTypeCode='" . 
            getStyleCodeVal($post['edit_styleCode']) . "' and grossWt='" . clean($post['edit_grossWt']) . "' and diaWt='" . clean($post['edit_diaWt']) . 
            "' and cstoneWt='" . clean($post['edit_cstoneWt']) . "' and goldWt='" . clean($post['edit_goldWt']) . "' and noOfDia='" . clean($post['edit_noOfDia']) .
             "' and sellPrice='" . clean($post['edit_sellPrice']) . "' and curStock='" . clean($post['edit_curStock']) . "' and ringSize='" . 
             $post['edit_ringSize'] . "' and styleCode='" . clean($post['edit_styleCode']) . "' and mu='".$post['edit_mu']."';";
$result1 = mysqli_query($mysqli1, $sqlTotal);
$totRows = mysqli_num_rows($result1);
if ($totRows == 1) {
while ($row = $result1->fetch_assoc())
   $newData=implode('#', $row);
   writelog(4, "success:2,prevData:".$previousData.",newData:".$newData);
   echo "1";
}
else echo "0";
?>
