<?php
session_start();
require 'db_config.php';
$mysqli=getConn();      
$_SESSION['userid']='36';
$sql = "SELECT * FROM `logs` where userid=".$_SESSION['userid']." ORDER BY `logid` DESC LIMIT ".$_GET['txn'].";";
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc())
{
    switch($row['action'])
    {
        case 3:
            revertInsert(extractPrevData($row['details'],3),-3);
            break;
        case 4:
            revertImportUpdate(extractPrevData($row['details'],4),-4);
            break;
        case 5:
            revertDelete(extractPrevData($row['details'],5),-5);
            break;
        case 7:
            $preData=extractPrevData($row['details'],7);
            // print_r($preData);
            if(strpos($preData[0], 'newData') !== false) {
                revertInsert($preData,-7);
            }
            else
                revertImportUpdate($preData,-7);
            break;
    }
}
$data['data'] = 'success';
echo json_encode($data);

function extractPrevData($details,$origin){
    $details1=substr($details,1,-1);
    $arr = explode(",", $details1);
    $prevDataArr;
    switch($origin){
        case 3:
            $prevDataArr=explode("#",$arr[2]);
            break;
        case 4:
            $prevDataArr=explode("#",$arr[3]);
            break;
        case 5:
            $prevDataArr=explode("#",$arr[2]);
            break;
        case 7:
            $prevDataArr=explode("#",$arr[3]);
            if(count($prevDataArr)<3)
                $prevDataArr=explode("#",$arr[4]);
            break;
    }
    return $prevDataArr;
}

function revertDelete($prevData,$origin){
    array_shift($prevData);
    $sql="INSERT INTO product values (null,'".implode("','",$prevData)."');";
    $data['sql'] = $sql;
    $mysqli=getConn();      
    $result = $mysqli->query($sql);
    writelog($origin,implode("#",$prevData));
}

function revertInsert($prevData,$origin){
    $sql="DELETE FROM product where itemNo='".$prevData[1]."';";
    $data['sql'] = $sql;
    // echo $sql;
    $mysqli=getConn();      
    $result = $mysqli->query($sql);
    writelog($origin,implode("#",$prevData));
}

function revertImportUpdate($prevData,$origin){
    $sql = "UPDATE product SET comments='".$prevData[19]."', mu='".$prevData[20]."', costPrice='".$prevData[21]."', itemNo='" . $prevData[1] . "', vendor='" . $prevData[2] . 
           "', vendorCode='" . vendorCheck($prevData[3]) . "', description='" . $prevData[5] . "', itemTypeCode='" . 
           $prevData[6] . "', grossWt='" . $prevData[7] . "',diaWt='" . $prevData[8] . 
           "',cstoneWt='" . $prevData[9] . "',goldWt='" . $prevData[10] . "',noOfDia='" . $prevData[11] . 
           "',sellPrice='" . $prevData[12] . "',curStock='" . $prevData[13] . "',ringSize='" . $prevData[14] . 
           "',styleCode='" . $prevData[17] . "', dt='".$prevData[18]."' where sno='" . substr($prevData[0],9) . "' ";
    // echo $sql;
    $mysqli=getConn();      
    $result = $mysqli->query($sql);
    $sql="UPDATE producthistory as p, (Select id from producthistory where sno=" . substr($prevData[0],9)." Order By timestamp desc limit 1) as i SET p.action='UNDO' where p.id=i.id";
    // echo $sql;
    $result = $mysqli->query($sql);
    $data['sql'] = $sql;
    writelog($origin,implode("#",$prevData));
}
?>
