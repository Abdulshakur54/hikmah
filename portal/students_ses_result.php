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
require_once 'fpdf/fpdf.php';
require_once 'phpqrcode/qrlib.php';
require_once('jpgraph/jpgraph.php');
require_once('jpgraph/jpgraph_bar.php');

if (Input::submitted('get')) {
    $token = Utility::escape(Input::get('token'));
    $identifier = Utility::escape(Input::get('identifier'));
    if (!empty($identifier) && QRC::verify($identifier, $token)) {
        $other_data = QRC::get_other_data($identifier);
        $session = $other_data['session'];
        $student_ids = [$other_data['student_id']];
        $term = $other_data['term'];
        $sch_abbr = $other_data['school'];
    } else if (Token::check($token, null, true)) {
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
$subject = new Subject();
$url = new Url();
$chart = new Chart();

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
$bar_image_paths = []; //stores the file path to the bar images that would be generated so it can be deleted before scripts end after outputting pdf
foreach ($student_ids as $std_id) {
    $pdf->AddPage();
    $agg_data = $res->get_ses_aggregate_data($std_id);
    $fname = $agg_data->fname;
    $lname = $agg_data->lname;
    $oname = $agg_data->oname;
    $tea_signature = $agg_data->tea_signature;
    $hos_signature = $agg_data->hos_signature;
    $scores = $res->get_ses_scores($std_id);
    if (!empty($scores)) {
        $total = 0;
        $count = 0;
        $score_totals = [];
        $ft_totals = [];
        $st_totals = [];
        $tt_totals = [];
        $subjects = [];
        foreach ($scores as $score) {
            $sum =  $score->ft_tot + $score->st_tot + $score->tt_tot;
            $ft_totals[] = $score->ft_tot;
            $st_totals[] = $score->st_tot;
            $tt_totals[] = $score->tt_tot;
            $subjects[] = ucfirst(strtolower(substr($score->subject,0,3)));
            $score_totals[] = $sum;
            $total  = $total + $sum;
            $count++;
        }
        $average = $total / $count;
        $position_grade = Result::get_grade($average);
        $output_session =  'SESSIONAL REPORT (' . $session . ' or ' . $agg_data->hijra_session . ')';
        $class = School::getLevelName($sch_abbr, $agg_data->level) . ' ' . $agg_data->class;
        $times_school_opened = ($agg_data->ft_times_opened + $agg_data->st_times_opened + $agg_data->tt_times_opened);
        $times_present = ($agg_data->ft_times_present +  $agg_data->st_times_present +  $agg_data->tt_times_present);
        $pass_mark = ($agg_data->ft_passmark + $agg_data->st_passmark + $agg_data->tt_passmark) / 3;
        $title = Utility::format_student_id($std_id) . '_' . $term . '_ses_result.png';
        $pdf->SetFont('BreeSerif-Regular', '', 13);
        $pdf->Cell(190, 9, $output_session, 0, 1, 'C');
        $qr_data = QRC::get_code($title, ['student_id' => $std_id, 'session' => $session, 'term' => $term, 'school' => $sch_abbr]);
        $link = $url->to('students_ses_result.php?identifier=' . $qr_data->identifier . '&token=' . $qr_data->token, 0);
        if (!file_exists('barcodes/' . $title)) {
            QRcode::png($link, 'barcodes/' . $title);
        }

        $pdf->Ln();
        if (file_exists('student/uploads/passports/' . $agg_data->picture)) {
            //output student passport
            $pdf->Image('student/uploads/passports/' . $agg_data->picture, 95, 25, 20, 20);
            //output student passport
        }
        $pdf->SetY(47);
        $pdf->SetFont('Lusitana-Bold', '', 11);
        $pdf->Cell(190, 6, strtoupper($std_id), 0, 1, 'C');
        $pdf->Ln();
        $y = $pdf->GetY();
        $pdf->Cell(190, 6, 'Summary', 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->SetFont('Rubik-Regular', '', 9);
        if (School::get_std_prefix($sch_abbr) === 'Pupil') {
            $pdf->Cell(26, 6, 'NAME OF PUPIL: ', 0, 0, 'L');
        } else {
            $pdf->Cell(31, 6, 'NAME OF STUDENT: ', 0, 0, 'L');
        }
        $y = $pdf->GetY();
        $pdf->Cell(27, 6, ucfirst(Utility::formatName($fname, $oname, $lname, false)), 0, 1, 'L');
        $pdf->Cell(13, 6, 'TOTAL: ', 0, 0, 'L');
        $pdf->Cell(20, 6, $total, 0, 1, 'L');
        $grade_summary = Result::get_grades_summary($score_totals);
        $formatted_grade_summary = Result::format_grade_summary($grade_summary);
        $pdf->Cell(29, 6, 'GRADE SUMMARY: ', 0, 0, 'L');
        $pdf->Cell(30, 6, $formatted_grade_summary, 0, 1, 'L');
        $x = 95;
        $pdf->SetXY($x, $y);
        $pdf->Cell(13, 6, 'CLASS: ', 0, 0, 'L');
        $pdf->Cell(27, 6, $class, 0, 1, 'L');
        $pdf->SetX($x);
        $pdf->Cell(17, 6, 'AVERAGE: ', 0, 0, 'L');
        $pdf->Cell(27, 6, $average, 0, 1, 'L');
        $pdf->SetX($x);
        $pdf->Cell(17, 6, 'VERDICT: ', 0, 0, 'L');
        if ($term === 'tt') {

            if ($average >= $pass_mark) {
                $pdf->SetTextColor(0, 204, 0);
                $pdf->Cell(190, 6, 'PROMOTED', 0, 1, 'C');
            } else {
                $pdf->SetTextColor(204, 0, 0);
                $pdf->Cell(27, 6, 'REPEATED', 0, 1, 'L');
            }
        } else {
            $pdf->SetTextColor(20, 32, 184);
            $pdf->Cell(27, 6, 'PENDING', 0, 1, 'L');
        }
        $pdf->SetTextColor(0, 0, 0);

        $x = 160;
        $pdf->SetXY($x, $y);
        $pdf->Cell(24, 6, 'ATTENDANCE: ', 0, 0, 'L');
        $pdf->Cell(27, 6, $times_present . ' / ' . $times_school_opened, 0, 1, 'L');
        $pdf->SetX($x);
        $pdf->Cell(28, 6, 'OVERALL GRADE: ', 0, 0, 'L');
        $overall_grade = Result::get_grade($average);
        switch ($overall_grade) {
            case 'A1':
                $pdf->SetTextColor(15, 165, 19);
                break;
            case 'F9':
                $pdf->SetTextColor(204, 0, 0);
                break;
            case 'B2':
            case 'B3':
            case 'C4':
            case 'C5':
            case 'C6':
                $pdf->SetTextColor(20, 32, 184);
        }
        $pdf->Cell(27, 6, $overall_grade, 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(11);
        $pdf->SetFont('Lusitana-Bold', '', 11);
        $pdf->Cell(190, 6, 'Detail', 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->SetFont('Lusitana-Bold', '', 6);
        $pdf->Cell(50, 6, 'SUBJECTS', 1, 0, 'C');
        $pdf->Cell(20, 6, 'FIRST TERM', 1, 0, 'C');
        $pdf->Cell(20, 6, 'SECOND TERM', 1, 0, 'C');
        $pdf->Cell(20, 6, 'THIRD TERM', 1, 0, 'C');
        $pdf->Cell(20, 6, 'SESSION TOTAL', 1, 0, 'C');
        $pdf->Cell(20, 6, 'AVERAGE', 1, 0, 'C');
        $pdf->Cell(20, 6, 'GRADE', 1, 0, 'C');
        $pdf->Cell(20, 6, 'REMARK', 1, 1, 'C');
        $pdf->SetFont('Rubik-Regular', '', 9);

        foreach ($scores as $score) {
            $sum = $score->ft_tot +  $score->st_tot +  $score->tt_tot;
            $score_count = 0;
            $terms = ['ft', 'st', 'tt'];
           
            foreach ($terms as $tm) {
                if (Utility::notEmpty($score->{$tm . '_tot'})) {
                    $score_count++;
                }
            }
           $devisor = ($score_count === 0)?1:$score_count;
            $avg = round($sum / $devisor,2);
            $gd = Result::get_grade($avg);
            $rm = Result::get_remark($gd);
            $pdf->Cell(50, 5, strtoupper($score->subject), 1, 0, 'L');
            if($score_count == 0){
                $pdf->Cell(20, 5, '-', 1, 0, 'C');
                $pdf->Cell(20, 5, '-', 1, 0, 'C');
                $pdf->Cell(20, 5, '-', 1, 0, 'C');
                $pdf->Cell(20, 5,'', 1, 0, 'C');
                $pdf->Cell(20, 5, '', 1, 0, 'C');
                $pdf->Cell(20, 5, '', 1, 0, 'C');
                $pdf->Cell(20, 5, '', 1, 1, 'C');
            }else{

                $pdf->Cell(20, 5, $score->ft_tot, 1, 0, 'C');
                $pdf->Cell(20, 5, $score->st_tot, 1, 0, 'C');
                $pdf->Cell(20, 5, $score->tt_tot, 1, 0, 'C');
                $pdf->Cell(20, 5, $sum, 1, 0, 'C');
                $pdf->Cell(20, 5,$avg, 1, 0, 'C');
                switch ($gd) {
                    case 'A1':
                        $pdf->SetTextColor(0, 204, 0);
                        break;
                    case 'F9':
                        $pdf->SetTextColor(204, 0, 0);
                }
                $pdf->Cell(20, 5, $gd, 1, 0, 'C');
                $pdf->Cell(20, 5, $rm, 1, 1, 'C');
            }
           
            $pdf->SetTextColor(0, 0, 0);
        }
       
     
       
        $pdf->Ln(8);
        $pdf->SetFont('Lusitana-Bold', '', 11);
        $pdf->Cell(190, 6, 'Chart', 0, 1, 'C');
        $bar_image_path = 'charts/bar_'.Token::create(5).'.png';
        $bar_image_paths[] = $bar_image_path;
        $bar_image = $chart->bar_chart(400,250, [$ft_totals, $st_totals, $tt_totals], 'subjects', 'scores', $subjects,true,$bar_image_path);
        $pdf->Ln(2);
        $y = $pdf->GetY();
        $pdf->Image($bar_image_path, 10, $y, 190, 55);
        $y = $y + 55 + 5;
        $pdf->SetXY(10, $y);
        $pdf->SetFont('Lusitana-Bold', '', 8);
        $pdf->SetY(283);
        $pdf->Cell(25, 5, 'HOS (Sign/Date):', 0, 0, 'L');
        if (file_exists('staff/uploads/signatures/' . $agg_data->tea_signature)) {
            $pdf->Image('management/hos/uploads/signatures/' . $agg_data->hos_signature, 36, 277, 15, 9);
        }
        $pdf->SetX(61);
        $pdf->Cell(30, 5, 'Teacher (Sign/Date):', 0, 0, 'L');
        if (file_exists('staff/uploads/signatures/' . $agg_data->tea_signature)) {
            $pdf->Image('staff/uploads/signatures/' . $agg_data->tea_signature, 90, 277, 15, 9);
        }
        

        $pdf->Image('barcodes/' . $title, 185, 277, 15, 15);
        //end of images
    }
}
$pdf->Output('I', 'results.pdf');
foreach($bar_image_paths as $b_i_p){
    unlink($b_i_p);
}
