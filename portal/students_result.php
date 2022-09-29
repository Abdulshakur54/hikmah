<?php
//initializations

spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
require_once 'phpqrcode/qrlib.php';

function get_col_fullname($col)
{
    switch ($col) {
        case 'fa':
            return 'FIRST ASSIGN.';
        case 'sa':
            return 'SECOND ASSIGN.';
        case 'ft':
            return 'FIRST TEST';
        case 'st':
            return 'SECOND TEST';
        case 'pro':
            return 'PROJECT';
        case 'ex':
            return 'EXAM';
    }
}

function checked_counter($grade, $psy_grade)
{
    return ($grade == $psy_grade) ? 'V' : '';
}

if (Input::submitted('get')) {
    $token = Utility::escape(Input::get('token'));
    $identifier = Utility::escape(Input::get('identifier'));
    if (!empty($identifier) && QRC::verify($identifier, $token)) {
        $other_data = QRC::get_other_data($identifier);
        $session = $other_data['session'];
        $student_ids = [$other_data['student_id']];
        $term = $other_data['term'];
        $sch_abbr = $other_data['school'];
    } else if (Token::check($token)) {
        $session = Utility::escape(Input::get('session'));
        $term = Utility::escape(Input::get('term'));
        $sch_abbr = Utility::escape(Input::get('school'));
        $student_ids = urldecode(Input::get('student_ids'));
        $student_ids = json_decode($student_ids);
    } else {
        Redirect::to(404);
    }
} else {
    Redirect::to(404);
}


$res = new Result($session, $term, $sch_abbr);
require_once 'fpdf/fpdf.php';
require_once 'phpqrcode/qrlib.php';
$subject = new Subject();
$url = new Url();
/*getting the required column*/
$columns = $subject->getNeededColumns($sch_abbr);
$pos = array_search('exam', $columns);
if ($pos !== false) { //false is used for comparison because 0 is a valid value in this scenario
    $columns[$pos] = 'ex';
}
if (in_array('pro', $columns)) {
    $sub_width = 45;
} else {
    $sub_width = 50;
}
$post_sub_pos = 10 + $sub_width;
$pdf = new FPDF();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(true, 5);

//add fonts
$pdf->AddFont('Heebo-Regular', '', 'Heebo-Regular.php');
$pdf->AddFont('BreeSerif-Regular', '', 'BreeSerif-Regular.php');
$pdf->AddFont('Lusitana-Bold', '', 'Lusitana-Bold.php');
$pdf->AddFont('Rubik-Regular', '', 'Rubik-Regular.php');
$pdf->AddFont('Mansalva-Regular', '', 'Mansalva-Regular.php');
$pdf->AddFont('HindSiliguri-Medium', '', 'HindSiliguri-Medium.php');

$std_prefix = School::get_std_prefix($sch_abbr);
$school_name = strtoupper(School::getFullName($sch_abbr));

