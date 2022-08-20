<?php

//initializations
spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializatons

$user_type = Session::get('user_type');
switch ($user_type) {
    case 'student':
        $user = new Student();
        $id_col = Config::get('users/username_column2');
        break;
    case 'staff':
        $user = new Staff();
    case 'management':
        $user = new Management();
        break;
    case 'admission':
        $user = new Admission();
        break;
}
$user_type = Session::get('user_type');
Session::delete('user');
$user->logout();
Session::set('user_type',$user_type);
Redirect::to('login.php');
?>