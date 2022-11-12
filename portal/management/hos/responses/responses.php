<?php
//initializations
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
$hos = new Hos();
$url = new Url();
$val = new Validation();
if (Input::submitted()) {
    $op = Input::get('op');
    $sch_abbr = Input::get('school');
    switch ($op) {
        case 'delete_class':
            $classId = Utility::escape(Input::get('classid'));
            $msg = '';
            $val = new Validation();
            $values = [
                'classid' => [
                    'name' => 'Class',
                    'required' => true,
                    'exists' => 'id/class'
                ]
            ];
            if ($val->check($values)) {
                if ($hos->deleteClass($classId, $sch_abbr)) {
                    $msg = 'Deletion was successful';
                    echo response(204, $msg);
                } else {
                    $msg = 'Error encountered: Deletion not successful';
                    echo response(500, $msg);
                }
            } else {
                $errors = $val->errors();
                foreach ($errors as $error) {
                    $msg .= $error . '<br>';
                }
                echo response(400, $msg);
            }
            break;
        case 'update_schedules':
            $errCode = 0;
            $genMsg = '';
            $utils = new Utils();
            if (Input::submitted() && Token::check(Input::get('token'))) {
                $fa = (int)Input::get('fa');
                $sa = (int)Input::get('sa');
                $ft = (int)Input::get('ft');
                $st = (int)Input::get('st');
                $pro = (int)Input::get('pro');
                $exam = (int)Input::get('exam');
                $ftto = (int)Input::get('ftto');
                $stto = (int)Input::get('stto');
                $ttto = (int)Input::get('ttto');
                $ftrd = Utility::escape(Input::get('ftrd'));
                $strd = Utility::escape(Input::get('strd'));
                $ttrd = Utility::escape(Input::get('ttrd'));
                $ftcd = Utility::escape(Input::get('ftcd'));
                $stcd = Utility::escape(Input::get('stcd'));
                $ttcd = Utility::escape(Input::get('ttcd'));
                $a1 = Utility::escape(Input::get('a1'));
                $b2 = Utility::escape(Input::get('b2'));
                $b3 = Utility::escape(Input::get('b3'));
                $c4 = Utility::escape(Input::get('c4'));
                $c5 = Utility::escape(Input::get('c5'));
                $c6 = Utility::escape(Input::get('c6'));
                $d7 = Utility::escape(Input::get('d7'));
                $e8 = Utility::escape(Input::get('e8'));
                $f9 = Utility::escape(Input::get('f9'));
                $sch_abbr = Utility::escape(Input::get('school'));
                $currTerm = Utility::escape(Input::get('current_term'));
                $resetScores = Utility::escape(Input::get('resetScores'));

                if (($fa + $sa + $ft + $st + $pro + $exam) != 100) {
                    $errCode = 1;
                    $genMsg = '<div class="failure">The scores must sum up to 100</div>';
                    echo response(400, $genMsg);
                } else {
                    $scoreTable = $utils->getFormattedSession($sch_abbr) . '_score'; //the current score table

                    if (!empty($_FILES['signature']['name'])) {
                        $file = new File('signature');
                        $ext = $file->extension();
                        $signatureName = $sch_abbr . '.' . $ext;
                        //update schedule
                        if ($hos->updateSchedule($sch_abbr, $fa, $sa, $ft, $st, $pro, $exam, $ftto, $stto, $ttto, $ftrd, $strd, $ttrd, $ftcd, $stcd, $ttcd, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $signatureName)) {
                            $file->move('../uploads/signatures/' . $signatureName); //move picture to the destination folder
                            //update all the scores of this school from the score table depending on the term
                            if ($resetScores === 'true') {
                                $hos->updateScore($scoreTable, $currTerm);
                            }

                            $genMsg = 'Changes has been successfully updated';
                            Session::set_flash('post_method_success_message', $genMsg);
                            echo response(201);
                        } else {
                            $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                            echo response(400, $genMsg);
                        }
                    } else {
                        //update schedule
                        if ($hos->updateSchedule($sch_abbr, $fa, $sa, $ft, $st, $pro, $exam, $ftto, $stto, $ttto, $ftrd, $strd, $ttrd, $ftcd, $stcd, $ttcd, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9)) {
                            //update all the scores of this school from the score table depending on the term
                            if ($resetScores === 'true') {
                                $hos->updateScore($scoreTable, $currTerm);
                            }
                            $genMsg = 'Changes has been successfully updated';
                            Session::set_flash('post_method_success_message', $genMsg);
                            echo response(201);
                        } else {
                            $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                            echo response(400, $genMsg);
                        }
                    }
                }
            }
            break;
        case 'update_comment':
            $updated_data = json_decode(Input::get('updated_data'), true);
            $term = Utility::escape(Input::get('term'));
            $start = false;
            foreach ($updated_data as $upd_data) {
                if ($start) {
                    $db->requery([Utility::escape($upd_data[1]), Utility::escape($upd_data[0])]);
                } else {
                    $db->query('update student_psy set ' . $term . '_p_com =? where std_id = ?', [Utility::escape($upd_data[1]), Utility::escape($upd_data[0])]);
                    $start = true;
                }
            }
            echo response(201, 'Changes have been saved');
            break;
        case 'view_results':
            $classId = Utility::escape(Input::get('class_id'));
            $school = Utility::escape(Input::get('school'));
            $students = Result::get_ids($classId,$school);
            echo response(200, '',$students);
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
    }
} else {
    echo response(400, 'Invalid request method');
}


function response(int $status, $message = '', array $data = [])
{
    return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'token' => Token::generate()]);
}
