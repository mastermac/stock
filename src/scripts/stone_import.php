<?php
session_start();
require 'db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';
$fna = "importFile";
if ($_POST['src'] == "updateForm")
   $fna = "updateFile";
$objPHPExcel = new PHPExcel();
$successEntries = 0;
$updateEntries = 0;
$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/stock/src/import/";
$imageFileType = pathinfo(basename($_FILES[$fna]["name"]), PATHINFO_EXTENSION);
$fname = mt_rand();
$target_file = $target_dir . $fname . '.' . $imageFileType;
if ($imageFileType == 'xlsx' || $imageFileType == 'xls') {
   if (move_uploaded_file($_FILES[$fna]["tmp_name"], $target_file)) {
      $inputFileName = '../import/' . $fname . '.' . $imageFileType;
      try {
         $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
         $objReader = PHPExcel_IOFactory::createReader($inputFileType);
         $objReader->setReadDataOnly(true);
         $objPHPExcel = $objReader->load($inputFileName);
      } catch (Exception $e) {
         die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
      }
      $sheet = $objPHPExcel->getSheet(0);
      $highestRow = $sheet->getHighestRow();
      $highestColumn = $sheet->getHighestColumn();
      $lineError = "";
      $date = date('Y/m/d H:i:s');
      $blankLine = 0;
      $NoDashInItemFile = true;
      $output['highest']=$highestRow;
         for($row = 2; $row <= $highestRow; $row++) {
            $previousData = "";
            $rowData = $sheet->rangeToArray('A' . $row . ':' . 'N' . $row, NULL, TRUE, FALSE);
            $data = $rowData[0];
            $datetime = date('Y/m/d H:i:s');
            if ($_POST['src'] == "importForm") {
               if ($data[2] == '' && clean($data[12]) == '0') {
                  $blankLine++;
                  if ($blankLine > 20)
                     break;
                  continue;
               }
               $blankLine = 0;
               $rate = ( (float)clean(trim($data[11])) * (1-((float)clean(trim($data[12])))) );
               $sql = "INSERT INTO `stone-inventory` VALUES (null,'" . trim($data[0]) . "','" . trim($data[1]) . "','" .
                        trim($data[2]) . "', '" . $data[3] . "','" . trim($data[4]) . "','" .
                        clean(trim($data[5])) . "','" . clean(trim($data[6])) . "','" . clean(trim($data[7])) . "','" .
                        clean(trim($data[8])) . "', '" . trim($data[9]) . "','" . trim($data[10]) . "','" .
                        clean(trim($data[11])) . "', '" . clean(trim($data[12])) . "','" . $rate . "','" .
                        round($rate * (float)clean(trim($data[6])),2) . "', '" . round($rate * clean(trim($data[8])),0) . "','" . trim($data[13]) . "','" .
                        $datetime . "', '" . $datetime . "','" . $_SESSION['userid'] . "') ON DUPLICATE KEY UPDATE lot_no='" .
                        trim($data[0]) . "', name='" . trim($data[1]) . 
                        "', size='" . $data[2] . "', shape='" . trim($data[3]) . "', seller='" . trim($data[4]) . 
                        "',purchased_qty='" . clean($data[5]) . "',purchased_wt='" . clean($data[6]) . "',current_qty='" . clean($data[7]) . "',current_wt='" . clean($data[8]) . 
                        "',unit='" . trim($data[9]) . "',box='" . trim($data[10]) . "',cost='" . clean($data[11]) . "',less='" . clean($data[12]) . 
                        "', rate='" . $rate . "', total_amount='" . round($rate * (float)clean(trim($data[6])),2) . "', current_value='" . round($rate * clean(trim($data[8])),0) . "', description='" . 
                        $data[13] . "', last_update_date='" . $datetime . "', userid='" . $_SESSION['userid'] . "' ;";


            } elseif ($_POST['src'] == "updateForm") {
               if (trim($data[2]) == "") {
                  $blankLine++;
                  if ($blankLine > 10)
                     break;
                  continue;
               }
               $blankLine = 0;

               $buildQuery = "";
               $buildQuery = "UPDATE product SET";
               if (!isEmpty($data[0]))
                  $buildQuery = $buildQuery . " vendor='" . vendorCheck($data[0]) . "',";
               if (!isBlank($data[1]))
                  $buildQuery = $buildQuery . " vendorCode='" . trim($data[1]) . "',";
               if (!trim($data[4]) == "")
                  $buildQuery = $buildQuery . " description='" . trim($data[4]) . "',";
               if (!isEmpty($data[7]))
                  $buildQuery = $buildQuery . " grossWt='" . clean($data[7]) . "',";
               if (!isEmpty($data[8]))
                  $buildQuery = $buildQuery . " diaWt='" . clean($data[8]) . "',";
               if (!isEmpty($data[9]))
                  $buildQuery = $buildQuery . " cstoneWt='" . clean($data[9]) . "',";
               if (!isEmpty($data[10]))
                  $buildQuery = $buildQuery . " goldWt='" . clean($data[10]) . "',";
               if (!isEmpty($data[11]))
                  $buildQuery = $buildQuery . " noOfDia='" . clean($data[11]) . "',";
               if (!isEmpty($data[12]))
                  $buildQuery = $buildQuery . " sellPrice='" . clean($data[12]) . "',";
               if (!isEmpty($data[13]))
                  $buildQuery = $buildQuery . " curStock='" . clean($data[13]) . "',";
               if (!isEmpty($data[5]))
                  $buildQuery = $buildQuery . " ringSize='" . clean($data[5]) . "',";
               if (!isEmpty($data[15])) {
                  $buildQuery = $buildQuery . " itemTypeCode='" . getStyleCodeVal($data[15]) . "',";
                  $buildQuery = $buildQuery . " styleCode='" . clean($data[15]) . "',";
               }
               $buildQuery = $buildQuery . " comments='" . $data[19] . "',";
               if (!isEmpty($data[16]))
                  $buildQuery = $buildQuery . " mu='" . $data[16] . "',";
               if (!isEmpty($data[17]))
                  $buildQuery = $buildQuery . " costPrice='" . clean($data[17]) . "',";
               if (!isEmpty($data[6]))
                  $buildQuery = $buildQuery . " dimensions='" . clean($data[6]) . "',";
               if (!isEmpty($data[20]))
                  $buildQuery = $buildQuery . " vendorPO='" . clean($data[20]) . "',";
               if (!isEmpty($data[14]))
                  $buildQuery = $buildQuery . " brand='" . $data[14] . "',";
               if (!isEmpty($data[21]))
                  $buildQuery = $buildQuery . " goldPrice='" . clean($data[21]) . "',";
               if (!isEmpty($data[22]))
                  $buildQuery = $buildQuery . " silverPrice='" . clean($data[22]) . "',";
               if (substr($buildQuery, -1) == ",") {
                  $sql = substr($buildQuery, 0, -1) . " WHERE itemNo='" . $data[2] . "' ;";
               } else
                  continue;
            }
            // $previousData = "";
            // $tempData = getCurrentData($data[2]);
            // if (count($tempData) > 2)
            //    $previousData = implode("#", $tempData);
            $mysqli = getConn();
//            echo $sql;
            $result = $mysqli->query($sql);
            $affected = mysqli_affected_rows($mysqli);
            //echo $affected."\r\n";
            if ($affected >= 1) {
               if ($_POST['src'] == "updateForm") {
                  $updateEntries++;
                  writelog(7, "success:2,prevData:" . $previousData . ",newData:" . implode('#', $data));
                  continue;
               }

               $mysqli1 = getConn();
               $sqlTotal = "Select * from product where itemNo like '" . $data[2] . "';";
               $output['newSql']=$sqlTotal;
               $result1 = mysqli_query($mysqli1, $sqlTotal);
               $totRows = mysqli_num_rows($result1);
               if ($totRows == 1 && $affected == 2) {
                  $updateEntries++;
                  writelog(7, "success:2,prevData:" . $previousData . ",newData:" . implode('#', $data));
               } elseif ($totRows == 1 && $affected == 1) {
                  $successEntries++;
                  $previousData = "";
                  writelog(7, "success:1,prevData:" . $previousData . ",newData:" . implode("#", getCurrentData($data[2])));
               } elseif ($totRows == 0) {
                  $lineError = $lineError . $row . ", ";
                  writelog(7, "success:0,prevData:" . $previousData . ",newData:" . implode('#', $data));
               }
            } else {
               $lineError = $lineError . $row . ", ";
               // writelog(7, "success:0,prevData:" . $previousData . ",newData:" . implode('#', $data));
            }
         }
         if ($lineError == "") {
            $output['success'] = 1;
            $output['msg'] = "The File has been successfully uploaded!";
         } else {
            $output['success'] = 0;
            $output['msg'] = 'Entries in line ' . $lineError . ' have error!';
         }
   } else {
      $output['success'] = 0;
      $output['msg'] = 'there was an error uploading your file.';
   }
} else {
   $output['success'] = 0;
   $output['msg'] = 'Sorry, You can only upload a .XLSX file!';
}

$output['impact'] = $successEntries;
$output['update'] = $updateEntries;
$output['total'] = $row - 2;
$output['sql'] = $sql;
echo json_encode($output);
