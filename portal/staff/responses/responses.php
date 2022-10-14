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
$staff = new Staff();
$url = new Url();
if (Input::submitted()) {
    $op = Input::get('op');
    $sch_abbr = Input::get('school');
    switch ($op) {
        case 'update_schedules':

            $classWithId = $staff->getClassWithId(Input::get('username'));
            $class = $classWithId->class;
            $classId = $classWithId->id;
            $level = $classWithId->level;
            $genMsg = '';
            if (Input::submitted() && Token::check(Input::get('token'))) {
                $punc = Utility::escape(Input::get('punc'));
                $hon = Utility::escape(Input::get('hon'));
                $dhw = Utility::escape(Input::get('dhw'));
                $rap = Utility::escape(Input::get('rap'));
                $sot = Utility::escape(Input::get('sot'));
                $rwp = Utility::escape(Input::get('rwp'));
                $ls = Utility::escape(Input::get('ls'));
                $atw = Utility::escape(Input::get('atw'));
                $ho = Utility::escape(Input::get('ho'));
                $car = Utility::escape(Input::get('car'));
                $con = Utility::escape(Input::get('con'));
                $wi = Utility::escape(Input::get('wi'));
                $ob = Utility::escape(Input::get('ob'));
                $hea = Utility::escape(Input::get('hea'));
                $vs = Utility::escape(Input::get('vs'));
                $pig = Utility::escape(Input::get('pig'));
                $pis = Utility::escape(Input::get('pis'));
                $ac = Utility::escape(Input::get('ac'));
                $pama = Utility::escape(Input::get('pama'));
                $ms = Utility::escape(Input::get('ms'));

                $a1 = Utility::escape(Input::get('a1'));
                $b2 = Utility::escape(Input::get('b2'));
                $b3 = Utility::escape(Input::get('b3'));
                $c4 = Utility::escape(Input::get('c4'));
                $c5 = Utility::escape(Input::get('c5'));
                $c6 = Utility::escape(Input::get('c6'));
                $d7 = Utility::escape(Input::get('d7'));
                $e8 = Utility::escape(Input::get('e8'));
                $f9 = Utility::escape(Input::get('f9'));

                $height_beg = Utility::escape(Input::get('height_beg'));
                $height_end = Utility::escape(Input::get('height_end'));
                $weight_beg = Utility::escape(Input::get('weight_beg'));
                $weight_end = Utility::escape(Input::get('weight_end'));

                if (!empty($_FILES['signature']['name'])) {
                    $file = new File('signature');
                    $ext = $file->extension();
                    $signatureName = $sch_abbr . '_' . $level . $class . '.' . $ext;
                    //update schedule
                    if ($staff->updateSchedule($classId, $punc, $hon, $dhw, $rap, $sot, $rwp, $ls, $atw, $ho, $car, $con, $wi, $ob, $hea, $vs, $pig, $pis, $ac, $pama, $ms, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $height_beg, $height_end, $weight_beg, $weight_end, $signatureName)) {
                        $file->move('../uploads/signatures/' . $signatureName); //move picture to the destination folder

                        $genMsg = 'Changes has been successfully updated';
                        Session::set_flash('post_method_success_message', $genMsg);
                        echo response(201);
                    } else {
                        $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                        echo response(400, $genMsg);
                    }
                } else {
                    //update schedule
                    if ($staff->updateSchedule($classId, $punc, $hon, $dhw, $rap, $sot, $rwp, $ls, $atw, $ho, $car, $con, $wi, $ob, $hea, $vs, $pig, $pis, $ac, $pama, $ms, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $height_beg, $height_end, $weight_beg, $weight_end)) {

                        $genMsg = 'Changes has been successfully updated';
                        Session::set_flash('post_method_success_message', $genMsg);
                        echo response(201);
                    } else {
                        $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                        echo response(400, $genMsg);
                    }
                }
            }
            break;
        case 'edit_scheme':
            $title = Utility::escape(Input::get('title'));
            $scheme = trim(Utility::escape(Input::get('scheme')));
            $scheme_id = Utility::escape(Input::get('scheme_id'));
            $scheme_order = Utility::escape(Input::get('order'));
            $rules = [
                'title' => ['name' => 'Title', 'required' => true],
                'scheme' => ['name' => 'Scheme', 'required' => true],
                'order' => ['name' => 'Order', 'required' => true],
            ];
            $val = new Validation();
            if (!$val->check($rules)) {
                echo response(406, implode('<br />', $val->errors()));
                exit();
            }

            Subject::edit_scheme($scheme_id, $title, $scheme, $scheme_order);
            echo response(201, 'Changes have been saved');
            break;

        case 'add_scheme':
            $term = Utility::escape(Input::get('term'));
            $title = Utility::escape(Input::get('title'));
            $scheme = trim(Utility::escape(Input::get('scheme')));
            $scheme_id = Utility::escape(Input::get('scheme_id'));
            $scheme_order = Utility::escape(Input::get('order'));
            $sub_id = Utility::escape(Input::get('subid'));
            $rules = [
                'term' => ['name' => 'Term', 'required' => true],
                'title' => ['name' => 'Title', 'required' => true],
                'scheme' => ['name' => 'Scheme', 'required' => true],
                'order' => ['name' => 'Order', 'required' => true],
            ];
            $val = new Validation();
            if (!$val->check($rules)) {
                echo response(406, implode('<br />', $val->errors()));
                exit();
            }

            Subject::add_scheme($sub_id, $title, $scheme, $term, $scheme_order);
            echo response(201, 'Scheme has been added');
            break;
        case 'delete_scheme':
            $scheme_id = Utility::escape(Input::get('scheme_id'));
            Subject::delete_scheme($scheme_id);
            echo response(204, 'menu was successfully deleted');
            break;
        case 'update_student_psy':
            $current_term = Utility::escape(Input::get('current_term'));
            $std_id = Utility::escape(Input::get('std_id'));
            $values = [];
            for ($i = 1; $i <= 20; $i++) {
                $values[$current_term . '_psy' . $i] = Utility::escape(Input::get('psy' . $i));
            }
            $values[$current_term . '_height_beg'] = Utility::escape(Input::get('height_beg'));
            $values[$current_term . '_height_end'] = Utility::escape(Input::get('height_end'));
            $values[$current_term . '_weight_beg'] = Utility::escape(Input::get('weight_beg'));
            $values[$current_term . '_weight_end'] = Utility::escape(Input::get('weight_end'));
            $values[$current_term . '_com'] = Utility::escape(Input::get('comment'));

            $staff->updateStdPsy($std_id, $values);
            $genMsg = 'Changes has been successfully updated';
            Session::set_flash('post_method_success_message', $genMsg);
            echo response(201);
            break;
        case 'update_comment':
            $updated_data = json_decode(Input::get('updated_data'), true);
            $term = Utility::escape(Input::get('term'));
            $start = false;
            foreach ($updated_data as $upd_data) {
                if ($start) {
                    $db->requery([Utility::escape($upd_data[1]), Utility::escape($upd_data[0])]);
                } else {
                    $db->query('update student_psy set ' . $term . '_com =? where std_id = ?', [Utility::escape($upd_data[1]), Utility::escape($upd_data[0])]);
                    $start = true;
                }
            }
            echo response(201, 'Changes have been saved');
            break;
        case 'view_results':
            $classId = Utility::escape(Input::get('class_id'));
            $school = Utility::escape(Input::get('school'));
            $students = Result::get_ids($classId, $school);
            echo response(200, '', $students);
            break;
    }
} else {
    echo response(400, 'Invalid request method');
}


function response(int $status, $message = '', array $data = [])
{
    return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'token' => Token::generate()]);
}
