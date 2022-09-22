<?php
spl_autoload_register(
    function($class){
        require_once'../classes/'.$class.'.php';
    }
);
require_once 'fpdf/fpdf.php';
$school_name = 'HIKMAH INTERNATIONAL';
$address = 'HIKMA HOUSE, OPPOSITE UNIVERSITY OF JOS PERMANENT SITE, JOS, PLATEAU STATE';
$phone_and_email = 'TELEPHONE: 081 2172 1573, 080 3621 9054, 081 0635 9939. EMAIL: HIKMAHSCHOOLS.JOS@GMAIL.COM';
$date = '10TH SEPTEMBER, 2022(14TH SAFAR, 1444)';
$term = 'ft';
$fname = 'Abdulshakur';
$lname = 'Muhammad';
$oname = 'Jamiu';
$average = 98.4; 
$class = 'Pre Nursery';
$no_in_class = 30;
$position_grade = 'A+';
$session = '2019/2020 (1441/1442)';
$time_school_opened = 73;
$times_present = 59;
$times_absent = 14;
$height_bot = 5;
$height_eot = 6;
$weight_bot = 7;
$weight_eot = 9;
$scores = [
    ['subject'=>'mathematics','fa'=>5,'sa'=>4,'ft'=>13,'st'=>15, 'pro'=>10,'ex'=>30],
    ['subject'=>'Arabiya','fa' => 3, 'sa' => 4, 'ft' => 10, 'st' => 11, 'pro'=>10, 'ex' => 20],
    ['subject'=>'Integrated Science','fa' => 4, 'sa' => 1, 'ft' => 15, 'st' => 9, 'pro'=>10, 'ex' => 40],
    ['subject'=>'mathematics','fa'=>5,'sa'=>4,'ft'=>13,'st'=>15, 'pro'=>10,'ex'=>30],
    ['subject'=>'Arabiya','fa' => 3, 'sa' => 4, 'ft' => 10, 'st' => 11, 'pro'=>10, 'ex' => 20],
    ['subject'=>'Integrated Science','fa' => 4, 'sa' => 1, 'ft' => 15, 'st' => 9, 'pro'=>10, 'ex' => 40],
    ['subject'=>'mathematics','fa'=>5,'sa'=>4,'ft'=>13,'st'=>15, 'pro'=>10,'ex'=>30],
    ['subject'=>'Arabiya','fa' => 3, 'sa' => 4, 'ft' => 10, 'st' => 11, 'pro'=>10, 'ex' => 20],
    ['subject'=>'Integrated Science','fa' => 4, 'sa' => 1, 'ft' => 15, 'st' => 9, 'pro'=>10, 'ex' => 40],
    ['subject'=>'mathematics','fa'=>5,'sa'=>4,'ft'=>13,'st'=>15, 'pro'=>10,'ex'=>30],
    ['subject'=>'Arabiya','fa' => 3, 'sa' => 4, 'ft' => 10, 'st' => 11, 'pro'=>10, 'ex' => 20],
    ['subject'=>'Integrated Science','fa' => 4, 'sa' => 1, 'ft' => 15, 'st' => 9, 'pro'=>10, 'ex' => 40],
    ['subject'=>'mathematics','fa'=>5,'sa'=>4,'ft'=>13,'st'=>15, 'pro'=>10,'ex'=>30],
    ['subject'=>'Arabiya','fa' => 3, 'sa' => 4, 'ft' => 10, 'st' => 11, 'pro'=>10, 'ex' => 20],
    ['subject'=>'Integrated Science','fa' => 4, 'sa' => 1, 'ft' => 15, 'st' => 9, 'pro'=>10, 'ex' => 40],
    ['subject'=>'mathematics','fa'=>5,'sa'=>4,'ft'=>13,'st'=>15, 'pro'=>10,'ex'=>30],
    ['subject'=>'Arabiya','fa' => 3, 'sa' => 4, 'ft' => 10, 'st' => 11, 'pro'=>10, 'ex' => 20],
    ['subject'=>'mathematics','fa'=>5,'sa'=>4,'ft'=>13,'st'=>15, 'pro'=>10,'ex'=>30],
    ['subject'=>'Arabiya','fa' => 3, 'sa' => 4, 'ft' => 10, 'st' => 11, 'pro'=>10, 'ex' => 20],
];

$pdf = new FPDF();
$pdf->SetMargins(10,5,10);
$pdf->SetAutoPageBreak(true,5);
$pdf->AddPage();
//$pdf->AddFont('Roboto-Bold', '', 'Roboto-Bold.php');
$pdf->AddFont('Heebo-Regular', '', 'Heebo-Regular.php');
$pdf->AddFont('BreeSerif-Regular', '', 'BreeSerif-Regular.php');
$pdf->AddFont('Lusitana-Bold', '', 'Lusitana-Bold.php');
$pdf->AddFont('Rubik-Regular', '', 'Rubik-Regular.php');
//$pdf->AddFont('Rubik-Bold', '', 'Rubik-Bold.php');
// $pdf->AddFont('NotoSerif-Regular', '', 'NotoSerif-Regular.php');

