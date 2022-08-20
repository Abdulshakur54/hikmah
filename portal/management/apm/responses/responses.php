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
$apm = new Apm();
$url = new Url();

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
