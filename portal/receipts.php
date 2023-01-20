<?php
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
require_once 'fpdf/fpdf.php';
require_once 'phpqrcode/qrlib.php';
$url = new Url();

$signature_link = $url->to('accountant/signature/', 1) . Account::getSignature();

$margin = 12; //serve as a horizontal margin
$db = DB::get_instance();
if (Input::submitted('get')) {
    $token = Utility::escape(Input::get('token'));
    $identifier = Utility::escape(Input::get('identifier'));
    if (!empty($identifier) && QRC::verify($identifier, $token)) {
        $other_data = QRC::get_other_data($identifier);
        $trans_id = $other_data['trans_id'];
    } else if (Token::check($token, null,true)) {
        $trans_id = Utility::escape(Input::get('trans_id'));
    } else {
        Redirect::to(404);
    }
} else {
    Redirect::to(404);
}

$trans_det = $db->get('transaction', '*', "trans_id='$trans_id'");
$trans_cat = (int)$trans_det->category;
$cat_with_sch_details = [1, 2, 3, 4, 5];
if (in_array($trans_cat, $cat_with_sch_details)) {
    if ($trans_cat == 2 || $trans_cat == 3) {
        $sch_abbr = $trans_det->payer; //school are payers to staffs or management
    }else{
        $sch_abbr = $trans_det->receiver;//students are payers to school
    }
    $sch_detail = $db->get('school', '*', "sch_abbr='$sch_abbr'");
    $address = $sch_detail->address;
    $email = $sch_detail->email;
    $phone_contacts = $sch_detail->phone_contacts;
    $school = School::getFullName($sch_abbr);
} else {
    $sch_abbr = Config::get('receipts/default_school');
    $sch_detail = $db->get('school', '*', "sch_abbr='$sch_abbr'");
    $school = 'HIKMAH EDUCATION SERVICES';
    $address = $sch_detail->address;
    $email = $sch_detail->email;
    $phone_contacts = $sch_detail->phone_contacts;
}

$date = Utility::formatFullDate($trans_det->created, true);
$title = 'receipt_' . $trans_id . '.png';

$qr_data = QRC::get_code($title, ['trans_id' => $trans_id]);

$link = $url->to('receipts.php?identifier=' . $qr_data->identifier . '&token=' . $qr_data->token, 0);

if (!file_exists('barcodes/' . $title)) {
    QRcode::png($link, 'barcodes/' . $title);
}

