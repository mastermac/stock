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
$dataRows=explode(";",$post['data']);
$NumRows=count($dataRows);
$stock=Array();
$errorStockCodes=Array();
$syncedItems=0;
$blankRows=0;
for($i=0;$i<$NumRows;$i++){
    $stock=Array();
    if(strlen(trim($dataRows[$i]))>0)
        $stock=explode(":",$dataRows[$i]);
    else{
        $blankRows++;
        continue;
    }
    if(count($stock)==2){
        $Query[]="UPDATE product SET curstock='".$stock[1]."' WHERE itemNo='".$stock[0]."'";
        $syncedItems++;
    }
    else
        array_push($errorStockCodes,$dataRows[$i]);
}
$Q = implode(';', $Query);
$TheQueries[]=$Q;
$mysqli=getConn();
$mysqli->multi_query($Q);
if(count($errorStockCodes)>0){
    echo "Error in Items -- ".implode(';',$errorStockCodes)."\n";
}
if($syncedItems>0)
    echo $syncedItems." out of ".($NumRows-$blankRows)." Items Synced.";
else
    echo "Invalid Request";
return;
?>