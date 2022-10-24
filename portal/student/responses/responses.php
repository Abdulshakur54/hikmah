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
$std = new Student();
$url = new Url();

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
               
                $email = Utility::escape(Input::get('email'));
                $address = Utility::escape(Input::get('address'));
                $phone = Utility::escape(Input::get('phone'));
                $username = Utility::escape(Input::get('username'));
                $values = [
                    'email' => $email,
                    'address' => $address,
                    'phone' => $phone,
                ];
                if (!empty($_FILES['picture']['name'])) {
                    $file = new File('picture');
                    $pictureName = $username . '.' . $file->extension();
                    $values['picture'] = $pictureName;
                    $db->update('student', $values, "std_id='$username'");
                    $file_path = '../uploads/passports/' . $pictureName;
                    $file->move($file_path);
                } else {
                    $db->update('student', $values, "std_id='$username'");
                }
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
