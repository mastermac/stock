<?php
//unlink("test.pdf");
session_start();
require 'db_config.php';
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once 'fpdf.php';
class PDF extends FPDF
{
function Header()
{
    $this->Image('../../pics/bglogo.jpg',80,2);
}
// Better table
function ImprovedTable($header,$sql)
{
	// Column widths
	$w = array(65, 65, 65);
	// Header
	// Data
    $mysqli=getConn();
    $result = $mysqli->query($sql);
    $sno=1;
    $loc="";
    $data=array();
    $index=0;
    while ($row = $result->fetch_assoc())
    {
        if(file_exists('../../pics/' . $row['itemNo'] . '.JPG')){
            $data[$index++]=array($sno,$row['itemNo'],$row['sellPrice']);
            if($index%3==0){
                for($i=0;$i<3;$i++){
                    if (file_exists('../../pics/' . $data[$i][1] . '.JPG'))
                        $loc='../../pics/' . $data[$i][1] . '.JPG';
                    else
                        $loc='../../pics/noImage.jpeg';
                    $this->Cell( 65, 35, $this->Image($loc, $this->GetX(), $this->GetY(), 0,35), 0, 'C' );
                }
                $this->Ln();
                for($i=0;$i<3;$i++){                
                    $this->Cell(65,6,"Item No: ".$data[$i][1],0,'L');
                }
                $this->Ln();
                for($i=0;$i<3;$i++){                
                    $this->Cell(65,6,"Price: $".number_format($data[$i][2]),0,'L');
                }
                $this->Ln();
                $index=0;
                $data=array();
            }
            $sno++;
            if($sno%15==1){
                $this->AddPage();
                $this->SetY(25);
            }
        }
    }
    if($index>0){
        for($i=0;$i<$index;$i++){
            if (file_exists('../../pics/' . $data[$i][1] . '.JPG'))
                $loc='../../pics/' . $data[$i][1] . '.JPG';
            else
                $loc='../../pics/noImage.jpeg';
            // $this->Cell( 65, 35, $this->Image($loc, $this->GetX(), $this->GetY(), 0,35), 'T', 'C' );
            $this->Cell( 65, 35, $this->Image($loc, $this->GetX(), $this->GetY(), 0,35), 0, 'C' );
        }
        $this->Ln();
        for($i=0;$i<$index;$i++){                
            $this->Cell(65,6,"Item No: ".$data[$i][1],0,'L');
        }
        $this->Ln();
        for($i=0;$i<$index;$i++){                
            $this->Cell(65,6,"Price: $".number_format($data[$i][2]),0,'L');
        }
        $this->Ln();
        $index=0;
        $data=array();
    }
	// Closing line
	//$this->Cell(195,0,'','T');
}


}

$pdf = new PDF();
// Column headings
$header = array('', '', '');
// Data loading
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->SetY(25);

$itemCon=" itemNo like '%" . $_GET["itemNo"] . "%' and";
$usertype = '';
$dtcon="";
if ($_SESSION['usertype'] == 1) $usertype = ' and userid=' . $_SESSION['userid'];
$cond="";
if($_GET['styleCode']!="")
   $cond=" and styleCode = '" . $_GET["styleCode"] . "'";
if($_GET['curStock']!="")
   $cond=$cond." and curStock ='" . $_GET["curStock"] . "'";
if($_GET['sdt']!="0000-00-00")
   $dtcon=" and dt between '".$_GET["sdt"]." 00:00:00' and '".$_GET["edt"]." 23:59:59'";
$sno = 2;

if($_GET["itemNoExt"]!=""){
   $itemArr=preg_split('@(?:\s*,\s*|^\s*|\s*$)@', trim($_GET["itemNoExt"]), NULL, PREG_SPLIT_NO_EMPTY);
   $itemStr=implode("','", $itemArr);

   $itemCon=" itemNo in ('" . $itemStr . "') and";

$sql = "SELECT * FROM product where ".$itemCon." vendor like '%" . $_GET["vendor"] . "%' and vendorCode like '%" . $_GET["vendorCode"] . "%' and description like '%" . $_GET["description"] . "%' and itemTypeCode like '%" . $_GET["itemTypeCode"] . "%' and grossWt like '%" . $_GET["grossWt"] . "%' and diaWt like '%" . $_GET["diaWt"] . "%' and cstoneWt like '%" . $_GET["cstoneWt"] . "%' and goldWt like '%" . $_GET["goldWt"] . "%' and sellPrice like '%" . $_GET["sellPrice"] . "%'".$cond." and ringSize like '%" . $_GET["ringSize"] . "%'" . $usertype.$dtcon . " Order By itemNo LIMIT " . $_GET["perPage"];
$pdf->ImprovedTable($header,$sql);

}
elseif ($_GET["itemNoExt"]=="") {
$sql = "SELECT * FROM product where ".$itemCon." vendor like '%" . $_GET["vendor"] . "%' and vendorCode like '%" . $_GET["vendorCode"] . "%' and description like '%" . $_GET["description"] . "%' and itemTypeCode like '%" . $_GET["itemTypeCode"] . "%' and grossWt like '%" . $_GET["grossWt"] . "%' and diaWt like '%" . $_GET["diaWt"] . "%' and cstoneWt like '%" . $_GET["cstoneWt"] . "%' and goldWt like '%" . $_GET["goldWt"] . "%' and sellPrice like '%" . $_GET["sellPrice"] . "%'".$cond." and ringSize like '%" . $_GET["ringSize"] . "%'" . $usertype.$dtcon . " Order By itemNo LIMIT " . $_GET["perPage"];
$pdf->ImprovedTable($header,$sql);

// $result = $mysqli->query($sql);
// while ($row = $result->fetch_assoc())
// {
//    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $sno, $row['vendor'])->setCellValue('B' . $sno, $row['vendorCode'])->setCellValue('C' . $sno, $row['itemNo'])->setCellValue('D' . $sno, $row['itemPic'])->setCellValue('E' . $sno, $row['description'])->setCellValue('F' . $sno, $row['ringSize'])->setCellValue('G' . $sno, $row['grossWt'])->setCellValue('H' . $sno, $row['diaWt'])->setCellValue('I' . $sno, $row['cstoneWt'])->setCellValue('J' . $sno, $row['goldWt'])->setCellValue('K' . $sno, $row['noOfDia'])->setCellValue('L' . $sno, '$'.$row['sellPrice'])->setCellValue('M' . $sno, $row['curStock'])->setCellValue('N' . $sno, $row['styleCode']);
//    $objDrawing = new PHPExcel_Worksheet_Drawing();
//    $objDrawing->setName('test_img');
//    $objDrawing->setDescription('test_img');
//    if (file_exists('../../pics/' . $row['itemNo'] . '.JPG')) $objDrawing->setPath('../../pics/' . $row['itemNo'] . '.JPG');
//    else $objDrawing->setPath('../../pics/noImage.jpeg');
//    $objDrawing->setCoordinates('D' . $sno);
//    $objDrawing->setOffsetX(5);
//    $objDrawing->setOffsetY(5);
//    $objDrawing->setWidth(100);
//    $objDrawing->setHeight(100);
//    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
//    $objPHPExcel->getActiveSheet()->getRowDimension($sno)->setRowHeight(82.5);
//    $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setWrapText(true);
//    $sno++;
// }
}
$filename=uniqid().".pdf";
$pdf->Output($filename,'F');
$data['data'] = 'success';
$data['filename']=$filename;
writelog(6,implode(',', $_GET));
echo json_encode($data);

?>
