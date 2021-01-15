<?php
session_start();
require 'db_config.php';
$mysqli=getConn();
$sql="";
$accountActive=1;
if(!isset($_POST['accountActive']))
	$_POST['accountActive']=0;
$canExport=0;
if(!isset($_POST['canExport']))
	$_POST['canExport']=1;
if($_POST['vendorAction']=="add"){
$sql="INSERT INTO vendor VALUES (null,'".$_POST['vendorId']."','".$_POST['vendorName']."','".$_POST['vendorProfit']."'";
if(isset($_POST['newAccount']))
$sql=$sql.",'".$_POST['vendorEmail']."','".$_POST['vendorPwd']."','".$_POST['vendorType']."','".$_POST['vendorSeries']."',$canExport,$accountActive);";
else
$sql=$sql.",null,null,1,'',$canExport,$accountActive);";	

}
else if($_POST['vendorAction']=="edit"){
	$sql="UPDATE vendor set name='".$_POST['vendorName']."', profit='".$_POST['vendorProfit']."'";
if(isset($_POST['newAccount']))
$sql=$sql.",email='".$_POST['vendorEmail']."',pwd='".$_POST['vendorPwd']."',type='".$_POST['vendorType']."',series='".$_POST['vendorSeries']."',canExport=$canExport,enabled=$accountActive where vid=".$_POST['vid'].";";
else
$sql=$sql.",email=null,pwd=null,type=1,series='',canExport=$canExport,enabled=$accountActive where vid=".$_POST['vid'].";";	
}
$result  = $mysqli->query($sql);
$sql     = "SELECT * FROM vendor where code='" . $_POST['vendorId'] . "';";
$result  = $mysqli->query($sql);
while ($row = $result->fetch_assoc())
  $logsql=implode(',', $row);
writeLog(7, $logsql);
$totRows = mysqli_num_rows($result);
if ($totRows == 1) {    
	$res['success']=1;
	$res['id']=$_POST['vendorId'];
    echo json_encode($res);
} else {
    $res['success']=0;
    echo json_encode($res);
}


?>