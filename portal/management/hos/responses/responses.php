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
                    $scoreTable = $utils->getFormatedSession($sch_abbr) . '_score'; //the current score table

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
    }
} else {
    echo response(400, 'Invalid request method');
}


function response(int $status, $message = '', array $data = [])
{
    return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'token' => Token::generate()]);
}