$pdf->SetFont('BreeSerif-Regular','',15);
$pdf->Cell(190,9,$school_name,0,1,'C');

$pdf->SetFont('Heebo-Regular','',7);
$pdf->Cell(190,4,$address,0,1,'C');
$pdf->SetFont('Heebo-Regular', '', 7);
$pdf->Cell(190, 4, $phone_and_email, 0, 1, 'C');
$pdf->SetFont('Lusitana-Bold', '', 11);
$pdf->Ln(5);
$pdf->Cell(190, 7, strtoupper(Utility::formatTerm($term)).' RESULT', 0, 1, 'C');
$pdf->Ln();
$yPos = $pdf->GetY();
$pdf->SetFont('Rubik-Regular', '', 9);

$pdf->Cell(26,6, 'NAME OF PUPIL: ', 0, 0, 'L');
$pdf->Cell(27, 6, ucfirst(Utility::formatName($fname, $oname, $lname, false)), 0, 1, 'L');
$pdf->Cell(18,6, 'AVERAGE: ', 0, 0, 'L');
$pdf->Cell(20, 6, $average, 0, 1, 'L');
$pdf->Cell(14,6, 'CLASS: ', 0, 0, 'L');
$pdf->Cell(30, 6, $class, 0, 2, 'L');

$pdf->setXY(147, $yPos);
$pdf->Cell(28,6, 'GRADE POSITION: ', 0, 0, 'L');
$pdf->Cell(30,6, $position_grade, 0, 2, 'L');
$pdf->setX(147);
$pdf->Cell(15, 6, 'SESSION: ', 0, 0, 'L');
$pdf->Cell(30, 6, $session, 0, 2, 'L');
$pdf->setX(147);
$pdf->Cell(21, 6, 'NO IN CLASS: ', 0, 0, 'L');
$pdf->Cell(30, 6, $no_in_class, 0, 2, 'L');

$pdf->SetXY(10,65);
$pdf->SetFont('Lusitana-Bold', '', 6);
$yPos = $pdf->GetY();

$pdf->Cell(43, 4, 'ATTENDANCE RECORD', 1, 1, 'C');
$pdf->SetFont('Rubik-Regular', '', 6);
$pdf->Cell(27, 4, 'Time School Opened', 1, 0, 'L');
$pdf->Cell(16, 4, $time_school_opened, 1,1, 'L');
$pdf->Cell(27, 4, 'Times Present', 1, 0, 'L');
$pdf->Cell(16, 4, $times_present, 1,1, 'L');
$pdf->Cell(27, 4, 'Times Absent', 1, 0, 'L');
$pdf->Cell(16, 4, $times_absent, 1,1, 'L');

$pdf->SetFont('Lusitana-Bold', '', 6);
$pdf->SetXY(148, $yPos);
$pdf->Cell(46, 4, 'HEALTH AND PHYSICAL GROWTH', 1, 2, 'C');
$pdf->Cell(23, 4, 'HEIGHT (M)', 1, 0, 'C');
$pdf->Cell(23, 4, 'WEIGHT (KM)', 1, 2, 'C');
$pdf->SetFont('Rubik-Regular', '', 6);
$pdf->SetX(148);
$pdf->Cell(11.5, 4, 'BOT', 1, 0, 'C');
$pdf->Cell(11.5, 4, 'EOT', 1, 0, 'C');
$pdf->Cell(11.5, 4, 'BOT', 1, 0, 'C');
$pdf->Cell(11.5, 4, 'EOT', 1, 2, 'C');
$pdf->SetX(148);
$pdf->Cell(11.5, 4, $height_bot, 1, 0, 'C');
$pdf->Cell(11.5, 4, $height_eot, 1, 0, 'C');
$pdf->Cell(11.5, 4, $weight_bot, 1, 0, 'C');
$pdf->Cell(11.5, 4, $weight_eot, 1, 0, 'C');
$widths = [];

