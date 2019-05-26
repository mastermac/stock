<?php
//$mysqli = new mysqli("localhost", "silvesa6_master", "Mastermac@007", "silvesa6_silverapp");

function getConn(){
   if($_SERVER['DOCUMENT_ROOT']=="C:/wamp/www")
      $mysqli = new mysqli("localhost", "root", "", "silverapp");
   else if($_SERVER['DOCUMENT_ROOT']=="C:/wamp64/www")
      $mysqli = new mysqli("localhost", "root", "", "silverapp");
   else
      $mysqli = new mysqli("localhost", "silvesa6_master", "Mastermac@007", "silvesa6_silverapp");
   return $mysqli;
}
function clean($string)
{
   $string = str_replace(' ', '', $string);
   $string = str_replace(',', '', $string);
   $string = preg_replace('/[^0-9\.]/', '', $string);
   if ($string == '') $string = '0';
   return $string;
}
function onlyNum($string){
   $string = str_replace(' ', '', $string);
   $string = str_replace(',', '', $string);
   $string = preg_replace('/[^0-9]/', '', $string);
   return $string;
}
function onlyDeciNum($string){
      $string = str_replace(' ', '', $string);
      $string = str_replace(',', '', $string);
      $string = preg_replace('/[^0-9\.]/', '', $string);
      return $string;
}
function isEmpty($string)
{
   $string = str_replace(' ', '', $string);
   $string = str_replace(',', '', $string);
   $string = preg_replace('/[^0-9\.]/', '', $string);
   if ($string == '') return true;
   return false;
}
function isBlank($string)
{
   $string = str_replace(' ', '', $string);
   $string = str_replace(',', '', $string);
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
   if ($string == '') return true;
   return false;
}
function vendorCheck($string)
{
   $string = str_replace(' ', '', $string);
   $string = str_replace(',', '', $string);
   return strtoupper($string);
}
function getStyleCodeVal($string)
{
   $string = str_replace(' ', '', $string);
   $string = str_replace(',', '', $string);
   $string = preg_replace('/[^0-9]/', '', $string);
   $q1 = "select concat( purity,' ', category,' ',iType) as res from itemtype where styleCode=" . $string;
   $mysqli=getConn();
   $result = $mysqli->query($q1);
   while ($row = $result->fetch_assoc())
   {
      return $row['res'];
   }
}
function checkVal($val)
{
   if ($val == '') return '0';
   return $val;
}
function writeLog($action,$data)
{
   $userid=-1;
   $dt=date("Y-m-d H:i:s");
   $sql="";
   $details="";
   if(isset($_SESSION['userid'])){
      $userid=$_SESSION['userid'];
   }
   switch($action){
      case 1: $details="{userid:".$userid.",ip:".$_SERVER['REMOTE_ADDR'].",success:".$data."}";
         break;
      case 2: $details="{userid:".$userid.",ip:".$_SERVER['REMOTE_ADDR'].",success:".$data."}";
         break;
      case 3: $details="{userid:".$userid.",ip:".$_SERVER['REMOTE_ADDR'].",sql:".$data."}";
         break;
      case 4: $details="{userid:".$userid.",ip:".$_SERVER['REMOTE_ADDR'].",".$data."}";
         break;
      case 5: $details="{userid:".$userid.",ip:".$_SERVER['REMOTE_ADDR'].",sql:".$data."}";
         break;
      case 6: $details="{userid:".$userid.",ip:".$_SERVER['REMOTE_ADDR'].",sql:".$data."}";
         break;
      case 7: $details="{userid:".$userid.",ip:".$_SERVER['REMOTE_ADDR'].",".$data."}";
         break;
      default: $details="{userid:".$userid.",ip:".$_SERVER['REMOTE_ADDR'].",".$data."}";
        break;
   }
   $sql="INSERT INTO logs (userid,action,details,dt) VALUES ('".$userid."','".$action."','".$details."','".$dt."');";
   $mysqli = getConn();
   $result = $mysqli->query($sql);
}
function getCurrentData($id){
   $sql="Select * from product where itemNo='".$id."';";
   $mysqli = getConn();
   $result = $mysqli->query($sql);
   while ($row = $result->fetch_assoc())
   {
      return $row;
   }
   return '#';
}
?>