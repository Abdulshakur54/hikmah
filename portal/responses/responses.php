<?php
//initializations

spl_autoload_register(
    function ($class) {
        require_once '../../classes/' . $class . '.php';
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
    switch ($op) {
        case 'get_lga_list':
            $state_id = Utility::escape(Input::get('state_id'));
            $lgas = Utility::getLgas($state_id);
            echo response(200, '', $lgas);
            break;
        case 'set_checked':
            $menu_id = Input::get('menu_id');
            $checked = (int)Input::get('checked');
            $db = DB::get_instance();
            if ($db->update('users_menu', ['shown' => $checked], "id = $menu_id")) {
                echo response(204, '');
            } else {
                echo response(500, 'Something went wrong');
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
