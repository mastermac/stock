<?php
require 'fpdf.php';
require 'db_config.php';

session_start();

$json = array();
function checkUserType(){
    if ($_SESSION['usertype'] > 1) return ' WHERE userid=' . $_SESSION['userid'];
    return '';
}

function getSettings()
{
    $mysqli = getConn();
    $sql = "SELECT * from settings LIMIT 1";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json[] = $row;

    returnData($json,$sql,$result);
}

function getPurchaseOrders(){
    $mysqli = getConn();
    $sql = "SELECT po_id, COUNT(*) as totalItems FROM `po_items` group by po_id";
    $result = $mysqli->query($sql);
    $dict=[];
    while ($row = $result->fetch_assoc()){
        $dict[$row['po_id']]=$row['totalItems'];
    }
    $sql = "SELECT * from po ".checkUserType()." order by id desc ";
    $result = $mysqli->query($sql);
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $json[$sno]['item_count'] = $dict[$row['id']];
        $sno++;
    }
    returnData($json,$sql,$result);
}

function getPdfId(){
    $mysqli = getConn();
    $sql = "SELECT id FROM `po_so_generated` ORDER BY id DESC LIMIT 1";
    $result = $mysqli->query($sql);
    $id=1;
    while($row = $result->fetch_assoc())
        $id=$row['id']+1;
    return $id;
}

function createPdfId($id){
    $mysqli = getConn();
    $stmt = $mysqli->prepare("INSERT INTO po_so_generated VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id, $_GET['id'], $_SESSION['userid'], date("Y-m-d H:i:s"));
    $stmt->execute();
    $stmt->close();
}


function getPurchaseOrderItems(){
    $mysqli = getConn();
    $sql = "SELECT * from `po_items` where po_id='".$_GET['po_id']."' order by id asc ";
    $result = $mysqli->query($sql);
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    returnData($json,$sql,$result);;
}

function deletePurchaseOrderItem(){
    $mysqli = getConn();
    $sql = "DELETE from `po_items` where po_id=".$_GET['id'];
    $result = $mysqli->query($sql);
    $sql = "DELETE from `po` where id=".$_GET['id'];
    $result = $mysqli->query($sql);
    returnData('',$sql,$result);
}

function createPurchaseOrder(){
    $mysqli = getConn();
    if (empty($_POST['cust_code']))
        $_POST['cust_code'] = '';
    if (empty($_POST['discount']))
        $_POST['discount'] = '';
    
    $stmt = $mysqli->prepare("INSERT INTO `po` VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissssssddssss", $_POST['id'], $_SESSION['userid'], $_POST['cust_code'], date("Y-m-d", strtotime($_POST['entry_date'])), date("Y-m-d", strtotime($_POST['order_date'])), date("Y-m-d", strtotime($_POST['ship_date'])), date("Y-m-d", strtotime($_POST['cancel_date'])), $_POST['type'], date("Y-m-d H:i:s"), $_POST['discount'], round($_POST['total'], 2), $_POST['note'], $_POST['entered_by'], $_POST['ship_via'], $_POST['customer_ref']);
    $stmt->execute();
    $stmt->close();

    $itemArray= json_decode($_POST['items'], true);
    for($i=0;$i<count($itemArray);$i++){
        $sql = "INSERT INTO `po_items` SELECT null, itemNo,vendor,vendorCode,itemPic,description,itemTypeCode,grossWt,diaWt,cstoneWt,goldWt,noOfDia,sellPrice,curStock,ringSize,stoneSize,userid,styleCode,dt,comments,mu,costPrice,dimensions,vendorPO,brand,goldPrice,silverPrice, '".$itemArray[$i]['po_qty']."', 0, '".$_POST['id']."', '".$itemArray[$i]['discount']."', '".$itemArray[$i]['unit_price']."','".$itemArray[$i]['note']."','".date("Y-m-d H:i:s")."' FROM product where itemNo='".$itemArray[$i]['itemNo']."';";
        $mysqli->query($sql);
    }
    $mysqli->close();
    $json['result']='done';
    echo json_encode($json);
}

function updatePurchaseOrder(){
    $mysqli = getConn();

    $stmt = $mysqli->prepare("UPDATE `po` SET cust_code=?, entry_date=?, order_date=?, ship_date=?, cancel_date=?, type=?, last_modified_date=?, discount=?, total=?, note=?, entered_by=?, ship_via=?, customer_ref=? where id=? and userid=?");
    $stmt->bind_param("issssssddssssii", $_POST['cust_code'], date("Y-m-d", strtotime($_POST['entry_date'])), date("Y-m-d", strtotime($_POST['order_date'])), date("Y-m-d", strtotime($_POST['ship_date'])), date("Y-m-d", strtotime($_POST['cancel_date'])), $_POST['type'], date("Y-m-d H:i:s"), $_POST['discount'], round($_POST['total'], 2), $_POST['note'], $_POST['entered_by'], $_POST['ship_via'], $_POST['customer_ref'], $_POST['id'], $_SESSION['userid']);
    $stmt->execute();
    $stmt->close();

    $sql = "SELECT * from `po_items` where po_id=".$_POST['id']." order by id asc";
    $existingPOItems = array();

    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()){
        $existingPOItems[] = $row;
    }
    $itemArray= json_decode($_POST['items'], true);
    for($i=0;$i<count($itemArray);$i++){
        if(isExistingItem($existingPOItems, $itemArray[$i]['itemNo'])){
            $stmt = $mysqli->prepare("UPDATE `po_items` SET po_qty=?, discount=?, unit_price=?, note=?, last_modified_date=? where po_id=? and id=?");
            $stmt->bind_param("sssssss", $itemArray[$i]['po_qty'], $itemArray[$i]['discount'], $itemArray[$i]['unit_price'], $itemArray[$i]['note'], date("Y-m-d H:i:s"), $itemArray[$i]['po_id'], $itemArray[$i]['id']);
            $stmt->execute();
            $stmt->close();
        }
        else{
            $sql = "INSERT INTO `po_items` SELECT null, itemNo,vendor,vendorCode,itemPic,description,itemTypeCode,grossWt,diaWt,cstoneWt,goldWt,noOfDia,sellPrice,curStock,ringSize,stoneSize,userid,styleCode,dt,comments,mu,costPrice,dimensions,vendorPO,brand,goldPrice,silverPrice, '".$itemArray[$i]['po_qty']."', 0, '".$_POST['id']."', '".$itemArray[$i]['discount']."', '".$itemArray[$i]['unit_price']."','".$itemArray[$i]['note']."','".date("Y-m-d H:i:s")."' FROM product where itemNo='".$itemArray[$i]['itemNo']."';";
            $mysqli->query($sql);
        }
    }
    for($i=0;$i<count($existingPOItems);$i++){
        if(!isExistingItem($itemArray, $existingPOItems[$i]['itemNo'])){
            $sql = "DELETE FROM `po_items` WHERE id=".$existingPOItems[$i]['id'].";";
            $mysqli->query($sql);
        }
    }
    $mysqli->close();
    $json['result']='done';
    echo json_encode($json);
}

