
<?php
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

// put it at the beginning because it crashes if you declare $ mysqli before !

?>