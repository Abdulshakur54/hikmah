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
                        echo response(204,'Update was successful');
                    } else {
                        echo response(500, 'An error is preventing changes to being saved');
                    }
                } else {
                    if ($apm->updateSchedule($sch_abbr, $term, $formFee, $regFee, $ftsf, $stsf, $ttsf)) {
                        echo response(204, 'Update was successful2');
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
    }
} else {
    echo response(400, 'Invalid request method');
}


function response(int $status, $message = '', array $data = [])
{
    return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'token' => Token::generate()]);
}
