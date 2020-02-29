<?php
require 'db_config.php';
$post = $_POST;

if(empty($post['auth_token']) || $post['auth_token']!="cw\"o1s]zvN|6x@ShG4gKFeUX`slFoXNDA2~|2S6|BF=unZ\"SL'FNThq6O-@-f*)"){
    echo "Authentication Error";
    return;
}
if(empty($post['data'])){
    echo "Invalid Request";
    return;
}

$Query=Array();
$UpdateLogQuery=Array();
$dataRows=explode(";",$post['data']);
$NumRows=count($dataRows);
$stock=Array();
$errorStockCodes=Array();
$syncedItems=0;
$blankRows=0;
$itemCodes="";
for($i=0;$i<$NumRows;$i++){
    $stock=Array();
    if(strlen(trim($dataRows[$i]))>0)
        $stock=explode(":",$dataRows[$i]);
    else{
        $blankRows++;
        continue;
    }
    if(count($stock)==2){
        $itemCodes=$itemCodes."'".trim($stock[0])."',";
        $Query[]="UPDATE product SET curstock='".trim($stock[1])."' WHERE itemNo='".trim($stock[0])."'";
        // $UpdateLogQuery[]="UPDATE producthistory SET action='API-update' where curstock='".$stock[1]."' and itemNo='".$stock[0]."'";
        $UpdateLogQuery[]="UPDATE producthistory as p, (Select id from producthistory where itemNo='".trim($stock[0])."' and curstock='".trim($stock[1])."' Order By timestamp desc limit 1) as i SET p.action='API-update' where p.id=i.id";
        $syncedItems++;
    }
    else
        array_push($errorStockCodes,$dataRows[$i]);
}
$itemCodes=substr($itemCodes,0,-1);
$selectSQLQuery="Select itemNo from product where itemNo in (".$itemCodes.");";
$Q = implode(';', $Query);
$TheQueries[]=$Q;
$mysqli=getConn();
$mysqli->multi_query($Q);
$noItemFoundCodes=Array();
// echo $selectSQLQuery;
$mysqli2=getConn();
$result = $mysqli2->query($selectSQLQuery);
$noItemFoundCodes=explode(",",str_replace("'","",$itemCodes));
$itemFoundArray=Array();
while (($row = $result->fetch_assoc())!==null)
{
    array_push($itemFoundArray, $row["itemNo"]);
}
// echo implode(',',$itemFoundArray);
$noItemFoundCodes = \array_diff($noItemFoundCodes,$itemFoundArray);

if(count($errorStockCodes)>0){
    echo "Error in Items -- ".implode(';',$errorStockCodes)."\n";
}
if(count($noItemFoundCodes)>0)
{
    echo "No Items found with ItemCodes -- ".implode(',',$noItemFoundCodes)."\n";
}
if($syncedItems>0){
    echo ($syncedItems-count($noItemFoundCodes))." out of ".($NumRows-$blankRows)." Items Synced.";
    $QL=implode(';',$UpdateLogQuery);
    //echo $QL;
    $mysqli1=getConn();
    $mysqli1->multi_query($QL);
}
else
    echo "Invalid Request";
return;
?>