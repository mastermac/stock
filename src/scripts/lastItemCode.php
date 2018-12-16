<?php
require 'db_config.php';
session_start();
$mysqli=getConn();
$srch="";
$sql = "SELECT * from itemtype order by icode asc;";
$result = $mysqli->query($sql);
$data="";
$cond="";
$subl=0;
if($_SESSION['series']!='')
	$cond=" userid=".$_SESSION['userid']." and ";
while ($row = $result->fetch_assoc())
{
	$arr=array();
	$sublen=0;
	if($row["purity"]=="14k")
	{
		$srch="14".$_SESSION["series"]."_";
		$sublen=4+strlen($_SESSION["series"]);
		$subl=strlen("14".$_SESSION["series"]);
	}	
	if($row["purity"]=="18k")
	{
		$srch="18".$_SESSION["series"]."_";
		$sublen=4+strlen($_SESSION["series"]);
		$subl=strlen("18".$_SESSION["series"]);
	}	
	if($row["purity"]=="925")
	{
		$srch="SD".$_SESSION["series"];
		$sublen=3+strlen($_SESSION["series"]);
		$subl=strlen("SD".$_SESSION["series"]);
	}
	$srch=$srch.strtoupper(substr($row["iType"],0,1));

	$sqlTotal = "SELECT itemNo from product where".$cond." itemNo not like '%-%' and itemNo like '" . $srch."%' and styleCode=".$row['styleCode']." order by dt desc, sno desc LIMIT 250";
	//echo $sqlTotal;
	//echo '<br>';
	$mysqli1=getConn();
   	$result1 = $mysqli1->query($sqlTotal);
   	$color="";
   	while ($row1 = $result1->fetch_assoc())
   	{
		$num=substr($row1["itemNo"],$subl);
   		if($_SESSION["series"]=="" && strlen(onlyNum(substr($num,0,1)))==0)
	   		array_push($arr,onlyDeciNum($num));
	   	else if($_SESSION["series"]!="")
	   		array_push($arr,onlyDeciNum($num));
   	}
	rsort($arr);
	//print_r($arr);
	$mysqli2=getConn();
	$maxitno="";
   	$s2="SELECT itemNo from product where".$cond." itemNo not like '%-%' and itemNo like '" . $srch.$arr[0]."' and styleCode=".$row['styleCode']." order by dt desc, sno desc LIMIT 1";
  // 	echo $s2;
   //	echo '<br><br>';
   	$result2 = $mysqli2->query($s2);
   	
   	while ($row2 = $result2->fetch_assoc())
   	{
   		$maxitno=$row2["itemNo"];
   	}
   	if($maxitno=='')
   		$maxitno="N.A.";
  	if($row["purity"]=="14k") $color="blue";
   	if($row["purity"]=="18k") $color="red";
   	if($row["purity"]=="925") $color="black";
   	$response[$row["purity"].' '.$row["category"].' '.$row["iType"]]=$maxitno;
   	$data=$data."<tr style='color:".$color.";font-weight:700;'><td style='padding: .15rem;'>".$row["purity"].' '.$row["category"].' '.$row["iType"].'</td>';
   	$data=$data."<td  style='padding: .15rem;text-align:center;'>".$maxitno."</td><td  style='padding: .15rem;text-align:right;'>".$row["styleCode"].'</td></tr>';
}
echo $data;
?>
