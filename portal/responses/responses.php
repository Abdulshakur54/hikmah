<?php
//initializations

spl_autoload_register(
    function ($class) {
        require_once '../../classes/' . $class . '.php';
    }
);
include_once('../../libraries/vendor/autoload.php');
session_start(Config::get('session/options'));
//end of initializatons
header("Content-Type: application/json; charset=UTF-8");
$message = '';
$status = 400;
$data = [];
$db = DB::get_instance();
$mail = new Email();

if (Input::submitted() && Token::check(Input::get('token'))) {
    $op = Input::get('op');
    switch ($op) {
        case 'get_lga_list':
            $state_id = Utility::escape(Input::get('state_id'));
            $lgas = Utility::getLgas($state_id);
            echo response(200, '', $lgas);
            break;
        case 'set_checked':
            $menu_id = Input::get('menu_id');
            $checked = (int)Input::get('checked');
            if ($db->update('users_menu', ['shown' => $checked], "id = $menu_id")) {
                echo response(204, '');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
        case 'send_message':
            $checked = (int)Input::get('checked');
            $message_type = Utility::escape(Input::get('message_type'));
            $message_type = strtolower($message_type);
            $title = Utility::escape(Input::get('title'));
            $message = Utility::escape(Input::get('message'));
            $sender = Utility::escape(Input::get('sender'));
            $user_table = Utility::escape(Input::get('user_table'));
            $recipients = (array) json_decode(Input::get('recipients'));
            $recipients_csv = implode(',', $recipients);
            $sender_emails = $db->get('management', 'choosen_email,email', "mgt_id='$sender'");
            $sender_email = (!empty($sender_emails->choosen_email)) ? $sender_emails->choosen_email : $sender_emails->email;


            switch ($user_table) {
                case 'management':
                    $contacts = $db->select($user_table, 'phone,email', "mgt_id IN('" . $recipients_csv . "')");
                    break;
                case 'staff':
                    $contacts = $db->select($user_table, 'phone,email', "staff_id IN('" . $recipients_csv . "')");
                    break;
                case 'student':
                    $contacts = $db->select('student2', 'phone,email', "std_id IN('" . $recipients_csv . "')");
                    break;
                default:
                    $contacts = [];
            }
            switch ($message_type) {
                case 'sms':
                    if ($db->get('messaging_permission', 'sms', "user_id='$sender'")->sms != 1) {
                        echo response(500, "You don't have permission to send SMS");
                        exit();
                    }
                    $phones = Utility::convertToArray($contacts, 'phone');
                    $phones_csv = implode(',', $phones);
                    $rsp = SMS::send($message, $phones_csv);
                    echo response($rsp['status'], $rsp['message']);
                    break;
                case 'email':
                    if ($db->get('messaging_permission', 'email', "user_id='$sender'")->email != 1) {
                        echo response(500, "You don't have permission to send Emails");
                        exit();
                    }
                   
                    $emails = Utility::convertToArray($contacts, 'email');
                    if ($mail->send($emails, $title, $message, ['from' => $sender_email])) {
                        echo response(204, 'Emails sent successfully');
                    } else {
                        echo response(500, $mail->getErrors());
                    }
                    break;
                case 'notification':
                    if ($db->get('messaging_permission', 'notification', "user_id='$sender'")->notification != 1) {
                        echo response(500, "You don't have permission to send SMS");
                        exit();
                    }
                    $alert = new Alert();
                    $start = false;
                    foreach ($recipients as $recipient) {
                        if ($start) {
                            $alert->send($recipient, $title, $message, true);
                        } else {
                            $alert->send($recipient, $title, $message);
                        }
                    }
                    echo response(204, 'Notifications sent successfully');
                    break;
            }
            break;
    }
} else {
    echo response(400, 'Invalid request method');
}


function response(int $status, $message = '', array $data = [])
{
    return Utility::response($status, $message, $data);
}
