<?php
//initializations

spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');

if (Input::submitted('get')) {
    $token = Utility::escape(Input::get('token'));
    $identifier = Utility::escape(Input::get('identifier'));
    if (!empty($identifier) && QRC::verify($identifier, $token)) {
        $other_data = QRC::get_other_data($identifier);
        $adm_id = $other_data['adm_id'];
        $sch_abbr = $other_data['school'];
    } else if (Token::check($token)) {
        $adm_id = Utility::escape(Input::get('adm_id'));
        $sch_abbr = Utility::escape(Input::get('school'));
    } else {
        Redirect::to(404);
    }
} else {
    Redirect::to(404);
}

require_once 'fpdf/html2pdf.php';
require_once 'phpqrcode/qrlib.php';
require_once 'hijridatelib/hijri.class.php';
$cal = new hijri\Calendar();
$pdf = new PDF_HTML();
$url = new Url();
$adm = new Admission();
$adm_data = $adm->get_admission_data($adm_id,$sch_abbr);

//add fonts
$pdf->AddFont('Heebo-Regular', '', 'Heebo-Regular.php');
$pdf->AddFont('BreeSerif-Regular', '', 'BreeSerif-Regular.php');
$pdf->AddFont('Lusitana-Bold', '', 'Lusitana-Bold.php');
$pdf->AddFont('Rubik', '', 'Rubik-Regular.php');
$pdf->AddFont('Rubik', 'B', 'Rubik-Medium.php');
$pdf->AddFont('HindSiliguri', '', 'HindSiliguri-Medium.php');;

$std_prefix = School::get_std_prefix($sch_abbr);
$school_name = strtoupper(School::getFullName($sch_abbr));

$pdf->AddPage();
$address = $adm_data->address;
$phone_and_email = 'TELEPHONE: ' . $adm_data->phone_contacts . '. EMAIL: ' . $adm_data->email;
$fname = $adm_data->fname;
$lname = $adm_data->lname;
$oname = $adm_data->oname;
$fathername = $adm_data->fathername;
$level_name = School::getLevelName($sch_abbr, $adm_data->level);
$title = Utility::format_student_id($adm_id) . '_admission.png';
$qr_data = QRC::get_code($title, ['adm_id' => $adm_id, 'school' => $sch_abbr]);
$link = $url->to('students_admission.php?identifier=' . $qr_data->identifier . '&token=' . $qr_data->token, 0);
if (!file_exists('barcodes/' . $title)) {
    QRcode::png($link, 'barcodes/' . $title);
}



$pdf->Image('management/apm/uploads/logo/hikmah.jpg', 10, 5, 15, 15);
$pdf->Image('management/apm/uploads/logo/' . $adm_data->logo, 185, 5, 15, 15);
$pdf->SetFont('BreeSerif-Regular', '', 15);
$pdf->Cell(190, 9, $school_name, 0, 1, 'C');
$pdf->SetTextColor(18, 34, 204);
$pdf->SetFont('Heebo-Regular', '', 7);
$pdf->Cell(190, 4, $address, 0, 1, 'C');
$pdf->SetFont('Heebo-Regular', '', 7);
$pdf->Cell(190, 4, $phone_and_email, 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(9);
$pdf->SetFont('Rubik', '', 11);
$greg_date_arr = explode('-',$adm_data->date_of_admission);
$hijri_date_arr = $cal->GregorianToHijri($greg_date_arr[0],$greg_date_arr[1],$greg_date_arr[2]);
$hijri_date = Utility::formatDate($adm_data->date_of_admission,1).' ('.$hijri_date_arr['d'].Utility::numSuffix((string)$hijri_date_arr['d']).' '.$cal->month_name($hijri_date_arr['m'],'en').', '.$hijri_date_arr['y'].')';
$pdf->Ln(5);
$pdf->Cell(190, 7,$hijri_date, 0, 1, 'R');
$pdf->Cell(190, 7,'Mallam '.ucwords($adm_data->fathername).',', 0, 1, 'L');
$pdf->Cell(190, 7, 'As-salaamu alaykum,', 0, 1, 'L');
$pdf->Ln();
$pdf->SetFont('Rubik', 'B', 11);
$pdf->Cell(190, 6, 'OFFER OF PROVISIONAL ADMISSION', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Rubik', '', 11);
$adm_message = 'We are pleased to congratulate and inform you that <strong>'.Utility::formatName($adm_data->fname,$adm_data->oname,$adm_data->lname).'</strong>, has been offered admission into <strong>'.$level_name. '</strong> of <strong>'.ucwords(strtolower($school_name)). '</strong>.
<p>After going through the screening exercise, we are happy to let you know that she is suitable for this admission and has acquired it on merit.</p>
<p> We wish to remind you that our philosophy is based on the core values of learning and character whose purpose is to educate and inspire children to become confident, enthusiastic and responsible members of the society.</p>

<p>By entrusting your child with us, we shall offer them an imaginative and challenging curriculum that focuses on academic excellence, Islamic morals, creativity, physical fitness and service to the community.</p>

<p>Meanwhile, to make rules and regulations clearer to parents, we have attached a copy of our policy guidance to this letter. It should be noted that your child\'s admission is subject to the acceptance by you of the terms and conditions set out in the school policy guidance. You are therefore advised to read these terms and conditions before accepting this offer.</p>

<p>You should accept this offer within <strong>14 days</strong> of this letter. If we do not hear from you within this stipulated time, the school reserves the right to withdraw the offer. To accept the offer, simply login to the portal and check the <strong>Application</strong> Section on the menu bar.</p>

<p>Please, feel free to contact us any time if you have any questions or would like to learn more about the School and its interesting programs.</p>
<p>Thank you for your interest in <strong>'.ucwords(strtolower($school_name)). '</strong> and we look forward to working with you.</p>
<p><strong>Barakallaahu Feekum</strong></p>
<p><strong>Admission Manager,<br />Hikmah Schools, Jos.<br />'.trim($adm_data->phone_contacts).'</strong></p>
';
$pdf->WriteHTML($adm_message);
$y = $pdf->GetY();
$pdf->Image('barcodes/' . $title, 185, $y, 15, 15);
$pdf->Output('I', 'admission_letter.pdf');