$pdf = new FPDF();
$pdf->AddPage('P');
//add fonts
$pdf->AddFont('Rowdies-Regular', '', 'Rowdies-Regular.php');
$pdf->AddFont('Nunito-Medium', '', 'Nunito-Medium.php');
$pdf->AddFont('FjallaOne-Regular', '', 'FjallaOne-Regular.php');
$pdf->AddFont('Exo2-VariableFont_wght', '', 'Exo2-VariableFont_wght.php');
$pdf->AddFont('FiraSans-Medium', '', 'FiraSans-Medium.php');
//end of add fonts
$pdf->Rect(10, 10, 190, 277, 'D');
$pdf->SetFont('Rowdies-Regular', '', 15);
$pdf->Cell(190, 15, ucwords($school), 0, 1, 'C');
$pdf->setY(21);
$pdf->SetFont('Nunito-Medium', '', 9);
$pdf->Cell(190, 4, ucwords(strtolower($address)), 0, 1, 'C');
$pdf->Cell(190, 4, $phone_contacts . ' | ' . strtolower($email), 0, 0, 'C');
$message_data = message_details($trans_det, $cat_with_sch_details);
$pdf->SetY(60);
$pdf->SetFont('FjallaOne-Regular', '', 15);
$pdf->Cell(190, 15, $message_data['title'], 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('FiraSans-Medium', '', 11);
$pdf->SetX($margin);
$pdf->Cell(28, 11, 'Transaction ID ', 0, 0, 'L');
$pdf->SetFont('Exo2-VariableFont_wght', '', 11);
$pdf->Cell(40, 11, $trans_id, 0, 0, 'L');
$pdf->SetX(138);
$pdf->SetFont('FiraSans-Medium', '', 11);
$pdf->Cell(12, 11, 'Date ', 0, 0, 'L');
$pdf->SetFont('Exo2-VariableFont_wght', '', 11);
$pdf->Cell(49, 11, $date, 0, 1, 'L');
$pdf->Ln(3);
$pdf->SetX($margin);
$pdf->MultiCell(210 - 20 - $margin, 7, $message_data['message'], 0, 'L');
$pdf->Ln(21);
$y = $pdf->GetY();
$pdf->Image($signature_link, $margin, $y, 25, 20);
$pdf->Image('barcodes/' . $title, 210 - $margin - 20, $y, 20, 20);
$pdf->Output('D', 'receipt.pdf');

function getName(string $person)
{
    $category = Payment::getPayerOrRecipientCategory($person);
    $db = DB::get_instance();
    switch ($category) {
        case 'student':
            $res = $db->get('student', 'fname,oname,lname', "std_id='$person'");
            return Utility::formatName($res->fname, $res->oname, $res->lname);
        case 'staff':
            $res = $db->get('staff', 'title,fname,oname,lname', "staff_id='$person'");
            return $res->title . '. ' . Utility::formatName($res->fname, $res->oname, $res->lname, false);
        case 'management':
            $res = $db->get('management', 'title,fname,oname,lname', "mgt_id='$person'");
            return $res->title . '. ' . Utility::formatName($res->fname, $res->oname, $res->lname, false);
        default:
            return $person;
    }
}

function message_details(object $data, $allowed_cat): array
{
    $db = DB::get_instance();
    $trans_cat = (int)$data->category;
    if (in_array($trans_cat, $allowed_cat)) {
        $payers_name = getName($data->payer);
        $receivers_name = getName($data->receiver);
    } else {
        $payers_name = $data->payer;
        $receivers_name = $data->receiver;
    }
    
    $message = 'This is to acknowledge that ' . $payers_name . ' payed the total sum of N' . number_format((float)$data->amount, 2) . ' to ' . $receivers_name;
    $title = '';
    
    switch ((int) $data->category) {
        case 1:
            $other_data = $db->get('school_fee', 'term,session', "id=" . $data->school_fee_id);
            $title = 'PAYMENT SLIP';
            $message .= ' as school fee for ' . strtolower(Utility::formatTerm($other_data->term)) . ', ' . $other_data->session . ' session';
            break;
        case 2:
        case 3:
            $db->query('select payment_months.month, payment_months.year from payment_months inner join salary on payment_months.id = salary.payment_month inner join transaction on transaction.receiver = salary.receiver where transaction.trans_id = ?', [$data->trans_id]);
            $other_data = $db->one_result();
            $title = 'SALARY SLIP';
            $message .= ' as part/full salary for the month of ' . $other_data->month . ', ' . $other_data->year;
            break;
        case 4:
            $other_data = $db->get('reg_fee', 'session', "std_id='$data->payer'");
            $title = 'REGISTRATION SLIP';
            $message .= ' as registration fee during ' . $other_data->session . ' session';
            break;
        case 5:
            $other_data = $db->get('session', 'session', "sch_abbr='$data->receiver' and current = 1");
            $title = 'PAYMENT SLIP';
            $message .=' as form fee for during ' . $other_data->session . ' session';
            break;
        case 6:
            $title = 'WITHDRAWAL SLIP';
            $message = 'This is to acknowledge that ' . $receivers_name . ' withdrew the total sum of N' . number_format((float)$data->amount, 2) . ' from ' . $payers_name . ' on ' . Utility::formatFullDate($data->created, true);
            break;
        case 7:
            $title = 'DEPOSIT SLIP';
            $message = 'This is to acknowledge that ' . $payers_name . ' deposited the total sum of N' . number_format((float)$data->amount, 2) . ' to ' . $receivers_name . ' account on ' . Utility::formatFullDate($data->created, true);
            break;
        case 8:
            $title = 'TRANSFER SLIP';
            $message = 'This is to acknowledge that the total sum of N' . number_format((float)$data->amount, 2) . ' was transferred from ' . $payers_name . ' account to ' . $receivers_name . ' account on ' . Utility::formatFullDate($data->created, true);
            break;
    }
    return ['title' => $title, 'message' => $message];
}
