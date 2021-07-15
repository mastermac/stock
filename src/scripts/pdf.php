
<?php

//
// example of an invoice with mysqli
// manage the multi-page
// Ver 1.0 THONGSOUME Jean-Paul
//


require('fpdf.php');

function createPO()
{
    // put it at the beginning because it crashes if you declare $ mysqli before !
    $pdf = new FPDF('P', 'mm', 'A4');

    // declare $ mysqli after !
    $mysqli = getConn();
    // FORCE UTF-8
    // mysqli_query($mysqli, "SET NAMES UTF8") ;


    $var_id_invoice = $_GET['id_param'];

    // we add the 2 cm at the bottom
    $pdf->SetAutoPagebreak(False);
    $pdf->SetMargins(0, 0, 0);

    // number of pages for multi-pages : 18 lines

    $sql = 'select count(*) FROM vendor';
    $result = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
    $row_client = mysqli_fetch_row($result);
    mysqli_free_result($result);
    $nb_page = $row_client[0];
    $sql = 'select abs(FLOOR(-' . $nb_page . '/12))';
    $result = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
    $row_client = mysqli_fetch_row($result);
    mysqli_free_result($result);
    $nb_page = $row_client[0];

    $num_page = 1;
    $limit_inf = 0;
    $limit_sup = 12;
    $count = 1;
    while ($num_page <= $nb_page) {
        $pdf->AddPage();

        // logo : 80 wide and 55 high

        $pdf->Image('../../pics/masthead.png', 5, 5, 75, 29);

        // page number in the upper right corner

        $pdf->SetXY(120, 5);
        $pdf->SetFont("Arial", "", 12);
        $pdf->Cell(85, 5, 'Page ' . $num_page . '/' . $nb_page, 0, 0, 'R');

        // invoice number, due date, payment and obs.

        $select = 'select * FROM vendor';
        $result = mysqli_query($mysqli, $select) or die('SQL error: ' . $select . mysqli_connect_error());
        $row = mysqli_fetch_row($result);
        mysqli_free_result($result);

        $field_date = date_create($row[0]);
        $year = date_format($field_date, 'Y');
        $num_fact = "To - " . str_pad($row[2], 4, '0', STR_PAD_LEFT);
        $pdf->SetLineWidth(0.1);
        $pdf->SetFillColor(192);
        $pdf->Rect(5, 40, 85, 8, "DF");
        $pdf->SetXY(5, 40);
        $pdf->SetFont("Arial", "B", 12);
        $pdf->MultiCell(85, 8, $num_fact);

        // final file name
        $file_name = "fact_" . $year . '-' . str_pad($row[1], 4, '0', STR_PAD_LEFT) . ".pdf";

        // invoice date
        // $field_date = date_create($row[0]); $date_fact = date_format($field_date, 'd/m/Y') ;
        // $pdf->SetFont('Arial','',11); $pdf->SetXY( 122, 30 ) ;
        // $pdf->Cell( 60, 8, "MY CITY, on " . $date_fact, 0, 0, '') ;

        // if last page then display total
        if ($num_page == $nb_page) {
            // the totals, we display only the HT. the frame after the lines, starts at 213
            $pdf->SetLineWidth(0.1);
            $pdf->SetFillColor(192);
            $pdf->Rect(5, 213, 90, 8, "DF");
            // HT, VAT and TTC are calculated after
            $number_format_francais = "Total HT: " . number_format($row[3], 2, ',', ' ') . " €";
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(95, 213);
            $pdf->Cell(63, 8, $number_format_english, 0, 0, 'C');
            // at the bottom right
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(181, 227);
            $pdf->Cell(24, 6, number_format($row[3], 2, ',', ' '), 0, 0, 'R');

            // vertical line total frame, 8 high -> 213 + 8 = 221
            $pdf->Rect(5, 213, 200, 8, "D");
            $pdf->Line(95, 213, 95, 221);
            $pdf->Line(158, 213, 158, 221);

            // payment
            $pdf->SetXY(5, 225);
            $pdf->Cell(38, 5, "Settlement Mode:", 0, 0, 'R');
            $pdf->Cell(55, 5, $row[6], 0, 0, 'L');
            // due date
            $field_date = date_create($row[7]);
            $date_ech = date_format($field_date, 'd/m/Y');
            $pdf->SetXY(5, 230);
            $pdf->Cell(38, 5, "Date Due:", 0, 0, 'R');
            $pdf->Cell(38, 5, $date_ech, 0, 0, 'L');
        }


        // // observations
        // $pdf->SetFont( "Arial", "BU", 10 ); $pdf->SetXY( 5, 75 ); $pdf->Cell($pdf->GetStringWidth("Observations"), 0, "Observations", 0, "L");
        // $pdf->SetFont( "Arial", "", 10 ); $pdf->SetXY( 5, 78 ); $pdf->MultiCell(190, 4, $row[5], 0, "L");

        // client's fact address
        $select = "select * from vendor limit 1";
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
        $pdf->Cell(40, 7, $row_client[1], 0, 0, 'C');
        $y += 9;
        $pdf->SetXY($x + 45, $y);
        $pdf->Cell(40, 7, $row_client[2], 0, 0, 'C');
        $y += 9;
        $pdf->SetXY($x + 45, $y);
        $pdf->Cell(40, 7, $row_client[3], 0, 0, 'C');
        $y += 9;
        $pdf->SetXY($x + 45, $y);
        $pdf->Cell(40, 7, $row_client[4], 0, 0, 'C');
        $y += 9;
        $pdf->SetXY($x + 45, $y);
        $pdf->Cell(40, 7, $row_client[5], 0, 0, 'C');
        $y += 9;


        // if ($row_client[1]) { $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, $row_client[1], 0, 0, ''); $y += 4;}
        // if ($row_client[2]) { $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, $row_client[2], 0, 0, ''); $y += 4;}
        // if ($row_client[3]) { $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, $row_client[3], 0, 0, ''); $y += 4;}
        // if ($row_client[4] || $row_client[5]) { $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, $row_client[4] . ' ' .$row_client[5] , 0, 0, ''); $y += 4;}
        // if ($row_client[6]) { $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, 'Intra VAT No.: ' . $row_client[6] , 0, 0, '');}

        // ***********************
        // the frame of the articles
        // ***********************
        // frame with 18 lines max! and 118 height --> 95 + 118 = 213 for vertical lines
        $pdf->SetLineWidth(0.1);
        $pdf->Rect(5, 65, 200, 202, "D");
        // column title frame
        $pdf->Line(5, 75, 205, 75);
        // columns vertical lines
        $pdf->Line(20, 65, 20, 267);
        $pdf->Line(45, 65, 45, 267);
        $pdf->Line(65, 65, 65, 267);
        $pdf->Line(130 + 35, 65, 130 + 35, 267);
        $pdf->Line(155 + 33, 65, 155 + 33, 267);
        $pdf->Line(175 + 30, 65, 175 + 30, 267);
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

        // the items
        $pdf->SetFont('Arial', '', 8);
        $y = 67;
        // 1st page = LIMIT 0,18 ; 2nd page = LIMIT 18,36 etc...
        $sql = 'select * FROM product';
        $sql .= ' LIMIT ' . ($limit_sup) . ' OFFSET ' . (($num_page - 1) * $limit_sup);
        // echo $sql;
        $res = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
        while ($data = mysqli_fetch_assoc($res)) {

            // libelle
            $pdf->SetXY(7, $y + 9);
            $pdf->Cell(20, 5, $count, 0, 0, 'L');
            $pdf->SetXY(22, $y + 9);
            $pdf->Cell(25, 5, $data['itemNo'], 0, 0, 'L');
            $pdf->SetXY(47, $y + 9);
            $pdf->Cell(20, 5, $pdf->Image('../../pics/noImage.jpeg', 47, $y + 9, 0, 16), 0, 0, 'L');
            $pdf->SetXY(67, $y + 9);
            $pdf->Multicell(60, 5, "Hello World\nThis is shubham\nThis is shubham", 0);
            $pdf->SetXY(132 + 35, $y + 9);
            $pdf->MultiCell(22, 5, $data['vendorCode'], 0);
            $pdf->SetXY(157 + 32, $y + 9);
            $pdf->Cell(15, 5, $data['curStock'], 0, 0, 'R');
            // qte
            // $pdf->SetXY( 47, $y+9 ); $pdf->Cell( 13, 5, strrev(wordwrap(strrev($data['qte'])), 3, ' ', true), 0, 0, 'R');
            // // PU
            // $number_format_english = number_format($data['email'], 2, ',', ' ');
            // $pdf->SetXY( 158, $y+9 ); $pdf->Cell( 18, 5, $number_format_english, 0, 0, 'R');
            // // Rate
            // $number_format_francais = number_format($data['vid'], 2, ',', ' ');
            // $pdf->SetXY( 177, $y+9 ); $pdf->Cell( 10, 5, $number_format_francais, 0, 0, 'R');
            // // total
            // $number_format_english = number_format($data['id']*$data['qte'], 2, ',', ' ');
            // $pdf->SetXY( 187, $y+9 ); $pdf->Cell( 18, 5, $english_format_number, 0, 0, 'R');

            $pdf->Line(5, $y + 24, 205, $y + 24);

            $y += 16;
            $count = $count + 1;
        }
        mysqli_free_result($res);

        // if last page then display VAT frame
        if ($num_page == $nb_page) {
            // the detail of the totals, starts at 221 after the totals frame
            $pdf->SetLineWidth(0.1);
            $pdf->Rect(130, 221, 75, 24, "D");
            // the vertical lines
            $pdf->Line(147, 221, 147, 245);
            $pdf->Line(164, 221, 164, 245);
            $pdf->Line(181, 221, 181, 245);
            // horizontal lines step 6 and start at 221
            $pdf->Line(130, 227, 205, 227);
            $pdf->Line(130, 233, 205, 233);
            $pdf->Line(130, 239, 205, 239);
            // the titles
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(181, 221);
            $pdf->Cell(24, 6, "TOTAL", 0, 0, 'C');
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(105, 221);
            $pdf->Cell(25, 6, "VAT Rate", 0, 0, 'R');
            $pdf->SetXY(105, 227);
            $pdf->Cell(25, 6, "Total before tax", 0, 0, 'R');
            $pdf->SetXY(105, 233);
            $pdf->Cell(25, 6, "Total VAT", 0, 0, 'R');
            $pdf->SetXY(105, 239);
            $pdf->Cell(25, 6, "Total VAT", 0, 0, 'R');

            // the rates of vat and HT and TTC
            $col_ht = 0;
            $col_tva = 0;
            $col_ttc = 0;
            $taux = 0;
            $tot_tva = 0;
            $tot_ttc = 0;
            $x = 130;
            $sql = 'select * from vendor limit 0';
            $res = mysqli_query($mysqli, $sql) or die('SQL error: ' . $sql . mysqli_connect_error());
            while ($data = mysqli_fetch_assoc($res)) {
                $pdf->SetXY($x, 221);
                $pdf->Cell(17, 6, $data['name'] . ' %', 0, 0, 'C');
                $rates = $data['vat_rate'];

                $number_format_english = number_format($data['email'], 2, ',', ' ');
                $pdf->SetXY($x, 227);
                $pdf->Cell(17, 6, $number_format_english, 0, 0, 'R');
                $col_ht = $data['vid'];

                $col_tva = $col_ht - ($col_ht * (1 - ($taux / 100)));
                $number_format_francais = number_format($col_tva, 2, ',', ' ');
                $pdf->SetXY($x, 233);
                $pdf->Cell(17, 6, $number_format_francais, 0, 0, 'R');

                $col_ttc = $col_ht + $col_tva;
                $number_format_english = number_format($col_ttc, 2, ',', ' ');
                $pdf->SetXY($x, 239);
                $pdf->Cell(17, 6, $number_format_francais, 0, 0, 'R');

                $tot_tva += $col_tva;
                $tot_ttc += $col_ttc;

                $x += 17;
            }
            mysqli_free_result($res);

            $number_format_francais = "Net to pay TTC : " . number_format($tot_ttc, 2, ',', ' ') . " €";
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(5, 213);
            $pdf->Cell(90, 8, $number_format_francais, 0, 0, 'C');
            // at the bottom right
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(181, 239);
            $pdf->Cell(24, 6, number_format($tot_ttc, 2, ',', ' '), 0, 0, 'R');
            // VAT
            $number_format_francais = "Total VAT : " . number_format($tot_tva, 2, ',', ' ') . " €";
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(158, 213);
            $pdf->Cell(47, 8, $number_format_english, 0, 0, 'C');
            // at the bottom right
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(181, 233);
            $pdf->Cell(24, 6, number_format($tot_tva, 2, ',', ' '), 0, 0, 'R');
        }

        // **************************
        // footer
        // **************************
        $pdf->SetLineWidth(0.1);
        $pdf->Rect(5, 270, 200, 6, "D");
        $pdf->SetXY(1, 270);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell($pdf->GetPageWidth(), 7, "Clause de réserve de propriété (loi 80.335 du 12 mai 1980) : Les marchandises vendues demeurent notre propriété jusqu'au paiement intégral de celles-ci.", 0, 0, 'C');

        $y1 = 280;
        //Positioning at the bottom and center everything
        $pdf->SetXY(1, $y1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($pdf->GetPageWidth(), 5, "BANK REF : FR76 xxx - BIC : xxxx", 0, 0, 'C');

        $pdf->SetFont('Arial', '', 10);

        $pdf->SetXY(1, $y1 + 4);
        $pdf->Cell($pdf->GetPageWidth(), 5, "COMPANY NAME | ADDRESS 1 + POSTAL + CITY | Tel + Mail + SIRET", 0, 0, 'C');

        $pdf->SetXY(1, $y1 + 8);
        $pdf->Cell($pdf->GetPageWidth(), 5, "Web Address", 0, 0, 'C');

        // per page of 18 lines
        $num_page++;
        $limit_inf += 18;
    }

    $filename=uniqid().".pdf";
    $pdf->Output($filename,'F');
    $data['data'] = 'success';
    $data['filename']=$filename;
    $data['sql']=$sql;
    // writelog(6,implode(',', $_GET));
    echo json_encode($data);
}

?>