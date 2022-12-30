<?php
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
include_once('../../../../libraries/vendor/autoload.php');
//end of initializatons
header("Content-Type: application/json; charset=UTF-8");
$message = '';
$status = 400;
$data = [];
$db = DB::get_instance();
$dir = new Director();
$url = new Url();
$val = new Validation();
$mail = new Email();


if (Input::submitted() && Token::check(Input::get('token'))) {
    $op = Input::get('op');
    switch ($op) {
        case 'change_password':
            $password = Utility::escape(Input::get('password'));
            $new_password = Utility::escape(Input::get('new_password'));
            $username = Utility::escape(Input::get('username'));
            $rules = [
                'password' => [
                    'name' => 'Password',
                    'required' => true,
                    'pattern' => '^[A-Za-z0-9]+$'
                ],
                'new_password' => [
                    'name' => 'New Password',
                    'required' => true,
                    'pattern' => '^[A-Za-z0-9]+$',
                    'min' => 6,
                    'max' => 32
                ]
            ];
            if ($val->check($rules)) {
                $db_pwd = $db->get('management', 'password', "mgt_id='$username'")->password;
                if (password_verify($password, $db_pwd)) {
                    $db->update('management', ['password' => password_hash($new_password, PASSWORD_DEFAULT)]);
                    echo response(204, 'Successfully changed password');
                } else {
                    echo response(400, 'Present Password is incorrectly entered');
                }
            } else {
                $errors = $val->errors();
                echo response(400, implode('<br />', $errors));
            }
            break;
        case 'update_account':
            $rules = [
                'fname' => [
                    'name' => 'First Name',
                    'required' => true,
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z]+$'
                ],
                'oname' => [
                    'name' => 'Other Name',
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z]+$'
                ],
                'lname' => [
                    'name' => 'Last Name',
                    'required' => true,
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z]+$'
                ],
                'title' => [
                    'name' => 'Title',
                    'required' => true,
                    'pattern' => '^[a-zA-Z]+$'
                ],
                'dob' => [
                    'name' => 'Date of Birth',
                    'required' => true
                ],
                'state' => [
                    'name' => 'State',
                    'required' => true
                ],
                'lga' => [
                    'name' => 'LGA',
                    'required' => true
                ],
                'phone' => [
                    'name' => 'Phone',
                    'required' => true,
                    'size' => 11,
                    'pattern' => '^[0-9]{11}$'
                ],
                'email' => [
                    'name' => 'Email',
                    'required' => true,
                    'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'
                ],
                'choosen_email' => [
                    'name' => 'Preffered Email',
                    'required' => true,
                    'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'
                ],
                'account' => [
                    'name' => 'Account No',
                    'required' => true,
                    'pattern' => '^[0-9]{10}$'
                ],
                'bank' => [
                    'name' => 'Bank',
                    'required' => true,
                ]

            ];
            $fileValues = [
                'picture' => [
                    'name' => 'Picture',
                    'required' => false,
                    'maxSize' => 100,
                    'extension' => ['jpg', 'jpeg', 'png']
                ]
            ];
            if ($val->check($rules) && $val->checkFile($fileValues) && Utility::noScript(Input::get('address'))) {
                $fname = Utility::escape(Input::get('fname'));
                $lname = Utility::escape(Input::get('lname'));
                $oname = Utility::escape(Input::get('oname'));
                $title = Utility::escape(Input::get('title'));
                $email = Utility::escape(Input::get('email'));
                $choosen_email = Utility::escape(Input::get('choosen_email'));
                $address = Utility::escape(Input::get('address'));
                $state = Utility::escape(Input::get('state'));
                $lga = Utility::escape(Input::get('lga'));
                $dob = Utility::escape(Input::get('dob'));
                $phone = Utility::escape(Input::get('phone'));
                $account = Utility::escape(Input::get('account'));
                $bank = Utility::escape(Input::get('bank'));
                $username = Utility::escape(Input::get('username'));
                $values = [
                    'fname' => $fname,
                    'lname' => $lname,
                    'oname' => $oname,
                    'title' => $title,
                    'email' => $email,
                    'choosen_email' => $choosen_email,
                    'address' => $address,
                    'state' => $state,
                    'lga' => $lga,
                    'dob' => $dob,
                    'phone' => $phone,
                ];
                if (!empty($_FILES['picture']['name'])) {
                    $file = new File('picture');
                    $pictureName = $username . '.' . $file->extension();
                    $values['picture'] = $pictureName;
                    $db->update('management', $values, "mgt_id='$username'");
                    $file_path = '../../uploads/passports/' . $pictureName;
                    $file->move($file_path);
                } else {
                    $db->update('management', $values, "mgt_id='$username'");
                }
                $db->update('account', ['no' => $account, 'bank' => $bank]);
                echo response(201, 'Update was successful');
            } else {
                $errors = implode('<br />', $val->errors());
                echo response(500, $errors);
            }
            break;
        case 'set_messaging_permission':
            $menu_id = Input::get('menu_id');
            $type = Input::get('type');
            $checked = (int)Input::get('checked');
            $subordinate = Utility::escape(Input::get('subordinate'));
            $db = DB::get_instance();
            $alert = new Alert();
            if ($db->update('messaging_permission', [$type => $checked], "id=" . $menu_id)) {
                $status = ($checked == 1) ? 'enabled to send' : 'disabled from sending';
                $alert->send($subordinate, 'Messaging Permissions', 'You have been ' . $status . ' ' . strtolower($type));
                echo response(204, '');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
        case 'sack_management_member':
            $user_id = Utility::escape(Input::get('id'));
            $member_detail = $db->get('management', 'email,fname,lname,oname,rank,title', "mgt_id = '$user_id'");
            $db->update('management',['active'=>0],"mgt_id = '$user_id'");
            $mail->send($member_detail->email, 'Management Decision', 'Dear Sir/Ma, The management writes to inform you that you have been relived of your duties. In other words, you have been sacked. We wish you better days ahead');
            $alert = new Alert(true);
            $alert->sendToRank(6, 'Management Decision', 'This is to notify you that ' . $member_detail->title . '. ' . Utility::formatName($member_detail->fname, $member_detail->oname, $member_detail->lname) . ' (' . User::getFullPosition($member_detail->rank) . ') have been relived of his duties');
            echo response(204, '<p>Member have been sacked</p><p>The HR department has been notified</p><p>An email have been sent to the sacked member regarding this</p>');
            break;
        case 'expel_student':
            $user_id = Utility::escape(Input::get('id'));
            $db->query('select student2.email,student.fname,student.lname,student.oname,student.rank, student.sch_abbr,student.level,class.class from student inner join student2 on student.std_id = student2.std_id inner join class on class.id=student.class_id where student.std_id = ?', [$user_id]);
            $student_detail = $db->one_result();
            $db->update('student',['active'=>1],"std_id = '$user_id'");
            $mail->send($student_detail->email, 'Management Decision', 'Dear ' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname) . ', The school writes to inform you that you have been expelled from ' . School::getFullName($student_detail->sch_abbr) . '. We wish you better days ahead');
            $alert = new Alert(true);
            $alert->sendToRank(2, 'Management Decision', 'This is to notify you that ' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname) . ' of ' . School::getFullName($student_detail->sch_abbr) . ' (' . School::getLevelName($student_detail->sch_abbr, (int)$student_detail->level) . ' ' . $student_detail->class . ' has been expelled');
            echo response(204, '<p>Member have been sacked. <p><p>The HR department has been notified</p><p>An email have been sent to the sacked member regarding this</p>');
            break;

        case 'delete_management_member':
            $user_id = Utility::escape(Input::get('id'));
            $picture = $db->get('management','picture',"mgt_id='$user_id'")->picture;
            $sql1 = 'delete from management where mgt_id =?';
            $sql2 = 'delete from account where receiver = ?';
            $sql3 = 'delete from alert where receiver_id =?';
            $sql4 = 'delete from mgt_cookie where mgt_id =?';
            $sql5 = 'delete from messaging_permission where user_id =?';
            $sql6 = 'delete from request where requester_id =?';
            $sql7 = 'delete from request2 where requester_id =?';
            $sql8 = 'delete from users where user_id =?';
            $sql9 = 'delete from users_menu where user_id =?';
            if ($db->trans_query([[$sql1, [$user_id]], [$sql2, [$user_id]], [$sql3, [$user_id]], [$sql4, [$user_id]], [$sql5, [$user_id]], [$sql6, [$user_id]], [$sql7, [$user_id]], [$sql8, [$user_id]], [$sql9, [$user_id]]])) {
                unlink('../../uploads/passports/'.$picture);
                $alert = new Alert(true);
                echo response(204, '<p>Member have been deleted. </p>');
            } else {
                echo response(500, 'Something went wrong');
            }

            break;
        
        case 'delete_student':
            $user_id = Utility::escape(Input::get('id'));
            $picture = $db->get('student', 'picture', "std_id='$user_id'")->picture;
            $util = new Utils();
            $sch_abbr = Utility::escape(Input::get('school'));
            $formatted_session = $util->getFormattedSession($sch_abbr).'_score';
            $sql1 = 'delete from student where std_id =?';
            $sql2 = 'delete from student2 where std_id = ?';
            $sql3 = 'delete from alert where receiver_id =?';
            $sql4 = 'delete from std_cookie where mgt_id =?';
            $sql5 = 'delete from student_psy where std_id =?';
            $sql6 = 'delete from request where requester_id =?';
            $sql7 = 'delete from request2 where requester_id =?';
            $sql8 = 'delete from users where user_id =?';
            $sql9 = 'delete from users_menu where user_id =?';
            $sql10 = 'delete from  '.$formatted_session.' where std_id =?';
            if ($db->trans_query([[$sql1, [$user_id]], [$sql2, [$user_id]], [$sql3, [$user_id]], [$sql4, [$user_id]], [$sql5, [$user_id]], [$sql6, [$user_id]], [$sql7, [$user_id]], [$sql8, [$user_id]], [$sql9, [$user_id]], [$sq10, [$user_id]]])) {
                unlink('../../../student/uploads/passports/' . $picture);
                $alert = new Alert(true);
                echo response(204, '<p>Student have been deleted. </p>');
            } else {
                echo response(500, 'Something went wrong');
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