$pdf->SetFont('Lusitana-Bold', '', 6);
$yPos = 90;
$pdf->SetXY(10,$yPos);
$pdf->MultiCell(45, 6, 'SUBJECT',1,'C');
$pdf->SetXY(55,$yPos);
$pdf->MultiCell(14, 3, 'FIRST ASSIGN.',1,'C');
$pdf->SetXY(69, $yPos);
$pdf->MultiCell(14, 3, 'SECOND ASSIGN.',1,'C');
$pdf->SetXY(83, $yPos);
$pdf->MultiCell(14, 3, 'FIRST TEST',1,'C');
$pdf->SetXY(97, $yPos);
$pdf->MultiCell(14, 3, 'SECOND TEST',1,'C');
$pdf->SetXY(111, $yPos);
$pdf->MultiCell(14, 6, 'CA TOTAL', 1, 'C');
$pdf->SetXY(125, $yPos);
$pdf->MultiCell(14, 6, 'PROJECT',1,'C');
$pdf->SetXY(139, $yPos);
$pdf->MultiCell(14, 6, 'EXAM',1,'C');
$pdf->SetXY(153, $yPos);
$pdf->MultiCell(14, 6, 'TOTAL',1,'C');
$pdf->SetXY(167, $yPos);
$pdf->MultiCell(14, 6, 'GRADE',1,'C');
$pdf->SetXY(181, $yPos);
$pdf->MultiCell(19, 6, 'REMARK',1,'C');
$pdf->SetFont('Rubik-Regular', '', 9);
foreach($scores as $score){
    $yPos = $pdf->GetY();
    $pdf->SetXY(10, $yPos);
    $pdf->MultiCell(45, 5, ucwords($score['subject']), 1, 'L');
    $pdf->SetXY(55, $yPos);
    $pdf->MultiCell(14, 5, $score['fa'], 1, 'C');
    $pdf->SetXY(69, $yPos);
    $pdf->MultiCell(14, 5, $score['sa'], 1, 'C');
    $pdf->SetXY(83, $yPos);
    $pdf->MultiCell(14, 5, $score['ft'], 1, 'C');
    $pdf->SetXY(97, $yPos);
    $pdf->MultiCell(14, 5, $score['st'], 1, 'C');
    $pdf->SetXY(111, $yPos);
    $pdf->MultiCell(14, 5, $score['pro'], 1, 'C');
    $pdf->SetXY(125, $yPos);
    $pdf->MultiCell(14, 5, $score['pro'], 1, 'C');
    $pdf->SetXY(139, $yPos);
    $pdf->MultiCell(14, 5, $score['fa'], 1, 'C');
    $pdf->SetXY(153, $yPos);
    $pdf->MultiCell(14, 5, $score['fa'], 1, 'C');
    $pdf->SetXY(167, $yPos);
    $pdf->MultiCell(14, 5, $score['fa'], 1, 'C');
    $pdf->SetXY(181, $yPos);
    $pdf->MultiCell(19, 5, $score['fa'], 1, 'C');
}
$yPos = $pdf->GetY();
$pdf->SetXY(10, $yPos);
$pdf->MultiCell(45, 5, 'Total Score', 1, 'L');
$pdf->SetXY(55, $yPos);
$pdf->MultiCell(14, 5, 500, 1, 'C');

/////////
$pdf->Ln();
$pdf->SetFont('Lusitana-Bold', '', 6);
$yPos = $pdf->GetY();
$pdf->Cell(90, 4, 'PSYCHOMOTOR SKILLS', 1, 1, 'C');
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 1, 'C');

$pdf->SetFont('Rubik-Regular', '', 6);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 1, 'C');

$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 1, 'C');

$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 1, 'C');

$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 1, 'C');

$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 1, 'C');
/////////////
$pdf->SetFillColor(220, 238, 239);
$pdf->Ln();
$x=$pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Lusitana-Bold', '', 6);
$pdf->Cell(90, 7, 'KEY TO GRADES', 0, 1, 'C',true);
$pdf->SetFont('Rubik-Regular', '', 6);
$pdf->Cell(50, 4, 'A1 = EXCELLENT (75-100)', 0, 0, 'L',true);
$pdf->Cell(40, 4, 'C6 = LOWER CREDIT (50-54)', 0, 1, 'L',true);
$pdf->Cell(50, 4, 'B2 = VERY GOOD (70-74)', 0, 0, 'L',true);
$pdf->Cell(40, 4, 'D7 = PASS (45-49)', 0, 1, 'L',true);
$pdf->Cell(50, 4, 'B3 = GOOD (65-69)', 0, 0, 'L',true);
$pdf->Cell(40, 4, 'E8 = POOR (40-44)', 0, 1, 'L',true);
$pdf->Cell(50, 4, 'C4 = UPPER CREDIT (60-65)', 0, 0, 'L',true);
$pdf->Cell(40, 4, 'F9 = FAIL (0-39)', 0, 1, 'L',true);
$pdf->Cell(50, 5, 'C5 = CREDIT (55-59)', 0, 0, 'L',true);
$pdf->Cell(40, 5, '', 0, 1, 'L', true);


////////////////////////ASSESSMENT OF BEHAVIOUR///
$pdf->SetFont('Lusitana-Bold', '', 6);
$x = 110;
$y = $yPos;
$pdf->SetXY($x,$y);
$pdf->Cell(90, 4, 'ASSESSMENT OF BEHAVIOUR', 1, 2, 'C');

$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');
$pdf->SetFont('Rubik-Regular', '', 6);

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');


$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');


$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');

$pdf->SetX($x);
$pdf->Cell(40, 4, '', 1, 0, 'C');
$pdf->Cell(10, 4, 'A', 1, 0, 'C');
$pdf->Cell(10, 4, 'B+', 1, 0, 'C');
$pdf->Cell(10, 4, 'B', 1, 0, 'C');
$pdf->Cell(10, 4, 'C', 1, 0, 'C');
$pdf->Cell(10, 4, 'D', 1, 2, 'C');



$pdf->Output('I');
$pdf->Close();