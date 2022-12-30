<?php
//initializations

use Monolog\Handler\Curl\Util;

spl_autoload_register(
    function ($class) {
        require_once '../../../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializatons
header("Content-Type: application/json; charset=UTF-8");
$message = '';
$status = 400;
$data = [];
$db = DB::get_instance();
$apm = new Apm();
$url = new Url();
$val = new Validation();

if (Input::submitted() && Token::check(Input::get('token'))) {
    $op = Input::get('op');
    switch ($op) {
        case 'delete_attachment':
            $agg = new Aggregate();
            $idToDel = Utility::escape(Input::get('id'));
            $fileToDel = $agg->lookUp('attachment', 'attachment', 'id,=,' . $idToDel);
            $apm->delAttachment($idToDel);
            unlink('../uploads/attachment/' . $fileToDel); //deletes file from the server
            echo response(204, 'Attachment have been successfully deleted');
            break;
        case 'add_attachment':

            $sch_abbr = Utility::escape(Input::get('school'));
            $level = Utility::escape(Input::get('level'));
            $agg = new Aggregate();
            $val = new Validation();
            $fileValues = [
                'selectedFile' => [
                    'name' => 'File',
                    'required' => true,
                    'maxSize' => 3072
                ]
            ];
            if ($val->checkFile($fileValues)) {
                $selectedFile = new File('selectedFile');
                $name = $selectedFile->name();
                $att_name = 'f' . ($agg->max('id', 'attachment') + 1) . '_' . $name;
                //input the values into attachment table and also store the file in attachment folder
                $apm->insertAttachment($att_name, $name, $sch_abbr, $level);
                $selectedFile->move('../uploads/attachment/' . $att_name);
                echo response(204);
            } else {
                $errors = $val->errors();
                foreach ($errors as $error) {
                    $msg .= $error . '<br>';
                }
                echo response(400, $msg);
            }
            break;
        case 'save_schedules':

            $formvalues = [
                'term' => [
                    'name' => 'Current Term',
                    'required' => true,
                    'in' => ['ft', 'st', 'tt'],
                ],
                'formfee' => [
                    'name' => 'Form Fee',
                    'required' => true,
                    'pattern' => '^[0-9.]+$'
                ],
                'regfee' => [
                    'name' => 'Registration Fee',
                    'required' => true,
                    'pattern' => '^[0-9.]+$'
                ],
                'ftsf' => [
                    'name' => 'First Term School Fee',
                    'required' => true,
                    'pattern' => '^[0-9.]+$'
                ],
                'stsf' => [
                    'name' => 'Second Term School Fee',
                    'required' => true,
                    'pattern' => '^[0-9.]+$'
                ],
                'ttsf' => [
                    'name' => 'Third Term School Fee',
                    'required' => true,
                    'pattern' => '^[0-9.]+$'
                ]
            ];
            $fileValues = [
                'logo' => [
                    'name' => 'Logo',
                    'required' => false,
                    'maxSize' => 100,
                    'extension' => ['jpg', 'jpeg', 'png']
                ]
            ];


            $val = new Validation();
            $sch_abbr = Input::get('school');
            if ($val->check($formvalues) && $val->checkFile($fileValues)) {
                $term = Utility::escape(Input::get('term'));
                $formFee = Utility::escape(Input::get('formfee'));
                $regFee = Utility::escape(Input::get('regfee'));
                $ftsf = Utility::escape(Input::get('ftsf'));
                $stsf = Utility::escape(Input::get('stsf'));
                $ttsf = Utility::escape(Input::get('ttsf'));
                if (!empty($_FILES['logo']['name'])) {
                    $file = new File('logo');
                    $ext = $file->extension();
                    $logoName = $sch_abbr . '.' . $ext;
                    if ($apm->updateSchedule($sch_abbr, $term, $formFee, $regFee, $ftsf, $stsf, $ttsf, $logoName)) {
                        $file->move('../uploads/logo/' . $logoName); //move picture to the destination folder
                        echo response(204, 'Update was successful');
                    } else {
                        echo response(500, 'An error is preventing changes to being saved');
                    }
                } else {
                    if ($apm->updateSchedule($sch_abbr, $term, $formFee, $regFee, $ftsf, $stsf, $ttsf)) {
                        echo response(204, 'Update was successful');
                    } else {
                        echo response(500, 'An error is preventing changes to being saved');
                    }
                }
            } else {
                $errors = $val->errors();
                foreach ($errors as $error) {
                    $msg .= $error . '<br>';
                }
                echo response(400, $msg);
            }
            break;
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
                    'pattern' => '^[a-zA-Z`]+$'
                ],
                'oname' => [
                    'name' => 'Other Name',
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z`]+$'
                ],
                'lname' => [
                    'name' => 'Last Name',
                    'required' => true,
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z`]+$'
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
        case 'manual_admission':
            require_once "../../../../libraries/vendor/autoload.php";
            $rules = [
                'fname' => [
                    'name' => 'First Name',
                    'required' => true,
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z`]+$'
                ],
                'oname' => [
                    'name' => 'Other Name',
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z`]+$'
                ],
                'lname' => [
                    'name' => 'Last Name',
                    'required' => true,
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z`]+$'
                ],
                'std_id' => [
                    'name' => 'Student ID',
                    'required' => true,
                    'pattern' => '^(HCK|HCB|HA|HIS|HM|HCI|H E-M|HCM|hck|hcb|ha|his|hm|hci|h e-m|hcm)\/[1-9][0-9]\/[1-9]\/[0-9]{3,5}$'
                ],
                'fathername' => [
                    'name' => 'Father Name',
                    'required' => true,
                    'pattern' => '^[a-zA-Z` ]+$'
                ],
                'mothername' => [
                    'name' => 'Mother Name',
                    'required' => true,
                    'pattern' => '^[a-zA-Z` ]+$'
                ],
                'password' => [
                    'name' => 'Password',
                    'required' => true,
                    'pattern' => '^[a-zA-Z0-9]+$',
                    'min' => 6,
                    'max' => 20
                ],
                'dob' => [
                    'name' => 'Date of Birth',
                    'required' => true
                ],
                'doa' => [
                    'name' => 'Date of Admission',
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

                'email' => [
                    'name' => 'Email',
                    'required' => true,
                    'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'
                ]

            ];
            if ($val->check($rules)) {
                $fname = Utility::escape(Input::get('fname'));
                $lname = Utility::escape(Input::get('lname'));
                $oname = Utility::escape(Input::get('oname'));
                $fathername = Utility::escape(Input::get('fathername'));
                $mothername = Utility::escape(Input::get('mothername'));
                $email = Utility::escape(Input::get('email'));
                $state = Utility::escape(Input::get('state'));
                $lga = Utility::escape(Input::get('lga'));
                $std_id = Utility::escape(Input::get('std_id'));
                $std_id = strtoupper($std_id);
                $dob = Utility::escape(Input::get('dob'));
                $password = Utility::escape(Input::get('password'));
                $std_arr = explode('/', $std_id);
                $sch_abbr = strtoupper($std_arr[0]);
                $level = strtoupper($std_arr[2]);
                $doa = Utility::escape(Input::get('doa'));
                $user_rank = Utility::escape(Input::get('rank'));
                $rank = ($user_rank == 2) ? 9 : 10;
                $util = new Utils();
                $adm_id = $util->getSession($sch_abbr) . '/' . 'A' . Admission::genId(); //generates the admission id
                if (!(($user_rank == 2 && in_array($sch_abbr, School::getConvectionalSchools(2))) || ($user_rank == 4 && in_array($sch_abbr, School::getIslamiyahSchools(2))))) {
                    echo response(400, 'Your student Id should reflect your school');
                    exit();
                }
                $sql1 = 'insert into student(fname,lname,oname,adm_id,password,std_id,rank,sch_abbr,level,date_of_admission) values(?,?,?,?,?,?,?,?,?,?)';
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $val1 = [$fname, $lname, $oname, $adm_id, $hashed_password, $std_id, $rank, $sch_abbr, $level, $doa];
                $sql2 = 'insert into student2(std_id,fathername,mothername,dob,email,state,lga) values(?,?,?,?,?,?,?)';
                $val2 = [$std_id, $fathername, $mothername, $dob, $email, $state, $lga];
                $sql3 = 'insert into student_psy(std_id)values(?)';
                $val3 = [$std_id];
                if ($db->trans_query([[$sql1, $val1], [$sql2, $val2], [$sql3, $val3]])) {
                    $mail = new Email();
                    $message = '<p>Congratulations, You have been successfully integrated into Hikmah Web Portal. You can now login to your portal with the link below.</p> <p>It is recommended for you to change your password and update your profile when you login</p>
                    <p>
                    <strong>Username: </strong>' . $std_id . '<br>
                    <strong>Password: </strong>' . $password . '<br>
                    <strong>Login Link: </strong><a href="' . $url->to('login.php', 0) . '">' . $url->to('login.php', 0) . '</a></p>'; //email 
                    $attachments = School::get_attachments($sch_abbr, $level);
                    $attachment_path = '../uploads/attachment';
                    $attachs = [];
                    foreach ($attachments as $attachment) {
                        $attachs[] = $attachment_path . '/' . $attachment->attachment . ', ' . $attachment->name;
                    }
                    if (!empty($attachments)) {
                        $mail->send($email, 'Integration to Hikmah Portal', $message, ['attachment' => $attachs]);
                    } else {

                        $mail->send($email, 'Integration to Hikmah Portal', $message);
                    }
                    echo response(201, 'Student have been admitted <br>An email has been sent to him along with his Student ID and Password');
                } else {
                    echo response(500, 'An error occoured while trying to admit student');
                }
            } else {
                $errors = $val->errors();
                echo response(204, implode("<br />", $errors));
            }
            break;
        case 'expel_student':
            $req = new Request();
            $username = Utility::escape(Input::get('username'));
            $user_id = Utility::escape(Input::get('id'));
            $db->query('select student.std_id,student.fname,student.oname,student.lname,student.rank,student.sch_abbr,student.level,student.class_id,class.class from student inner join class on student.class_id = class.id where student.std_id = ?', [$user_id]);

            $student_detail = $db->one_result();
            $request = '<p>Your permission is needed to expel ' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname) . '.<p>' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname)  . ' is a student of ' . School::getLevelName($student_detail->sch_abbr, (int)$student_detail->level) . ' ' . $student_detail->class . ' of ' . $student_detail->sch_abbr . '.</p>His/Her data would remain in the system but not accessible to the student</p>';
            $other = ['username' => $username, 'user_id' => $user_id];
            $req->send($username, 1, $request, RequestCategory::EXPEL_STUDENT, $other);
            echo response(204, 'A request has been sent to the Director to approve this action');
            break;
        case 'delete_student':
            $req = new Request();
            $username = Utility::escape(Input::get('username'));
            $user_id = Utility::escape(Input::get('id'));
            $db->query('select student.std_id,student.fname,student.oname,student.lname,student.rank,student.sch_abbr,student.level,student.class_id,class.class from student inner join class on student.class_id = class.id where student.std_id = ?', [$user_id]);

            $student_detail = $db->one_result();
            $request = '<p>Your permission is needed to delete ' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname) . ' from the portal along with his/her data.<p>' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname)  . ' is a student of ' . School::getLevelName($student_detail->sch_abbr, (int)$student_detail->level) . ' ' . $student_detail->class . ' of ' . $student_detail->sch_abbr . '.</p>';
            $other = ['username' => $username, 'user_id' => $user_id];
            $req->send($username, 1, $request, RequestCategory::DELETE_STUDENT, $other);
            echo response(204, 'A request has been sent to the Director to approve this action');
            break;
        case 'reset_admission_decision':
            $adm_id = Utility::escape(Input::get('adm_id'));
            if($db->update('admission', ['status' => 0], "adm_id='$adm_id'")){

                echo response(204, 'reset was successful');
            }else{
                echo response(500, 'Something went wrong while trying to reset');
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
