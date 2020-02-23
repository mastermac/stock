<?php
session_start();
require 'db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';
$fna="importFile";
if($_POST['src']=="updateForm")
$fna="updateFile";
$objPHPExcel = new PHPExcel();
$successEntries = 0;
$updateEntries = 0;
$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/stock/src/import/";
$imageFileType = pathinfo(basename($_FILES[$fna]["name"]) , PATHINFO_EXTENSION);
$fname = mt_rand();
$target_file = $target_dir . $fname . '.' . $imageFileType;
if ($imageFileType == 'xlsx' || $imageFileType == 'xls' )
{
   if (move_uploaded_file($_FILES[$fna]["tmp_name"], $target_file))
   {
      $inputFileName = '../import/' . $fname . '.'. $imageFileType;
      try
      {
         $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
         $objReader = PHPExcel_IOFactory::createReader($inputFileType);
         $objPHPExcel = $objReader->load($inputFileName);
      }
      catch(Exception $e)
      {
         die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
      }
      $sheet = $objPHPExcel->getSheet(0);
      $highestRow = $sheet->getHighestRow();
      $highestColumn = $sheet->getHighestColumn();
      $lineError = "";
      $date = date('Y/m/d H:i:s');
      $blankLine=0;
      $NoDashInItemFile=true;
      for($row = 2; $row <= $highestRow; $row++){
         $rowData = $sheet->rangeToArray('A' . $row . ':' . 'N' . $row, NULL, TRUE, FALSE);
         $data = $rowData[0];
         if(strpos($data[2],'-')!==false && (clean($data[13])=='586' || clean($data[13])=='756' || clean($data[13])=='6'))
         {
            $NoDashInItemFile=false;
            break;
         }
      }
      if($NoDashInItemFile){
         for ($row = 2; $row <= $highestRow; $row++)
         {
            $previousData="";
            $rowData = $sheet->rangeToArray('A' . $row . ':' . 'P' . $row, NULL, TRUE, FALSE);
            $data = $rowData[0];
            $date = date('Y/m/d H:i:s');
            if($_POST['src']=="importForm")
            {
               if($data[2]=='' && clean($data[11])=='0')
               {
               $blankLine++;
               if($blankLine>20)
                  break;
               continue;
               }
               $blankLine=0;
            
               $sql = "INSERT INTO product VALUES (null,'" . trim($data[2]) . "','" . trim(strtoupper($data[0])) . "','" . trim(vendorCheck($data[1])) . "',
                  '','" . $data[4] . "','" . getStyleCodeVal(clean($data[13])) . "','" . clean($data[6]) . "','" . clean($data[7]) . "','" . clean($data[8]) . "',
                  '" . clean($data[9]) . "','" . clean($data[10]) . "','" . clean($data[11]) . "','" . clean($data[12]) . "',
                  '" . trim($data[5]) . "',''," . $_SESSION['userid'] . "," . clean($data[13]) . ",'".$date."','".$data[14]."','".$data[15]."'
                  ) ON DUPLICATE KEY UPDATE vendor='" . strtoupper($data[0]) . "', vendorCode='" . vendorCheck($data[1]) . "', description='" . $data[4] . "', 
                  itemTypeCode='" . getStyleCodeVal(clean($data[13])) . "', grossWt='" . clean($data[6]) . "',diaWt='" . clean($data[7]) . "',cstoneWt='" . 
                  clean($data[8]) . "',goldWt='" . clean($data[9]) . "',noOfDia='" . clean($data[10]) . "',sellPrice='" . clean($data[11]) . "',curStock='" . 
                  clean($data[12]) . "',ringSize='" . $data[5] . "',styleCode='" . clean($data[13]) . "', comments='".$data[14]."', mu='".$data[15]."' ;";            
            }
            elseif($_POST['src']=="updateForm")
            {
               if(trim($data[2])==""){
               $blankLine++;
               if($blankLine>10)
                  break;
               continue;
               }
               $blankLine=0;
            
               $buildQuery="";
                  $buildQuery="UPDATE product SET";
                  if(!isEmpty($data[0]))
                     $buildQuery=$buildQuery." vendor='".vendorCheck($data[0])."',";
                  if(!isBlank($data[1]))
                     $buildQuery=$buildQuery." vendorCode='".trim($data[1])."',";
                  if(!trim($data[4])=="")
                     $buildQuery=$buildQuery." description='".trim($data[4])."',";
                  if(!isEmpty($data[6]))
                     $buildQuery=$buildQuery." grossWt='".clean($data[6])."',";
                  if(!isEmpty($data[7]))
                     $buildQuery=$buildQuery." diaWt='".clean($data[7])."',";
                  if(!isEmpty($data[8]))
                     $buildQuery=$buildQuery." cstoneWt='".clean($data[8])."',";
                  if(!isEmpty($data[9]))
                     $buildQuery=$buildQuery." goldWt='".clean($data[9])."',";
                  if(!isEmpty($data[10]))
                     $buildQuery=$buildQuery." noOfDia='".clean($data[10])."',";
                  if(!isEmpty($data[11]))
                     $buildQuery=$buildQuery." sellPrice='".clean($data[11])."',";
                  if(!isEmpty($data[12]))
                     $buildQuery=$buildQuery." curStock='".clean($data[12])."',";
                  if(!isEmpty($data[5]))
                     $buildQuery=$buildQuery." ringSize='".clean($data[5])."',";
                  if(!isEmpty($data[13]))
                  {
                     $buildQuery=$buildQuery." itemTypeCode='".getStyleCodeVal($data[13])."',";
                     $buildQuery=$buildQuery." styleCode='".clean($data[13])."',";
                  } 
                  if(!isEmpty($data[14]))
                     $buildQuery=$buildQuery." comments='".$data[14]."',";
                  if(!isEmpty($data[15]))
                     $buildQuery=$buildQuery." mu='".$data[15]."',";
                  if (substr($buildQuery, -1)==","){
                     $sql=substr($buildQuery, 0, -1)." WHERE itemNo='".$data[2]."' ;";
                  }
                  else
                     continue;
            }
            $previousData="";
            $tempData=getCurrentData($data[2]);
            if(count($tempData)>2)
               $previousData=implode("#",$tempData);
            $mysqli=getConn();      
            $result = $mysqli->query($sql);
            $affected=mysqli_affected_rows($mysqli);
            //echo $affected."\r\n";
            if ($affected >= 1)
            {
               if($_POST['src']=="updateForm"){
                  $updateEntries++;
                  writelog(7,"success:2,prevData:".$previousData.",newData:".implode('#', $data)); 
                 continue;
               }
   
               $mysqli1 = getConn();
               $sqlTotal = "Select * from product where itemNo like '" . $data[2] . "';";
               $result1 = mysqli_query($mysqli1, $sqlTotal);
               $totRows = mysqli_num_rows($result1);
               if ($totRows == 1 && $affected==2){
                 $updateEntries++;
                 writelog(7,"success:2,prevData:".$previousData.",newData:".implode('#', $data)); 
               } 
               elseif ($totRows == 1 && $affected==1) {
                  $successEntries++;
                  $previousData="";
                  writelog(7,"success:1,prevData:".$previousData.",newData:".implode("#",getCurrentData($data[2]))); 
               }
               elseif($totRows == 0)
               {
                  $lineError = $lineError . $row . ", ";
                  writelog(7,"success:0,prevData:".$previousData.",newData:".implode('#', $data)); 
               }
            }
            else {
               $lineError = $lineError . $row . ", ";
               writelog(7,"success:0,prevData:".$previousData.",newData:".implode('#', $data)); 
            }
         }
         if ($lineError == "")
         {
            $output['success'] = 1;
            $output['msg'] = "The File has been successfully uploaded!";
         }
         else
         {
            $output['success'] = 0;
            $output['msg'] = 'Entries in line ' . $lineError . ' have error!';
         }
      }
      else
      {
         $output['success'] = 0;
         $output['msg'] = 'Some Ring ItemCode have DASH [-]. Remove Them and reupload file...';
      }
   
   }
   else
   {
      $output['success'] = 0;
      $output['msg'] = 'there was an error uploading your file.';
   }
}
else
{
   $output['success'] = 0;
   $output['msg'] = 'Sorry, You can only upload a .XLSX file!';
}

$output['impact'] = $successEntries;
$output['update'] = $updateEntries;
$output['total'] = $row-2;
echo json_encode($output);
?>
