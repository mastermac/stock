<?php
session_start();
require 'db_config.php';
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Silver City Jewels")->setLastModifiedBy($_SESSION['username'])->setTitle("Exported Data")->setSubject("Exported Data")->setDescription("This data belongs to Silver City Jewels");

$itemCon = " itemNo like '%" . $_GET["itemNo"] . "%' and";
$usertype = '';
$dtcon = "";
$limitCon="";
if ($_SESSION['usertype'] >= 1){
   $usertype = ' and userid=' . $_SESSION['userid'];
   $limitCon = ' LIMIT 100';
} 
$cond = "";
if ($_GET['styleCode'] != "")
   $cond = " and styleCode = '" . $_GET["styleCode"] . "'";
if ($_GET['curStock'] != "") {
   $stockRange = explode(":", $_GET['curStock']);
   $cond = $cond . " and curStock BETWEEN " . $stockRange[0] . " and " . $stockRange[1];
}
if ($_GET['sellPrice'] != "") {
   $priceRange = explode(":", $_GET['sellPrice']);
   $cond = $cond . " and sellPrice BETWEEN " . $priceRange[0] . " and " . $priceRange[1];
}
if ($_GET['grossWt'] != "") {
   $grossWtRange = explode(":", $_GET['grossWt']);
   $cond = $cond . " and grossWt BETWEEN " . $grossWtRange[0] . " and " . $grossWtRange[1];
}
if ($_GET['sdt'] != "0000-00-00")
   $dtcon = " and dt between '" . $_GET["sdt"] . " 00:00:00' and '" . $_GET["edt"] . " 23:59:59'";

$style = array(
   'alignment' => array(
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
      'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
   )
);

$ExcelHeader = $objPHPExcel->getActiveSheet()->getStyle("A1:W1");

$objPHPExcel->getActiveSheet()
   ->getDefaultStyle()
   ->applyFromArray($style);

$ExcelHeader->getFont()
   ->setBold(true);

$ExcelHeader->getAlignment()
   ->setWrapText(true);

$ExcelHeader->getFill()
   ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
   ->getStartColor()
   ->setRGB('FFFF00');

$ExcelHeader->getBorders()
   ->getAllBorders()
   ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$mysqli = getConn();
$sno = 2;

if ($_GET["itemNoExt"] != "") {
   $itemArr = preg_split('@(?:\s*,\s*|^\s*|\s*$)@', trim($_GET["itemNoExt"]), NULL, PREG_SPLIT_NO_EMPTY);
   $itemStr = implode("','", $itemArr);
   $itemCon = " itemNo in ('" . $itemStr . "') and";
}
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(9);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(7.5);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Vendor')->setCellValue('B1', 'vCode')->setCellValue('C1', 'Item No')->setCellValue('D1', 'ItemPic')->setCellValue('E1', 'Description')->setCellValue('F1', 'Size')->setCellValue('G1', 'Dimensions')->setCellValue('H1', 'gross Wt')->setCellValue('I1', 'dia Wt')->setCellValue('J1', 'cstone Wt')->setCellValue('K1', 'gold Wt')->setCellValue('L1', 'No. of Dia')->setCellValue('M1', 'Sell Price')->setCellValue('N1', 'Qty')->setCellValue('O1', 'Brand')->setCellValue('P1', 'Style Code')->setCellValue('Q1', 'MU')->setCellValue('R1', 'Cost Price')->setCellValue('S1', 'Enter 1')->setCellValue('T1', 'Comments')->setCellValue('U1', 'Vendor PO#')->setCellValue('V1', 'Gold Price')->setCellValue('W1', 'Silver Price');
$sql = "SELECT * FROM product where " . $itemCon . " vendor like '%" . $_GET["vendor"] . "%' and vendorCode like '%" . $_GET["vendorCode"] . "%' and description like '%" . $_GET["description"] . "%' and itemTypeCode like '%" . $_GET["itemTypeCode"] . "%' and diaWt like '%" . $_GET["diaWt"] . "%' and cstoneWt like '%" . $_GET["cstoneWt"] . "%' and goldWt like '%" . $_GET["goldWt"] . "%'" . $cond . " and ringSize like '%" . $_GET["ringSize"] . "%'" . $usertype . $dtcon . " Order By itemNo".$limitCon;
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
   if($_SESSION['usertype']!=0)
   {
      $row['mu']="";
      $row['costPrice']="";
   }
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $sno, $row['vendor'])->setCellValue('B' . $sno, $row['vendorCode'])->setCellValue('C' . $sno, $row['itemNo'])->setCellValue('D' . $sno, $row['itemPic'])->setCellValue('E' . $sno, $row['description'])->setCellValue('F' . $sno, $row['ringSize'])->setCellValue('G' . $sno, $row['dimensions'])->setCellValue('H' . $sno, $row['grossWt'])->setCellValue('I' . $sno, $row['diaWt'])->setCellValue('J' . $sno, $row['cstoneWt'])->setCellValue('K' . $sno, $row['goldWt'])->setCellValue('L' . $sno, $row['noOfDia'])->setCellValue('M' . $sno, $row['sellPrice'])->setCellValue('N' . $sno, $row['curStock'])->setCellValue('O' . $sno, $row['brand'])->setCellValue('P' . $sno, $row['styleCode'])->setCellValue('Q' . $sno, $row['mu'])->setCellValue('R' . $sno, $row['costPrice'])->setCellValue('S' . $sno, '1')->setCellValue('T' . $sno, $row['comments'])->setCellValue('U' . $sno, $row['vendorPO'])->setCellValue('V' . $sno, $row['goldPrice'])->setCellValue('W' . $sno, $row['silverPrice']);
   $objDrawing = new PHPExcel_Worksheet_Drawing();
   $objDrawing->setName('test_img');
   $objDrawing->setDescription('test_img');
   if (file_exists('../../pics/' . $row['itemNo'] . '.JPG')) $objDrawing->setPath('../../pics/' . $row['itemNo'] . '.JPG');
   else $objDrawing->setPath('../../pics/noImage.jpeg');
   $objDrawing->setCoordinates('D' . $sno);
   $objDrawing->setOffsetX(5);
   $objDrawing->setOffsetY(5);
   $objDrawing->setWidth(100);
   $objDrawing->setHeight(100);
   $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
   $objPHPExcel->getActiveSheet()->getRowDimension($sno)->setRowHeight(82.5);
   $objPHPExcel->getActiveSheet()->getStyle('A' . $sno . ':W' . $sno)->getAlignment()->setWrapText(true);
   $sno++;
}
$objPHPExcel->setActiveSheetIndex(0);
$callStartTime = microtime(true);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('export.xlsx');
$data['data'] = 'success';
writelog(6, implode(',', $_GET));
echo json_encode($data);
