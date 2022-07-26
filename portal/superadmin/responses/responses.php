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
if (Input::submitted() && Token::check(Input::get('token'))) {
    $op = Input::get('op');
    switch($op){
        case 'edit_role':
            $role = Utility::escape(Input::get('role'));
            $role_id = Utility::escape(Input::get('role_id'));
            
            if(Menu::edit_role($role, $role_id)){
                echo response(204, 'Changes were saved successfully');
            }else{
                echo response(500, 'Something went wrong');
            }
        break;
        case 'add_role':
            $role = Utility::escape(Input::get('role'));
            if (Menu::add_role($role)) {
                echo response(204, 'Role was successfully inserted');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
        case 'delete_role':
            $role_id = Utility::escape(Input::get('role_id'));

            if (Menu::delete_role($role_id)) {
                echo response(204, 'role was successfully deleted');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
    }
    
}


function response(int $status, $message = '', $data = [])
{
    return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'token' => Token::generate()]);
}
?>