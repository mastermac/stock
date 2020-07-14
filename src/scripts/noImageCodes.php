<?php
session_start();
require 'db_config.php';
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Silver City Jewels")->setLastModifiedBy($_SESSION['username'])->setTitle("Exported Data")->setSubject("Exported Data")->setDescription("This data belongs to Silver City Jewels");
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Item Code')->setCellValue('B1', 'Vendor Code');
$objPHPExcel->getActiveSheet()->getStyle("A1:B1")->getFont()->setBold(true);
if ($_SESSION['usertype'] >= 1) $usertype = ' where userid=' . $_SESSION['userid'];
else $usertype="";
$sql = "SELECT * FROM product" . $usertype . " Order By vendor desc;";
$mysqli=getConn();      
$result = $mysqli->query($sql);
$sno = 2;
while ($row = $result->fetch_assoc())
{
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/stock/pics/".$row['itemNo'].".JPG")) {
		continue;
	} else {
   		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $sno, $row['itemNo'])->setCellValue('B' . $sno, $row['vendor']);
   		$sno++;
	}
}
$objPHPExcel->setActiveSheetIndex(0);
$callStartTime = microtime(true);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('noImageCodes.xlsx');
$data['data'] = 'success';
writelog(6,implode(',', $_GET));
echo json_encode($data);
?>

