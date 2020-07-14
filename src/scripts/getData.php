<?php
require 'db_config.php';
session_start();
$num_rec_per_page = $_GET["perPage"];
if (isset($_GET["page"]))
{
   $page = $_GET["page"];
}
else
{
   $page = 1;
}
$mysqli=getConn();
$usertype = '';
$cond="";
$dtcon="";
$itemCon=" itemNo like '%" . $_GET["itemNo"] . "%' and";
$vcon="";
if(trim($_GET['vendor'])!="")
$vcon = " vendor = '".trim($_GET['vendor'])."' and ";
$start_from = ($page - 1) * $num_rec_per_page;
if($_GET["itemNoExt"]!=""){
	$itemArr=preg_split('@(?:\s*,\s*|^\s*|\s*$)@', trim($_GET["itemNoExt"]), NULL, PREG_SPLIT_NO_EMPTY);
	$itemStr=implode("','", $itemArr);

	$itemCon=" itemNo in ('" . $itemStr . "') and";
}
if($_GET['source']=="edit")
	$itemCon=" itemNo = '" . $_GET["itemNo"] . "' and";
if($_GET['styleCode']!="")
	$cond=" and styleCode = '" . $_GET["styleCode"] . "'";
if($_GET['curStock']!=""){
	$stockRange=explode(":",$_GET['curStock']);
	$cond=$cond." and curStock BETWEEN " . $stockRange[0] . " and ".$stockRange[1];
}
if($_GET['sellPrice']!=""){
	$priceRange=explode(":",$_GET['sellPrice']);
	$cond=$cond." and sellPrice BETWEEN " . $priceRange[0] . " and ".$priceRange[1];
}
if($_GET['grossWt']!=""){
	$grossWtRange=explode(":",$_GET['grossWt']);
	$cond=$cond." and grossWt BETWEEN " . $grossWtRange[0] . " and ".$grossWtRange[1];
}

if ($_SESSION['usertype'] >= 1) $usertype = ' and userid=' . $_SESSION['userid'];
if($_GET['sdt']!="0000-00-00")
	$dtcon=" and dt between '".$_GET["sdt"]." 00:00:00' and '".$_GET["edt"]." 23:59:59'";
$sqlTotal = "SELECT * FROM product where".$itemCon.$vcon." vendorCode like '%" . $_GET["vendorCode"] . "%' and description like '%" . $_GET["description"] . "%' and itemTypeCode like '%" . $_GET["itemTypeCode"] . "%' and diaWt like '%" . $_GET["diaWt"] . "%' and cstoneWt like '%" . $_GET["cstoneWt"] . "%' and goldWt like '%" . $_GET["goldWt"] . "%' ".$cond." and ringSize like '%" . $_GET["ringSize"] . "%'" . $usertype.$dtcon;

$sql = "SELECT * FROM product where".$itemCon.$vcon." vendorCode like '%" . $_GET["vendorCode"] . "%' and description like '%" . $_GET["description"] . "%' and itemTypeCode like '%" . $_GET["itemTypeCode"] . "%' and diaWt like '%" . $_GET["diaWt"] . "%' and cstoneWt like '%" . $_GET["cstoneWt"] . "%' and goldWt like '%" . $_GET["goldWt"] . "%' ".$cond." and ringSize like '%" . $_GET["ringSize"] . "%'" . $usertype.$dtcon . " Order By dt desc, sno desc LIMIT $start_from, $num_rec_per_page";
//echo $sql;
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc())
{
   $json[] = $row;
   $json['type']=$_SESSION['usertype'];
}
if(!isset($json))
	$json[0]['def']="null";
$data['data'] = $json;
$result = mysqli_query($mysqli, $sqlTotal);
$data['total'] = mysqli_num_rows($result);
$data['sql']=$sql;
echo json_encode($data);
?>
