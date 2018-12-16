<?php
session_start();
require 'db_config.php';
$errors = array();
$uploadedFiles = array();
$extension = array("jpeg","jpg","png","gif","JPEG","JPG","PNG","GIF");
$bytes = 1024;
$KB = 1024;
$totalBytes = $bytes * $KB;
$UploadFolder = $_SERVER['DOCUMENT_ROOT'] . "/stock/pics/";
$resp['err']="";
$resp['suc']="";
$resp['scount']=0;
$resp['scount']=0;
$counter = 0;
 
foreach($_FILES["files"]["tmp_name"] as $key=>$tmp_name){
    $temp = $_FILES["files"]["tmp_name"][$key];
    $name = $_FILES["files"]["name"][$key];
    $filename = pathinfo($_FILES['files']['name'][$key], PATHINFO_FILENAME);
        $target_dir    = $_SERVER['DOCUMENT_ROOT'] . "/stock/pics/";
        $imageFileType = pathinfo(basename($_FILES["files"]["name"][$key]), PATHINFO_EXTENSION);
        $target_file   = $target_dir . $filename . '.' . $imageFileType;
        $img           = $_FILES['files']['tmp_name'][$key];
        $dst           = $target_dir . trim($filename);
    	
    if(empty($temp))
    {
        break;
    }
     
    $counter++;
    $UploadOk = true;
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    if($UploadOk && in_array($ext, $extension) == false){
        $UploadOk = false;
        array_push($errors, $name." is invalid file type.");
    } 
    if($UploadOk && $_FILES["files"]["size"][$key] > $totalBytes)
    {
        $UploadOk = false;
        array_push($errors, $name." file size is larger than the 1 MB.");
    }     
    if ($UploadOk && ($img_info = getimagesize($img)) === FALSE)
    {
        $UploadOk = false;
        array_push($errors, "Image Not Found");
    }     
      
    if($UploadOk == true){
    	$img_info = getimagesize($img);
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
                array_push($errors, $name." is invalid file type.");
        }
        $tmp = imagecreatetruecolor(500, 500);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, 500, 500, $width, $height);
        imagejpeg($tmp, $dst . ".JPG", 80);
        imagedestroy($tmp);
        imagedestroy($src);
        array_push($uploadedFiles, $name);
    }
}
 
if($counter>0){
    if(count($errors)>0)
    {
        $resp['err']="<ul>";
        foreach($errors as $error)
        {
            $resp['err']=$resp['err']."<li>".$error."</li>";
        }
        $resp['err']=$resp['err']."</ul><br/>";
    }
    $resp['scount']=count($uploadedFiles);
    $resp['ecount']=count($errors);                     
}
else{
	$resp['ecount']=1;
    $resp['err']="Please, Select file(s) to upload.";
}
echo json_encode($resp);
?>