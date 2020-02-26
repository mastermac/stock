<?php
require 'db_config.php';
$post = $_POST;

if(empty($post['auth_token']) || $post['auth_token']!="cw\"o1s]zvN|6x@ShG4gKFeUX`slFoXNDA2~|2S6|BF=unZ\"SL'FNThq6O-@-f*)")
    return "Authentication Error";
if(empty($post['data']))
    return "Invalid Request";

$Query=Array();
$dataRows=explode(";",$post['data']);
$NumRows=count($dataRows);
$stock=Array();
$errorStockCodes=Array();
for($i=1;$i<=$NumRows;$i++){
    $stock=Array();
    $stock=explode(":",$dataRows[$i]);
    if(count($stock)==2)
        $Query[]="UPDATE product SET curstock='".$stock[1]."' WHERE itemNo='".$stock[0]."'";
    else
        array_push($errorStockCodes,$dataRows[$i]);
}
$Q = implode(';', $Query);
$TheQueries[]=$Q;
$mysqli=getConn();
$mysqli->multi_query($Q);
if(count($errorStockCodes)>0){
    echo "Error in Items -- ".implode(';'. $errorStockCodes)."\n";
}
echo ($NumRows - count($errorStockCodes))." out of ".$NumRows." Items Synced.";
return;
?>