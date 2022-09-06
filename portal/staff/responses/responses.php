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
$staff= new Staff();
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
                        //update psycometry for students
                        $stdIds = $staff->getStudentsIds($classId);
                        if (!empty($stdIds)) {
                            $stdIdsString = "'" . implode("','", $stdIds) . "'";
                            $staff->populateStdPsy($classId, $stdIdsString); //update psycometry
                        }
                        $genMsg = '<div class="success">Changes has been successfully updated</div>';
                        echo response(201, $genMsg);
                    } else {
                        $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                        echo response(400, $genMsg);
                    }

                } else {
                    //update schedule
                    if ($staff->updateSchedule($classId, $punc, $hon, $dhw, $rap, $sot, $rwp, $ls, $atw, $ho, $car, $con, $wi, $ob, $hea, $vs, $pig, $pis, $ac, $pama, $ms, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $height_beg, $height_end, $weight_beg, $weight_end)) {
                        //update psycometry for students
                        $stdIds = $staff->getStudentsIds($classId);
                        if (!empty($stdIds)) {
                            $stdIdsString = "'" . implode("','", $stdIds) . "'";
                            $staff->populateStdPsy($classId, $stdIdsString); //update psycometry
                        }
                        $genMsg = '<div class="success">Changes has been successfully updated</div>';
                        echo response(201, $genMsg);
                    } else {
                        $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                        echo response(400, $genMsg);
                    }
                }
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
