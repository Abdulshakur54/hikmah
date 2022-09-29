<?php
//initializations

spl_autoload_register(
    function ($class) {
        require_once '../../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializatons
header("Content-Type: application/json; charset=UTF-8");
$message = '';
$status = 400;
$data = [];
$db = DB::get_instance();
$url = new Url();
if (Input::submitted() && Token::check(Input::get('token'))) {
    $op = Input::get('op');
    switch ($op) {
        case 'apply_admission':
            require_once "../../../libraries/vendor/autoload.php";
            $rules = [
                'fathername' => ['name' => 'FatherName', 'required' => true, 'pattern' => '^[a-zA-Z` ]+$'],
                'mothername' => ['name' => 'MotherName', 'required' => true, 'pattern' => '^[a-zA-Z` ]+$']
            ];
            $val = new Validation();
            if (!$val->check($rules)) {
                echo response(406, implode('<br />', $val->errors()));
                exit();
            }

            $father_name = Utility::escape(Input::get('fathername'));
            $mother_name = Utility::escape(Input::get('mothername'));
            $username = Utility::escape(Input::get('username'));
            $adm = new Admission();
            $data = $adm->getData($username);
            $sch_abbr = $data->sch_abbr;
            $level = $data->level;
            $email = $data->email;
            if ($adm->apply($father_name, $mother_name, $data->adm_id)) {
                $alert = new Alert(true);
                $message = '<div><p>This is to inform you that ' .
                    Utility::formatName($data->fname, $data->oname, $data->lname) .
                    ' has successfully completed his application</p><p>The Applicant would like to be admitted into ' .
                    School::getLevelName($sch_abbr, $data->level) . ' of ' . School::getFullName($sch_abbr) .
                    '</p><p>You can view the application <a onclick="getPage(\'application_preview.php?adm_id=' . $data->adm_id . '\')" href="#">here</a></p></div>';
                $title = 'Application for Admission';
                $rank = $data->rank;
                if ($rank == 11) {
                    $alert->sendToRank(2, $title, $message);
                } else {
                    if ($rank == 12) {
                        $alert->sendToRank(4, $title, $message);
                    }
                }

                //send email
                $message = '<p>We recieved your application seeking admission into <em>' . School::getLevelName($sch_abbr, $level) . ', ' . School::getFullName($sch_abbr) . '</em></p><p>We are going through it. Expect to hear from us soon</p>'; //email body

                $mail = new Email();
                $mail->send($email, 'Application Recieved', $message);
                echo response(204, 'You have successfully applied for Admission');
            } else {
                echo response(500, 'Something went wrong');
            }

            break;
        case 'accept_admission':
            require_once '../../../libraries/vendor/autoload.php';
            $adm_id = Utility::escape(Input::get('adm_id'));
            $id = Utility::escape(Input::get('id'));
            $adm = new Admission();
            $data = $adm->getData($adm_id);
            $sch_abbr = $data->sch_abbr;
            $std = new Student();
            $newId = $std->genId($sch_abbr, $data->level);
            $newPwd = Token::create(5);
            $rank = $data->rank;
            switch ($rank) {
                case 11:
                    $newRank = 9;
                case 12:
                    $newRank = 10;
            }
            $hashedNewPwd = password_hash($newPwd, PASSWORD_DEFAULT);
            if (!empty($data->picture)) {
                $picExt = explode('.', $data->picture)[1];
                $newPicture = str_replace('/', '', $newId) . '.' . $picExt;
            } else {
                $newPicture = '';
            }


            $sql1 = 'insert into student(std_id,adm_id,password,fname,lname,oname,rank,active,sch_abbr,level,picture,date_of_admission)' .
                'values(?,?,?,?,?,?,?,?,?,?,?,?)';
            $val1 = [$newId,$adm_id, $hashedNewPwd, Utility::escape($data->fname), Utility::escape($data->lname), Utility::escape($data->oname), $newRank,true, $sch_abbr, $data->level, $newPicture,$data->date_of_admission];
            $email =  Utility::escape($data->email);
            $sql2 = 'insert into student2(std_id,fathername,mothername,dob,address,phone,email) values(?,?,?,?,?,?,?)';
            $val2 = [$newId, Utility::escape($data->fathername), Utility::escape($data->mothername), $data->dob, Utility::escape($data->address), Utility::escape($data->phone), $email];
            $sql3 = 'insert into student3(std_id) values(?)';
            $sql4 = 'insert into student_psy(std_id) values(?)';
            //delete data from admission table
            $sql5 = 'delete from admission where id = ?';
            //transfer details to student tables
            $db = DB::get_instance();

            if ($db->trans_query([[$sql1, $val1], [$sql2, $val2], [$sql3, [$newId]], [$sql4, [$newId]], [$sql5, [$id]]])) {

                /*for hikmah only to help use the role and menu functionality*/
                $role_id = $adm->get_role_id($newRank, 0);
                $old_role_id = $adm->get_role_id($rank,0);
                $db->insert(Config::get('users/table_name'), ['user_id' => $newId, 'role_id' => $role_id]);
                Menu::add_available_menus($newId, $role_id); //add menus for real students
                Menu::delete_available_menus($adm_id,$old_role_id); //delete menus as an admission student
              /*for hikmah only to help use the role and menu functionality*/

                if (!empty($newPicture)) {
                    $oldPicDtn = '../uploads/passports/' . $data->picture;
                    $newPicDtn = '../../student/uploads/passports/' . $newPicture;
                    //transfer applicants passport
                    rename($oldPicDtn, $newPicDtn);
                }

                //send notification to APM or IC
                $alert = new Alert(true);
                $message = '<div><p>This is to inform you that ' .
                Utility::formatName($data->fname, $data->oname, $data->lname) .
                    ' has accepted the admission offer to '.School::getLevelName($sch_abbr, $data->level) . ' of ' . School::getFullName($sch_abbr).'</p>';
                $title = 'Acceptance of Admission';
                $rank = $data->rank;
                if ($rank == 11) {
                    $alert->sendToRank(2, $title, $message);
                } else {
                    if ($rank == 12) {
                        $alert->sendToRank(4, $title, $message);
                    }
                }

                //send email to the applicant with his studentid and password in the mail
                $message = '<p>You have accepted our offer!. You are officially a student at ' . School::getFullName($sch_abbr) . '</p><p>Your Student Login details are show below<p>Username: <span>' . $newId . '</span></p><p>'
                    . 'Password: <span>' . $newPwd . '</span></p> <p>It is recommended to change your password after login. <a href="' . $url->to('changepassword.php', 3) . '">Login and change password</a></p><p>Your data has also been transfered to the student portal.<br>This can be accessed at <a href="' . $url->to('index.php', 3) . '">' . $url->to('index.php', 3) . '</a></p>'; //email 
                $mail = new Email();
                $mail->send($email, 'Your Login Details', $message);
                Session::set_flash('acceptAdmission', 'You have successfully accepted our offer. Your Details have now been transfered to another portal. An email containing your login details to that portal has been sent to you');
                echo response(204);
            } else {
                echo response(500, 'An Error prevents you from accepting our admission');
            }

            break;
        case 'decline_admission':
            $db = DB::get_instance();
            $id = Utility::escape(Input::get('id'));
            $adm_id = Utility::escape(Input::get('adm_id'));
            $adm = new Admission();
            $data = $adm->getData($adm_id);
            $db->query('update admission set status = ? where id = ?', [3, $id]); //declines admission
            //send notification to APM or IC
            $alert = new Alert(true);
            $message = '<div><p>This is to inform you that ' .
            Utility::formatName($data->fname, $data->oname, $data->lname) .
                ' has declined the admission offer to ' . School::getLevelName($sch_abbr, $data->level) . ' of ' . School::getFullName($sch_abbr) . '</p>';
            $title = 'Declination of Admission';
            $rank = $data->rank;
            if ($rank == 11) {
                $alert->sendToRank(2, $title, $message);
            } else {
                if ($rank == 12) {
                    $alert->sendToRank(4, $title, $message);
                }
            }
            echo response('204');
            break;
    }
} else {
    echo response(400, 'Invalid request method');
}


function response(int $status, $message = '', array $data = [])
{
    return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'token' => Token::generate()]);
}