foreach ($student_ids as $std_id) {
    $pdf->AddPage();
    $agg_data = $res->get_aggregate_data($std_id, $sch_abbr);
    $scores = $res->get_scores($std_id);
    $address = $agg_data->address;
    $phone_and_email = 'TELEPHONE: ' . $agg_data->phone_contacts . '. EMAIL: ' . $agg_data->email;
    $fname = $agg_data->fname;
    $lname = $agg_data->lname;
    $oname = $agg_data->oname;
    $average = $agg_data->average;
    $class = School::getLevelName($sch_abbr, $agg_data->level) . ' ' . $agg_data->class;
    $no_in_class = $res->get_student_count($agg_data->class_id);
    $position_grade = Result::get_grade($average);
    $output_session = $session . ' (' . $agg_data->hijra_session . ')';
    $times_school_opened = $agg_data->{$term . '_times_opened'};
    $times_present = $agg_data->{$term . '_times_present'};
    $res_date = $agg_data->{$term . '_res_date'};
    $total = $agg_data->total;
    $times_absent = $times_school_opened - $times_present;
    $height_bot = $agg_data->{$term . '_height_beg'};
    $height_eot =  $agg_data->{$term . '_height_end'};
    $weight_bot =  $agg_data->{$term . '_weight_beg'};
    $weight_eot =  $agg_data->{$term . '_weight_end'};
    $title = Utility::format_student_id($std_id) . '_' . $term . '_result.png';
    $qr_data = QRC::get_code($title, ['student_id' => $std_id, 'session' => $session, 'term' => $term, 'school' => $sch_abbr]);
    $link = $url->to('students_result.php?identifier=' . $qr_data->identifier . '&token=' . $qr_data->token, 0);
    if (!file_exists('barcodes/' . $title)) {
        QRcode::png($link, 'barcodes/' . $title);
    }



    $pdf->Image('management/apm/uploads/logo/hikmah.jpg', 10, 5, 15, 15);
    $pdf->Image('management/apm/uploads/logo/' . $agg_data->logo, 185, 5, 15, 15);
    $pdf->SetFont('BreeSerif-Regular', '', 15);
    $pdf->Cell(190, 9, $school_name, 0, 1, 'C');
    $pdf->SetTextColor(18, 34, 204);
    $pdf->SetFont('Heebo-Regular', '', 7);
    $pdf->Cell(190, 4, $address, 0, 1, 'C');
    $pdf->SetFont('Heebo-Regular', '', 7);
    $pdf->Cell(190, 4, $phone_and_email, 0, 1, 'C');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Lusitana-Bold', '', 11);
    $pdf->Ln(5);
    $pdf->Cell(190, 7, strtoupper(Utility::formatTerm($term)) . ' RESULT', 0, 1, 'C');
    $pdf->Ln();
    $yPos = $pdf->GetY();
    $pdf->SetFont('Rubik-Regular', '', 9);
    if (School::get_std_prefix($sch_abbr) === 'Pupil') {
        $pdf->Cell(26, 6, 'NAME OF PUPIL: ', 0, 0, 'L');
    } else {
        $pdf->Cell(31, 6, 'NAME OF STUDENT: ', 0, 0, 'L');
    }

    $pdf->Cell(27, 6, ucfirst(Utility::formatName($fname, $oname, $lname, false)), 0, 1, 'L');
    $pdf->Cell(18, 6, 'AVERAGE: ', 0, 0, 'L');
    $pdf->Cell(20, 6, $average, 0, 1, 'L');
    $pdf->Cell(14, 6, 'CLASS: ', 0, 0, 'L');
    $pdf->Cell(30, 6, $class, 0, 1, 'L');
    $pdf->Cell(14, 6, 'TOTAL: ', 0, 0, 'L');
    $imgY = $pdf->GetY();
    $pdf->Cell(30, 6, $total, 0, 2, 'L');

    $pdf->setXY(148, $yPos);
    $pdf->Cell(28, 6, 'GRADE POSITION: ', 0, 0, 'L');
    switch ($position_grade) {
        case 'A1':
            $pdf->SetTextColor(15, 165, 19);
            break;
        case 'F9':
            $pdf->SetTextColor(172, 28, 18);
            break;
        case 'B2':
        case 'B3':
        case 'C4':
        case 'C5':
        case 'C6':
            $pdf->SetTextColor(20, 32, 184);
    }
    $pdf->Cell(30, 6, $position_grade, 0, 2, 'L');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->setX(148);
    $pdf->Cell(16, 6, 'SESSION: ', 0, 0, 'L');
    $pdf->Cell(30, 6, $output_session, 0, 2, 'L');
    $pdf->setX(148);
    $pdf->Cell(22, 6, 'NO IN CLASS: ', 0, 0, 'L');
    $pdf->Cell(30, 6, $no_in_class, 0, 2, 'L');
    $pdf->setX(148);
    $pdf->Cell(21, 6, 'NEXT TERM: ', 0, 0, 'L');
    $pdf->Cell(30, 6, Utility::formatDate($res_date, 1), 0, 2, 'L');

    $pdf->SetXY(10, 71);
    $pdf->SetFont('Lusitana-Bold', '', 6);
    $yPos = $pdf->GetY();

    $pdf->Cell(43, 4, 'ATTENDANCE RECORD', 1, 1, 'C');
    $pdf->SetFont('Rubik-Regular', '', 6);
    $pdf->Cell(27, 4, 'Time School Opened', 1, 0, 'L');
    $pdf->Cell(16, 4, $times_school_opened, 1, 1, 'L');
    $pdf->Cell(27, 4, 'Times Present', 1, 0, 'L');
    $pdf->Cell(16, 4, $times_present, 1, 1, 'L');
    $pdf->Cell(27, 4, 'Times Absent', 1, 0, 'L');
    $pdf->Cell(16, 4, $times_absent, 1, 1, 'L');

    //output student passport
    $pdf->Image('student/uploads/passports/' . $agg_data->picture, 100, $imgY + 4 - 15, 15, 15);
    //output student passport

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

    $pdf->SetFont('Lusitana-Bold', '', 6);
    $yPos = 96;
    $pdf->SetXY(10, $yPos);
    $pdf->MultiCell($sub_width, 6, 'SUBJECT', 1, 'C');
    $xPos = $post_sub_pos;
    $output_ca_total_col = false;

    foreach ($columns as $col) {
        $pdf->SetXY($xPos, $yPos);
        switch ($col) {
            case 'fa':
            case 'sa':
            case 'ft':
            case 'st':
                $pdf->MultiCell(14, 3, get_col_fullname($col), 1, 'C');
                break;
            case 'pro':
            case 'ex':
                if (!$output_ca_total_col) {
                    $pdf->MultiCell(14, 6, 'CA TOTAL', 1, 'C');
                    $xPos += 14;
                    $pdf->SetXY($xPos, $yPos);
                    $output_ca_total_col = true;
                }
                $pdf->MultiCell(14, 6, get_col_fullname($col), 1, 'C');
                break;
        }

        $xPos += 14;
    }

    $pdf->SetXY($xPos, $yPos);
    $pdf->MultiCell(14, 6, 'TOTAL', 1, 'C');
    $xPos += 14;

    $pdf->SetXY($xPos, $yPos);
    $pdf->MultiCell(12, 6, 'GRADE', 1, 'C');
    $xPos += 12;
    $rem_width = 200 - $xPos;
    $pdf->SetXY($xPos, $yPos);
    $pdf->MultiCell($rem_width, 6, 'REMARK', 1, 'C');

    $pdf->SetFont('Rubik-Regular', '', 9);
    foreach ($scores as $score) {
        $xPos = $post_sub_pos;
        $pdf->Cell($sub_width, 5, strtoupper($score->subject), 1, 0, 'L');
        $ca_s = [];
        $output_ca_total_col = false;
        foreach ($columns as $col) {
            switch ($col) {
                case 'fa':
                case 'sa':
                case 'ft':
                case 'st':
                    $col_name = $term . '_' . $col;
                    $ca_s[] =  $score->$col_name;

                    $pdf->Cell(14, 5, $score->$col_name, 1, 0, 'C');

                    break;
                case 'pro':
                case 'ex':
                    if (!$output_ca_total_col) {
                        $pdf->Cell(14, 5, array_sum($ca_s), 1, 0, 'C');
                        $output_ca_total_col = true;
                        $xPos += 14;
                    }
                    $pdf->Cell(14, 5, $score->{$term . '_' . $col}, 1, 0, 'C');
                    break;
            }

            $xPos += 14;
        }
        $tot = $score->{$term . '_tot'};
        $grade = Result::get_grade($tot);
        $pdf->Cell(14, 5, $tot, 1, 0, 'C');
        $xPos += 14;
        switch ($grade) {
            case 'A1':
                $pdf->SetTextColor(15, 165, 19);
                break;
            case 'F9':
                $pdf->SetTextColor(172, 28, 18);
        }
        $pdf->Cell(12, 5, $grade, 1, 0, 'C');
        $xPos += 12;
        $rem_width = 200 - $xPos;
        $pdf->Cell($rem_width, 5, Result::get_remark($grade), 1, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
    }


    //PSYCHOMOTOR
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

    $psychomotors = ['Verbal Skills', 'Participation in games', 'Participation in sports', 'Artistic Creativity', 'Physical and Mental Agility', 'Manual Skills (Dexterity)'];
    $counter = 1;
    foreach ($psychomotors as $psy) {
        $pdf->SetFont('Rubik-Regular', '', 6);
        $pdf->Cell(40, 4, $psy, 1, 0, 'L');
        $pdf->SetFont('Mansalva-Regular', '', 6);
        $pdf->Cell(10, 4, checked_counter('A', $agg_data->{$term . '_psy' . $counter}), 1, 0, 'C');
        $pdf->Cell(10, 4, checked_counter('B+', $agg_data->{$term . '_psy' . $counter}), 1, 0, 'C');
        $pdf->Cell(10, 4, checked_counter('B', $agg_data->{$term . '_psy' . $counter}), 1, 0, 'C');
        $pdf->Cell(10, 4, checked_counter('C', $agg_data->{$term . '_psy' . $counter}), 1, 0, 'C');
        $pdf->Cell(10, 4, checked_counter('D', $agg_data->{$term . '_psy' . $counter}), 1, 1, 'C');
        $counter++;
    }
    //PSYCHOMOTOR ENDS

    //KEY TO GRADES
    $pdf->SetFillColor(220, 238, 239);
    $pdf->Ln();
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->SetFont('Lusitana-Bold', '', 6);
    $pdf->Cell(90, 7, 'KEY TO GRADES', 0, 1, 'C', true);
    $pdf->SetFont('Rubik-Regular', '', 6);
    $pdf->Cell(50, 4, 'A1 = EXCELLENT (75-100)', 0, 0, 'L', true);
    $pdf->Cell(40, 4, 'C6 = LOWER CREDIT (50-54)', 0, 1, 'L', true);
    $pdf->Cell(50, 4, 'B2 = VERY GOOD (70-74)', 0, 0, 'L', true);
    $pdf->Cell(40, 4, 'D7 = PASS (45-49)', 0, 1, 'L', true);
    $pdf->Cell(50, 4, 'B3 = GOOD (65-69)', 0, 0, 'L', true);
    $pdf->Cell(40, 4, 'E8 = POOR (40-44)', 0, 1, 'L', true);
    $pdf->Cell(50, 4, 'C4 = UPPER CREDIT (60-64)', 0, 0, 'L', true);
    $pdf->Cell(40, 4, 'F9 = FAIL (0-39)', 0, 1, 'L', true);
    $pdf->Cell(50, 5, 'C5 = CREDIT (55-59)', 0, 0, 'L', true);
    $pdf->Cell(40, 5, '', 0, 1, 'L', true);
    //END OF KEY TO GRADES

    //ASSESSMENT OF BEHAVIOUR
    $pdf->SetFont('Lusitana-Bold', '', 6);
    $x = 110;
    $y = $yPos;
    $pdf->SetXY($x, $y);
    $pdf->Cell(90, 4, 'ASSESSMENT OF BEHAVIOUR', 1, 2, 'C');

    $pdf->Cell(40, 4, '', 1, 0, 'C');
    $pdf->Cell(10, 4, 'A', 1, 0, 'C');
    $pdf->Cell(10, 4, 'B+', 1, 0, 'C');
    $pdf->Cell(10, 4, 'B', 1, 0, 'C');
    $pdf->Cell(10, 4, 'C', 1, 0, 'C');
    $pdf->Cell(10, 4, 'D', 1, 2, 'C');
    $aob = ['Punctuality', 'Honesty', 'Does home work', 'Respect and Politeness', 'Spirit of teamwork', 'Relationship with peers', 'Leadership skills', 'Attitude to work', 'Helping others', 'Carefulness', 'Consideration', 'Works independently', 'Obedience', 'Health'];
    $counter = 7;
    foreach ($aob as $ab) {
        $pdf->SetX($x);
        $pdf->SetFont('Rubik-Regular', '', 6);
        $pdf->Cell(40, 4, $ab, 1, 0, 'L');
        $pdf->SetFont('Mansalva-Regular', '', 6);
        $pdf->Cell(10, 4, checked_counter('A', $agg_data->{$term . '_psy' . $counter}), 1, 0, 'C');
        $pdf->Cell(10, 4, checked_counter('B+', $agg_data->{$term . '_psy' . $counter}), 1, 0, 'C');
        $pdf->Cell(10, 4,  checked_counter('B', $agg_data->{$term . '_psy' . $counter}), 1, 0, 'C');
        $pdf->Cell(10, 4,  checked_counter('C', $agg_data->{$term . '_psy' . $counter}), 1, 0, 'C');
        $pdf->Cell(10, 4,  checked_counter('D', $agg_data->{$term . '_psy' . $counter}), 1, 2, 'C');
        $counter++;
    }
    //ASSESSMENT OF BEHAVIOUR ENDS
    $pdf->Ln();
    $pdf->Ln(3);
    $pdf->SetX(10);
    $pdf->SetFont('Lusitana-Bold', '', 8);
    $pdf->Cell(140, 5, 'Teacher\'s Remark', 0, 0);
    $imgY = $pdf->GetY();
    $pdf->Cell(15, 5, 'Sign/Date', 0, 1);
    $pdf->SetFont('HindSiliguri-Medium', '', 8);
    $pdf->Cell(140, 5, $agg_data->{$term . '_com'}, 0, 0);


    $pdf->Ln(7);
    $pdf->SetX(10);
    $pdf->SetFont('Lusitana-Bold', '', 8);
    $pdf->Cell(140, 5, 'HOS\'s Remark', 0, 0);
    $imgY2 = $pdf->GetY();
    $pdf->Cell(15, 5, 'Sign/Date', 0, 1);
    $pdf->SetFont('HindSiliguri-Medium', '', 8);
    $pdf->Cell(140, 5, $agg_data->{$term . '_p_com'}, 0, 0);

    //images
    $pdf->Image('staff/uploads/signatures/' . $agg_data->tea_signature, 165, $imgY - 2, 15, 9);
    $pdf->Image('management/hos/uploads/signatures/' . $agg_data->hos_signature, 165, $imgY2 - 2, 15, 9);
    $pdf->Image('barcodes/' . $title, 185, $imgY + 1, 15, 15);
    //end of images
}
$pdf->Output('I', 'results.pdf');
