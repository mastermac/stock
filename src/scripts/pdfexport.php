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
	$w = array(48, 48, 48,48);
	// Header
	// Data
    $mysqli=getConn();
    $result = $mysqli->query($sql);
    $sno=1;
    $loc="";
    $data=array();
    $index=0;
    $itemsInARow=4;
    $cell_width = 48;  //define cell width
    $cell_height=6;    //define cell height
    while ($row = $result->fetch_assoc())
    {
        if(file_exists('../../pics/' . $row['itemNo'] . '.JPG')){
            $data[$index++]=array($sno,$row['itemNo'],$row['sellPrice'], $row['dimensions']);
            if($index%$itemsInARow==0){
                for($i=0;$i<$itemsInARow;$i++){
                    if (file_exists('../../pics/' . $data[$i][1] . '.JPG'))
                        $loc='../../pics/' . $data[$i][1] . '.JPG';
                    else
                        $loc='../../pics/noImage.jpeg';
                    $this->Cell( $cell_width, $cell_width-5, $this->Image($loc, $this->GetX(), $this->GetY(), 0,$cell_width), 0,0, 'C' );
                }
                $this->Ln();
                $this->SetFont('Arial','',10);
                for($i=0;$i<$itemsInARow;$i++){                
                    $this->Cell($cell_width,6,"".$data[$i][1],0,0,'C'); //Item No
                }
                if($_GET['includeDescription']=="true"){
                    $this->Ln();
                    $current_y = $this->GetY();
                    $current_x = $this->GetX();
                    $this->SetFont('Arial','',10);
                    for($i=0;$i<$itemsInARow;$i++){
                        $this->SetY($current_y, false); 
                        $this->Cell($cell_width,6,$data[$i][3],0,0,"C");
                        $current_x+=$cell_width;
                        $this->SetX($current_x);
                    }
                }
                $this->Ln();
                for($i=0;$i<$itemsInARow;$i++){                
                    $this->Cell($cell_width,6,"$".number_format($data[$i][2]),0,0,'C');
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
            $this->Cell( $cell_width, $cell_width-5, $this->Image($loc, $this->GetX(), $this->GetY(), 0,$cell_width), 0, 0,'C' );
        }
        $this->Ln();
        $this->SetFont('Arial','',10);
        for($i=0;$i<$index;$i++){    
            //echo $data[$i][1];
            $this->Cell($cell_width,6,"".$data[$i][1],0,0,'C'); //Item No
        }
        if($_GET['includeDescription']=="true"){
            $this->Ln();
            $current_y = $this->GetY();
            $current_x = $this->GetX();
            $this->SetFont('Arial','',10);
            for($i=0;$i<$itemsInARow;$i++){                
                $this->SetY($current_y, false); 
                $this->Cell($cell_width,6,$data[$i][3],0,0,"C");
                $current_x+=$cell_width;                           
                $this->SetX($current_x);
                // $this->SetXY($current_x, $current_y); 
            }
        }
        $this->Ln();
        for($i=0;$i<$index;$i++){                
            $this->Cell($cell_width,6,"$".number_format($data[$i][2]),0,0,'C');
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
$sql="";
$limitCon="";

if ($_SESSION['usertype'] >= 1){
    $usertype = ' and userid=' . $_SESSION['userid'];
    $limitCon = ' LIMIT 100';
} 
$cond="";
if($_GET['styleCode']!="")
   $cond=" and styleCode = '" . $_GET["styleCode"] . "'";
if($_GET['curStock']!=""){
	$stockRange=explode(":",$_GET['curStock']);
	$cond=$cond." and curStock BETWEEN " . $stockRange[0] . " and ".$stockRange[1];
}
if($_GET['sellPrice']!=""){
	$priceRange=explode(":",$_GET['sellPrice']);
	$cond=$cond." and sellPrice BETWEEN " . $priceRange[0] . " and ".$priceRange[1];
}
if($_GET['grossWt']!=""){
	$grossWtRange=explode(":",$_GET['grossWt']);
	$cond=$cond." and grossWt BETWEEN " . $grossWtRange[0] . " and ".$grossWtRange[1];
}
if($_GET['sdt']!="0000-00-00")
   $dtcon=" and dt between '".$_GET["sdt"]." 00:00:00' and '".$_GET["edt"]." 23:59:59'";
$sno = 2;

if($_GET["itemNoExt"]!=""){
   $itemArr=preg_split('@(?:\s*,\s*|^\s*|\s*$)@', trim($_GET["itemNoExt"]), NULL, PREG_SPLIT_NO_EMPTY);
   $itemStr=implode("','", $itemArr);

   $itemCon=" itemNo in ('" . $itemStr . "') and";
   $customerDesignCon='';
    if($_GET["customerDesigns"]=="false")
        $customerDesignCon = " (itemNo like '14%' OR itemNo like '18%' OR itemNo like 'SD%') AND ";
$sql = "SELECT * FROM product where ".$customerDesignCon.$itemCon." vendor like '%" . $_GET["vendor"] . "%' and vendorCode like '%" . $_GET["vendorCode"] . "%' and description like '%" . $_GET["description"] . "%' and itemTypeCode like '%" . $_GET["itemTypeCode"] . "%' and diaWt like '%" . $_GET["diaWt"] . "%' and cstoneWt like '%" . $_GET["cstoneWt"] . "%' and goldWt like '%" . $_GET["goldWt"] . "%' ".$cond." and ringSize like '%" . $_GET["ringSize"] . "%'" . $usertype.$dtcon.$limitCon;

$sql = $sql . " Order By dt desc, sno desc";    

$pdf->ImprovedTable($header,$sql);

}
elseif ($_GET["itemNoExt"]=="") {
    $customerDesignCon='';
    if($_GET["customerDesigns"]=="false")
        $customerDesignCon = " (itemNo like '14%' OR itemNo like '18%' OR itemNo like 'SD%') AND ";

$sql = "SELECT * FROM product where ".$customerDesignCon.$itemCon." vendor like '%" . $_GET["vendor"] . "%' and vendorCode like '%" . $_GET["vendorCode"] . "%' and description like '%" . $_GET["description"] . "%' and itemTypeCode like '%" . $_GET["itemTypeCode"] . "%' and diaWt like '%" . $_GET["diaWt"] . "%' and cstoneWt like '%" . $_GET["cstoneWt"] . "%' and goldWt like '%" . $_GET["goldWt"] . "%' ".$cond." and ringSize like '%" . $_GET["ringSize"] . "%'" . $usertype.$dtcon.$limitCon;
$sql = $sql . " Order By dt desc, sno desc";    
$pdf->ImprovedTable($header,$sql);
}
$filename=uniqid().".pdf";
$pdf->Output($filename,'F');
$data['data'] = 'success';
$data['filename']=$filename;
$data['sql']=$sql;
writelog(6,implode(',', $_GET));
echo json_encode($data);

?>
