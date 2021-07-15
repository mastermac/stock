
<?php
require('fpdf.php');

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

?>