function isExistingItem($existingItems, $itemNo){
    for($i=0;$i<count($existingItems);$i++){
        if($existingItems[$i]['itemNo']==$itemNo)
            return true;
    }
    return false;
}

function getItemDiamonds($pid, $id){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-diamond` where pl_id=".$pid." AND item_id=".$id." order by id asc ";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}
function getItemStones($pid, $id){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-stone` where pl_id=".$pid." AND item_id=".$id." order by id asc ";
    $result = $mysqli->query($sql);
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}
function getItemMetals($pid, $id){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-metal` where pl_id=".$pid." AND item_id=".$id." order by id asc ";
    $result = $mysqli->query($sql);
    $json = array();
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}
function getItemOthers($pid, $id){
    $mysqli = getConn();
    $sql = "SELECT * from `pl-others` where pl_id=".$pid." AND item_id=".$id." order by id asc ";
    $result = $mysqli->query($sql);
    $sno=0;
    while ($row = $result->fetch_assoc()){
        $json[] = $row;
        $json[$sno]['sno']=$sno+1;
        $sno++;
    }
    return $json;
}

function getStockById(){
    $mysqli = getConn();
    $sql = "SELECT * from `product` where itemNo='".$_GET['itemNo']."'";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json = $row;
    $sql = "SELECT SUM(po_qty-po_qty_done) as itemSum from `po_items` where itemNo='".$_GET['itemNo']."'";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json['onOrder'] = (int)$row['itemSum'];

    returnData($json,$sql,$result);
}

function getNewPurchaseOrderId(){
    $mysqli = getConn();
    $sql = "SELECT SUM(wt) as total FROM `pl-diamond` where pl_id in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
    $sno=0;
    while ($row = $result->fetch_assoc())
        $json['dia_total'] = (float)$row['total'];

    $sql = "SELECT SUM(wt) as total FROM `pl-metal` where pl_id in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json['metal_total'] = (float)$row['total'];

    $sql = "SELECT SUM(wt) as total FROM `pl-stone` where pl_id in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json['stone_total'] = (float)$row['total'];

    $sql = "SELECT SUM(total) as total FROM `pl-items` where pid in (SELECT id FROM packinglist ".checkUserType().")";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
        $json['item_total'] = (float)$row['total'];

    returnData($json,$sql,$result);
}

function returnData($json,$sql,$result){
    $data['data'] = $json;
    $data['sql'] = $sql;
    $data['total'] = mysqli_num_rows($result);
    echo json_encode($data);
}

function updateItemPic()
{
    if (!empty($_FILES['changeItemPic']['name']))
    {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/stock/pics/po/";
        $imageFileType = pathinfo(basename($_FILES["changeItemPic"]["name"]) , PATHINFO_EXTENSION);
        $target_file = $target_dir . $_POST['itemNo'] . '.' . $imageFileType;
        $img = $_FILES['changeItemPic']['tmp_name'];
        $dst = $target_dir . $_POST['itemNo'];
        if (($img_info = getimagesize($img)) === FALSE) die("Image not found or not an image");
        $width = $img_info[0];
        $height = $img_info[1];
        switch ($img_info[2])
        {
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($img);
                break;
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($img);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($img);
                break;
            default:
                die("Unknown filetype");
        }
        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
        imagejpeg($tmp, $dst . ".JPG");
    }
    $json['result']='done';
    echo json_encode($json);
}

$functionType=$_GET['func'];
if(empty($functionType))
    $functionType=$_POST['func'];

switch($functionType){
    case "getStockById": getStockById();
        break;
    case "getPurchaseOrders": getPurchaseOrders();
        break;
    case "getPurchaseOrderItems": getPurchaseOrderItems();
        break;
    case "getNewPurchaseOrderId": getNewPurchaseOrderId();
        break;
    case "deletePurchaseOrderItem": deletePurchaseOrderItem();
        break;
    case "createPurchaseOrder": createPurchaseOrder();
        break;
    case "updatePurchaseOrder": updatePurchaseOrder();
        break;
    case "updateItemPic": updateItemPic();
        break;
    case "generatePO": createPO();
        break;
    case "generateSO": createSO();
        break;
}

function createPO()
{
    $pdf = new FPDF('P', 'mm', 'A4');
    $mysqli = getConn();
    $sql = "SELECT vendor FROM `po_items` WHERE po_id=" . $_GET['id'] . " GROUP BY vendor;";
    $result = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
    while ($row = $result->fetch_assoc()) {
        $vendors[] = $row['vendor'];
    }
    $allFiles = "";
    mysqli_free_result($result);
    foreach ($vendors as $vendor) {
        $pdf = new FPDF('P', 'mm', 'A4');
        $var_id_invoice = $_GET['id_param'];

        $pdf->SetAutoPagebreak(False);
        $pdf->SetMargins(0, 0, 0);

        $pdfID = getPdfId();

        $sql = "SELECT count(*) FROM po_items WHERE vendor='".$vendor."' AND po_id=". $_GET['id'];
        $result = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
        $row_client = mysqli_fetch_row($result);
        mysqli_free_result($result);
        $limit_sup = 12;
        $nb_page = ceil($row_client[0]/$limit_sup);

        $num_page = 1;
        $limit_inf = 0;
        $count = 1;
        while ($num_page <= $nb_page) {
            $pdf->AddPage();
            $pdf->Image('../../pics/masthead.png', 5, 5, 75, 29);

            $pdf->SetXY(120, 5);
            $pdf->SetFont("Arial", "", 12);
            $pdf->Cell(85, 5, 'Page ' . $num_page . '/' . $nb_page, 0, 0, 'R');

            $select = "SELECT * FROM vendor WHERE code=".$vendor;
            $result = mysqli_query($mysqli, $select) or die('SQL error: ' . $select . mysqli_connect_error());
            $vendorName = "";
            while($row = $result->fetch_assoc())
                $vendorName=$row['name'];
            mysqli_free_result($result);
            $num_fact = "To - " . $vendorName;
            $pdf->SetLineWidth(0.1);
            $pdf->SetFillColor(192);
            $pdf->Rect(5, 40, 85, 8, "DF");
            $pdf->SetXY(5, 40);
            $pdf->SetFont("Arial", "B", 12);
            $pdf->MultiCell(85, 8, $num_fact);

            $file_name = "po_" . $pdfID. ".pdf";

            $select = "SELECT * FROM po WHERE id=".$_GET['id'];
            $result = mysqli_query($mysqli, $select) or die('SQL error: ' . $select . mysqli_connect_error());
            $row_client = mysqli_fetch_row($result);
            mysqli_free_result($result);
            $pdf->SetFont('Arial', 'B', 11);
            $x = 120;
            $y = 13;
            $pdf->SetXY($x, $y);

            $pdf->SetLineWidth(0.1);
            $pdf->SetFillColor(192);
            $pdf->Rect($x, $y, 40,   7, "DF");
            $pdf->Rect($x, $y + 9, 40, 7, "DF");
            $pdf->Rect($x, $y + 18, 40, 7, "DF");
            $pdf->Rect($x, $y + 27, 40, 7, "DF");
            $pdf->Rect($x, $y + 36, 40, 7, "DF");
            $pdf->SetFillColor(255);
            $pdf->Rect($x + 45, $y, 40, 7, "DF");
            $pdf->Rect($x + 45, $y + 9, 40, 7, "DF");
            $pdf->Rect($x + 45, $y + 18, 40, 7, "DF");
            $pdf->Rect($x + 45, $y + 27, 40, 7, "DF");
            $pdf->Rect($x + 45, $y + 36, 40, 7, "DF");

            $pdf->SetXY($x, $y);
            $pdf->Cell(40, 7, 'Vendor P.O No', 0, 0, 'C');
            $y += 9;
            $pdf->SetXY($x, $y);
            $pdf->Cell(40, 7, 'Vendor No', 0, 0, 'C');
            $y += 9;
            $pdf->SetXY($x, $y);
            $pdf->Cell(40, 7, 'PO Date', 0, 0, 'C');
            $y += 9;
            $pdf->SetXY($x, $y);
            $pdf->Cell(40, 7, 'Due Date', 0, 0, 'C');
            $y += 9;
            $pdf->SetXY($x, $y);
            $pdf->Cell(40, 7, 'Ship Date', 0, 0, 'C');
            $y += 9;

            $x = 120;
            $y = 13;
            $pdf->SetXY($x + 45, $y);
            $pdf->Cell(40, 7, $pdfID, 0, 0, 'C');
            $y += 9;
            $pdf->SetXY($x + 45, $y);
            $pdf->Cell(40, 7, $vendor, 0, 0, 'C');
            $y += 9;
            $pdf->SetXY($x + 45, $y);
            $pdf->Cell(40, 7, date("m/d/Y"), 0, 0, 'C');
            $y += 9;
            $pdf->SetXY($x + 45, $y);
            $pdf->Cell(40, 7, date("m/d/Y", strtotime($row_client[4])), 0, 0, 'C');
            $y += 9;
            $pdf->SetXY($x + 45, $y);
            $pdf->Cell(40, 7, date("m/d/Y", strtotime($row_client[5])), 0, 0, 'C');
            $y += 9;

            
            // column title
            $pdf->setFillColor(192);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(5, 65.2);
            $pdf->Cell(14.7, 9.7, "S.No", 0, 0, 'C', TRUE);
            $pdf->SetXY(20.2, 65.2);
            $pdf->Cell(24.4, 9.7, "Item #", 0, 0, 'C', TRUE);
            $pdf->SetXY(45.3, 65.2);
            $pdf->Cell(19.4, 9.7, "Image", 0, 0, 'C', TRUE);
            $pdf->SetXY(65.3, 65.2);
            $pdf->Cell(99.4, 9.7, "Item Description", 0, 0, 'L', TRUE);
            $pdf->SetXY(132 + 33.3, 65.2);
            $pdf->Cell(22.4, 9.7, "Vendor #", 0, 0, 'L', TRUE);
            $pdf->SetXY(157 + 31.3, 65.2);
            $pdf->Cell(16.4, 9.7, "Order Qty", 0, 0, 'L', TRUE);

            $pdf->SetFont('Arial', '', 8);
            $y = 67;
            $totalItems = 0;
            $totalQty = 0;
            $sql = "SELECT * FROM po_items WHERE vendor='".$vendor."' AND po_id=". $_GET['id'];
            $sql .= ' LIMIT ' . ($limit_sup) . ' OFFSET ' . (($num_page - 1) * $limit_sup);
            $res = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
            while ($data = mysqli_fetch_assoc($res)) {
                $totalItems +=1;
                $pdf->SetXY(7, $y + 9);
                $pdf->Cell(20, 5, $count, 0, 0, 'L');
                $pdf->SetXY(22, $y + 9);
                $pdf->Cell(25, 5, $data['itemNo'], 0, 0, 'L');
                $pdf->SetXY(47, $y + 9);
                if (file_exists('../../pics/' . $data['itemNo'] . '.JPG'))
                    $loc='../../pics/' . $data['itemNo'] . '.JPG';
                else
                    $loc='../../pics/noImage.jpeg';

                $pdf->Cell(20, 5, $pdf->Image($loc, 47, $y + 9, 0, 16), 0, 0, 'L');
                $pdf->SetXY(66, $y + 9);
                $pdf->Multicell(60+35, 3.5, $data['description']." - ".strtoupper($data['note'])."\nDIA: ".$data['diaWt']."  CLR: ".$data['cstoneWt']."  GKD: ".$data['goldWt']."  GWT: ".$data['grossWt'], 0, 'L');
                $pdf->SetXY(132 + 35, $y + 9);
                $pdf->MultiCell(22, 5, $data['vendorCode'], 0, 'L');
                $pdf->SetXY(157 + 32, $y + 9);
                $pdf->Cell(15, 5, (int)$data['po_qty'] - (int)$data['po_qty_done'], 0, 0, 'R');

                $totalQty += ((int)$data['po_qty'] - (int)$data['po_qty_done']);

                $pdf->Line(5, $y + 24, 205, $y + 24);

                $y += 16;
                $count = $count + 1;
            }
            mysqli_free_result($res);

            $pdf->SetLineWidth(0.1);
            $lineHeight = $y+10-67;
            $pdf->Rect(5, 65, 200, $lineHeight, "D");
            $pdf->Line(5, 75, 205, 75);
            $pdf->Line(20, 65, 20, $lineHeight+65);
            $pdf->Line(45, 65, 45, $lineHeight+65);
            $pdf->Line(65, 65, 65, $lineHeight+65);
            $pdf->Line(130 + 35, 65, 130 + 35, $lineHeight+65);
            $pdf->Line(155 + 33, 65, 155 + 33, $lineHeight+65);
            $pdf->Line(175 + 30, 65, 175 + 30, $lineHeight+65);
            
            if ($num_page == $nb_page) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(5, $lineHeight+65+5);
                $pdf->Cell(38, 5, "Total : ".$totalItems. " Style(s)", 0, 0, 'R');
                $pdf->Cell(40, 5, $totalQty." Piece(s)", 0, 0, 'R');
            }

            $num_page++;
            $limit_inf += $limit_sup;
        }
        
        $pdf->Output($file_name, 'F');
        createPdfId($pdfID);
        $allFiles = $allFiles.",".$file_name;
    }
    $data['data'] = 'success';
    $data['filename'] = $allFiles;
    $data['sql'] = $sql;
    echo json_encode($data);
}

function createSO()
{
    $pdf = new FPDF('P', 'mm', 'A4');

    $mysqli = getConn();

    $pdfID = getPdfId();

    $pdf->SetAutoPagebreak(False);
    $pdf->SetMargins(0, 0, 0);

    $sql = "SELECT count(*) FROM po_items WHERE po_id=". $_GET['id'];
    $result = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
    $row_client = mysqli_fetch_row($result);
    mysqli_free_result($result);
    $limit_sup = 12;
    $nb_page = ceil($row_client[0]/$limit_sup);

    $num_page = 1;
    $limit_inf = 0;
    $count = 1;
    $inPage=0;
    $printTotalOnNextPage = false;
    
    $file_name = "so_" . $pdfID. ".pdf";

    while ($num_page <= $nb_page) {
        $pdf->AddPage();

        $pdf->Image('../../pics/masthead.png', 5, 5, 60, 23);

        $select = "SELECT * FROM po WHERE id=". $_GET['id'];
        $result = mysqli_query($mysqli, $select) or die('SQL error: ' . $select . mysqli_connect_error());
        $row = mysqli_fetch_row($result);
        $poResults = $row;
        mysqli_free_result($result);

        $field_date = date_create($row[0]);
        $year = date_format($field_date, 'Y');
        $num_fact = "Ship To";
        $pdf->SetLineWidth(0.1);
        $pdf->SetFillColor(192);
        $pdf->Rect(5, 30, 60, 6, "DF");
        $pdf->SetXY(5, 30);
        $pdf->SetFont("Arial", "B", 9);
        $pdf->MultiCell(60, 6, $num_fact);
        $pdf->SetFillColor(256);
        $pdf->Rect(5, 38, 60, 25, "DF");
        $pdf->SetXY(5, 38);
        $pdf->SetFont("Arial", "B", 8); //$pdf->MultiCell( 60, 6, $row[2]) ;

        $pdf->SetXY(70, 38);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->MultiCell(30, 6, 'Entered By');
        $pdf->SetXY(70, 44);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->MultiCell(30, 6, 'Ship Via');
        $pdf->SetXY(70, 50);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->MultiCell(30, 6, 'Customer Ref#');
        $pdf->SetXY(70, 5);
        $pdf->SetFont("Arial", "B", 14);
        $pdf->Cell(65, 6, 'SALESORDER', 0, 0, 'C');


        $pdf->SetXY(100, 38);
        $pdf->SetFont("Arial", "", 10);
        $pdf->MultiCell(35, 6, $row[12]);
        $pdf->SetXY(100, 44);
        $pdf->SetFont("Arial", "", 10);
        $pdf->MultiCell(35, 6, $row[13]);
        $pdf->SetXY(100, 50);
        $pdf->SetFont("Arial", "", 10);
        $pdf->MultiCell(35, 6, $row[14]);

        $pdf->Rect(142, 19, 63, 21, "DF");
        $pdf->Rect(142, 42, 63, 21, "DF");

        $pdf->SetXY(144, 21);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->MultiCell(30, 6, 'S.O. NO. :');
        $pdf->SetXY(144, 27);
        $pdf->MultiCell(30, 6, 'CUST. CODE :');
        $pdf->SetXY(144, 33);
        $pdf->MultiCell(30, 6, 'PAGE NO. :');

        $pdf->SetXY(144, 44);
        $pdf->MultiCell(30, 6, 'Order Date :');
        $pdf->SetXY(144, 50);
        $pdf->MultiCell(30, 6, 'Ship Date :');
        $pdf->SetXY(144, 56);
        $pdf->MultiCell(30, 6, 'Cancel Date :');

        $pdf->SetXY(172, 21);
        $pdf->SetFont("Arial", "", 10);
        $pdf->MultiCell(35, 6, $row[0]);
        $pdf->SetXY(172, 27);
        $pdf->MultiCell(35, 6, $row[2]);
        $pdf->SetXY(172, 33);
        $pdf->MultiCell(35, 6, $num_page);

        $pdf->SetXY(172, 44);
        $pdf->MultiCell(35, 6, date("m/d/Y", strtotime($row[4])));
        $pdf->SetXY(172, 50);
        $pdf->MultiCell(35, 6, date("m/d/Y", strtotime($row[5])));
        $pdf->SetXY(172, 56);
        $pdf->MultiCell(35, 6, date("m/d/Y", strtotime($row[6])));

        $pdf->SetFont('Arial', 'B', 11);
        $x = 120;
        $y = 13;
        $pdf->SetXY($x, $y);

        // column title
        $pdf->setFillColor(192);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetXY(5, 65.2);
        $pdf->Cell(10.7, 9.7, "S.No", 0, 0, 'C', TRUE);
        $pdf->SetXY(16.2, 65.2);
        $pdf->Cell(18.4, 9.7, "STYLE", 0, 0, 'C', TRUE);
        $pdf->SetXY(35.3, 65.2);
        $pdf->Cell(17.3, 9.7, "", 0, 0, 'C', TRUE);
        $pdf->SetXY(53.3, 65.2);
        $pdf->Cell(76.4, 9.7, "DESCRIPTION", 0, 0, 'L', TRUE);
        $pdf->SetXY(130.3, 65.2);
        $pdf->Cell(11.4, 9.7, "SIZE", 0, 0, 'C', TRUE);
        $pdf->SetXY(142.3, 65.2);
        $pdf->Cell(9.4, 9.7, "QTY", 0, 0, 'C', TRUE);
        $pdf->SetXY(152.3, 65.2);
        $pdf->Cell(14.4, 9.7, "TAGPRICE", 0, 0, 'L', TRUE);
        $pdf->SetXY(167.3, 65.2);
        $pdf->Cell(10.4, 9.7, "DISC %", 0, 0, 'C', TRUE);
        $pdf->SetXY(178.3, 65.2);
        $pdf->Cell(14.4, 9.7, "UNT PRICE", 0, 0, 'L', TRUE);
        $pdf->SetXY(193.3, 65.2);
        $pdf->Cell(11.4, 9.7, "TOTAL", 0, 0, 'L', TRUE);

        $pdf->SetFont('Arial', '', 7.5);
        $y = 67;
        $sql = "SELECT * FROM po_items WHERE po_id=". $_GET['id'];
        $sql .= ' LIMIT ' . ($limit_sup) . ' OFFSET ' . (($num_page - 1) * $limit_sup);
        $res = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
        $totalQty=0;
        $inPage=0;
        while ($data = mysqli_fetch_assoc($res)) {
            if (file_exists('../../pics/' . $data['itemNo'] . '.JPG'))
                $loc='../../pics/' . $data['itemNo'] . '.JPG';
            else
                $loc='../../pics/noImage.jpeg';

            $pdf->SetXY(6, $y + 9);
            $pdf->MultiCell(20, 5, $count, 0);
            $pdf->SetXY(16, $y + 9);
            $pdf->MultiCell(20, 5, $data['itemNo'], 0);
            $pdf->SetXY(35, $y + 9);
            $pdf->Cell(20, 5, $pdf->Image($loc, 36, $y + 9, 0, 16), 0, 0, 'L');
            $pdf->SetXY(53, $y + 9);
            $pdf->Multicell(76, 3.5, $data['description']." - ".strtoupper($data['note'])."\nGWT: ".$data['grossWt'].", D: ".$data['diaWt'].", C: ".$data['cstoneWt'].", G: ".$data['goldWt'], 0, 'L');
            $pdf->SetXY(130, $y + 9);
            $pdf->MultiCell(11.4, 5, $data['ringSize']);
            $pdf->SetXY(142, $y + 9);
            $pdf->MultiCell(9.4, 5, (int)$data['po_qty'] - (int)$data['po_qty_done'], 0, 'R');
            $pdf->SetXY(152, $y + 9);
            $pdf->MultiCell(14.4, 5, $data['sellPrice'], 0, 'R');
            $pdf->SetXY(167, $y + 9);
            $pdf->MultiCell(10.4, 5, $data['discount'], 0, 'R');
            $pdf->SetXY(178, $y + 9);
            $pdf->MultiCell(14.4, 5, $data['unit_price'], 0, 'R');
            $pdf->SetXY(193, $y + 9);
            $pdf->MultiCell(11.4, 5, ((int)$data['po_qty'] - (int)$data['po_qty_done'])*((int)$data['unit_price']), 0, 'R');

            $pdf->Line(5, $y + 24, 205, $y + 24);
            $totalQty += ((int)$data['po_qty'] - (int)$data['po_qty_done']);
            $y += 16;
            $count = $count + 1;
            $inPage+=1;
        }
        mysqli_free_result($res);

        $pdf->SetLineWidth(0.1);

        $lineHeight = $y+10-67;
        $pdf->Rect(5, 65, 200, $lineHeight, "D");
        $pdf->Line(5, 75, 205, 75);
        $pdf->Line(16, 65, 16, $lineHeight+65);
        $pdf->Line(35, 65, 35, $lineHeight+65);
        $pdf->Line(53, 65, 53, $lineHeight+65);
        $pdf->Line(130, 65, 130, $lineHeight+65);
        $pdf->Line(142, 65, 142, $lineHeight+65);
        $pdf->Line(152, 65, 152, $lineHeight+65);
        $pdf->Line(167, 65, 167, $lineHeight+65);
        $pdf->Line(178, 65, 178, $lineHeight+65);
        $pdf->Line(193, 65, 193, $lineHeight+65);

        if ($num_page == $nb_page && $inPage<10) {
            $pdf->SetLineWidth(0.1);
            $pdf->Rect(5, 221+20, 120, 24, "D");
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetXY(6, 226+20);
            $pdf->MultiCell(118+20, 3, "1) Prices are good for next 24 hours from the date of Sales Order.\n2) Customer need to check Details of above sales order and send confirmation by email only.\n3) All SPECIAL ORDERS CANNOT BE CANCELLED OR RETURNED AFTER CUSTOMER'S CONFIRMATION.\n4) All Special order require 75% down payment, remaining on the day of shipping.\n5) Prices, weight may vary as shown in above quote. upto 5% variation is possible", 0, 'L');

            $pdf->Rect(130, 221+20, 75, 24, "D");
            $pdf->Line(167.5, 221+20, 167.5, 245+20);
            $pdf->Line(130, 227+20, 205, 227+20);
            $pdf->Line(130, 233+20, 205, 233+20);
            $pdf->Line(130, 239+20, 205, 239+20);
            // the titles
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(130, 221+20);
            $pdf->Cell(37.5, 6, "Total Qty:", 0, 0, 'L');
            $pdf->SetXY(130, 227+20);
            $pdf->Cell(37.5, 6, "Subtotal:", 0, 0, 'L');
            $pdf->SetXY(130, 233+20);
            $pdf->Cell(37.5, 6, "Global Disc (".$row[9]."%)", 0, 0, 'L');
            $pdf->SetXY(130, 239+20);
            $pdf->Cell(37.5, 6, "Global Total:", 0, 0, 'L');

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(167.5, 221+20);
            $pdf->Cell(37.5, 6, $totalQty, 0, 0, 'R');
            $pdf->SetXY(167.5, 227+20);
            $pdf->Cell(37.5, 6, number_format((float)$row[10]/(1- ((float)$row[9]*.01))), 0, 0, 'R');
            $pdf->SetXY(167.5, 233+20);
            $pdf->Cell(37.5, 6, number_format((float)$row[10] * (float)$row[9]*.01), 0, 0, 'R');
            $pdf->SetXY(167.5, 239+20);
            $pdf->Cell(37.5, 6, number_format((float)$row[10]), 0, 0, 'R');
            $printTotalOnNextPage = false;
        }
        else{
            $printTotalOnNextPage = true;
        }

        // **************************
        // footer
        // **************************
        $pdf->SetLineWidth(0.1);
        $pdf->Rect(5, 270, 200, 6, "D");
        $pdf->SetXY(1, 270);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell($pdf->GetPageWidth(), 7, "All above prices are indicative, prices may vary depending on gold price or final weight of the products.", 0, 0, 'C');

        $num_page++;
        $limit_inf += $limit_sup;
    }
    if($printTotalOnNextPage == true){
        $pdf->AddPage();

        $pdf->Image('../../pics/masthead.png', 5, 5, 60, 23);

        $select = "SELECT * FROM po WHERE id=". $_GET['id'];
        $result = mysqli_query($mysqli, $select) or die('SQL error: ' . $select . mysqli_connect_error());
        $row = mysqli_fetch_row($result);
        $poResults = $row;
        mysqli_free_result($result);

        $field_date = date_create($row[0]);
        $year = date_format($field_date, 'Y');
        $num_fact = "Ship To";
        $pdf->SetLineWidth(0.1);
        $pdf->SetFillColor(192);
        $pdf->Rect(5, 30, 60, 6, "DF");
        $pdf->SetXY(5, 30);
        $pdf->SetFont("Arial", "B", 9);
        $pdf->MultiCell(60, 6, $num_fact);
        $pdf->SetFillColor(256);
        $pdf->Rect(5, 38, 60, 25, "DF");
        $pdf->SetXY(5, 38);
        $pdf->SetFont("Arial", "B", 8); //$pdf->MultiCell( 60, 6, $row[2]) ;

        $pdf->SetXY(70, 38);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->MultiCell(30, 6, 'Entered By');
        $pdf->SetXY(70, 44);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->MultiCell(30, 6, 'Ship Via');
        $pdf->SetXY(70, 50);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->MultiCell(30, 6, 'Customer Ref#');
        $pdf->SetXY(70, 5);
        $pdf->SetFont("Arial", "B", 14);
        $pdf->Cell(65, 6, 'SALESORDER', 0, 0, 'C');


        $pdf->SetXY(100, 38);
        $pdf->SetFont("Arial", "", 10);
        $pdf->MultiCell(35, 6, $row[12]);
        $pdf->SetXY(100, 44);
        $pdf->SetFont("Arial", "", 10);
        $pdf->MultiCell(35, 6, $row[13]);
        $pdf->SetXY(100, 50);
        $pdf->SetFont("Arial", "", 10);
        $pdf->MultiCell(35, 6, $row[14]);

        $pdf->Rect(142, 19, 63, 21, "DF");
        $pdf->Rect(142, 42, 63, 21, "DF");

        $pdf->SetXY(144, 21);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->MultiCell(30, 6, 'S.O. NO. :');
        $pdf->SetXY(144, 27);
        $pdf->MultiCell(30, 6, 'CUST. CODE :');
        $pdf->SetXY(144, 33);
        $pdf->MultiCell(30, 6, 'PAGE NO. :');

        $pdf->SetXY(144, 44);
        $pdf->MultiCell(30, 6, 'Order Date :');
        $pdf->SetXY(144, 50);
        $pdf->MultiCell(30, 6, 'Ship Date :');
        $pdf->SetXY(144, 56);
        $pdf->MultiCell(30, 6, 'Cancel Date :');

        $pdf->SetXY(172, 21);
        $pdf->SetFont("Arial", "", 10);
        $pdf->MultiCell(35, 6, $row[0]);
        $pdf->SetXY(172, 27);
        $pdf->MultiCell(35, 6, $row[2]);
        $pdf->SetXY(172, 33);
        $pdf->MultiCell(35, 6, $num_page);

        $pdf->SetXY(172, 44);
        $pdf->MultiCell(35, 6, date("m/d/Y", strtotime($row[4])));
        $pdf->SetXY(172, 50);
        $pdf->MultiCell(35, 6, date("m/d/Y", strtotime($row[5])));
        $pdf->SetXY(172, 56);
        $pdf->MultiCell(35, 6, date("m/d/Y", strtotime($row[6])));


        $pdf->SetLineWidth(0.1);
        $pdf->Rect(5, 221+20, 120, 24, "D");
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(6, 226+20);
        $pdf->MultiCell(118+20, 3, "1) Prices are good for next 24 hours from the date of Sales Order.\n2) Customer need to check Details of above sales order and send confirmation by email only.\n3) All SPECIAL ORDERS CANNOT BE CANCELLED OR RETURNED AFTER CUSTOMER'S CONFIRMATION.\n4) All Special order require 75% down payment, remaining on the day of shipping.\n5) Prices, weight may vary as shown in above quote. upto 5% variation is possible", 0, 'L');

        $pdf->Rect(130, 221+20, 75, 24, "D");
        $pdf->Line(167.5, 221+20, 167.5, 245+20);
        $pdf->Line(130, 227+20, 205, 227+20);
        $pdf->Line(130, 233+20, 205, 233+20);
        $pdf->Line(130, 239+20, 205, 239+20);
        // the titles
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(130, 221+20);
        $pdf->Cell(37.5, 6, "Total Qty:", 0, 0, 'L');
        $pdf->SetXY(130, 227+20);
        $pdf->Cell(37.5, 6, "Subtotal:", 0, 0, 'L');
        $pdf->SetXY(130, 233+20);
        $pdf->Cell(37.5, 6, "Global Disc (".$row[9]."%)", 0, 0, 'L');
        $pdf->SetXY(130, 239+20);
        $pdf->Cell(37.5, 6, "Global Total:", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(167.5, 221+20);
        $pdf->Cell(37.5, 6, $totalQty, 0, 0, 'R');
        $pdf->SetXY(167.5, 227+20);
        $pdf->Cell(37.5, 6, number_format((float)$row[10]/(1- ((float)$row[9]*.01))), 0, 0, 'R');
        $pdf->SetXY(167.5, 233+20);
        $pdf->Cell(37.5, 6, number_format((float)$row[10] * (float)$row[9]*.01), 0, 0, 'R');
        $pdf->SetXY(167.5, 239+20);
        $pdf->Cell(37.5, 6, number_format((float)$row[10]), 0, 0, 'R');

        // **************************
        // footer
        // **************************
        $pdf->SetLineWidth(0.1);
        $pdf->Rect(5, 270, 200, 6, "D");
        $pdf->SetXY(1, 270);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell($pdf->GetPageWidth(), 7, "All above prices are indicative, prices may vary depending on gold price or final weight of the products.", 0, 0, 'C');

    }
    $pdf->Output($file_name, 'F');
    createPdfId($pdfID);
    $data['data'] = 'success';
    $data['filename'] = $file_name;
    $data['sql'] = $sql;
    echo json_encode($data);
}

