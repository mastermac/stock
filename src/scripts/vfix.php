<?php
session_start();
require 'db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';

$objPHPExcel = new PHPExcel();
      $inputFileName = 'codes.xlsx';
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
      $lineError = "";
      $date = date('Y/m/d H:i:s');
      $blankLine=0;
      for ($row = 3; $row <= 218; $row++)
      {
         $rowData = $sheet->rangeToArray('A' . $row . ':' . 'D' . $row, NULL, TRUE, FALSE);
         $data = $rowData[0];
         $vid="";
         if($data[3]!="")
            $vid=", vendorCode='".$data[3]."'";
         $sql="UPDATE PRODUCT set vendor='".$data[2]."'".$vid." where vendor='".$data[0]."';";
         $mysqli=getConn();      
         $result = $mysqli->query($sql);
      }
?>
