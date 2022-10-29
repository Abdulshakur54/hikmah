<?php

//initializations
spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializatons
$session_user = Session::get('user');
$first_letter = strtolower(substr($session_user, 0, 1));
if (is_numeric($first_letter)) {
    $userObj = new Admission();
    $user_type  = 'admission';
} else {

    switch ($first_letter) {
        case 'h':
            $userObj = new Student();
            $user_type = 'student';
            break;
        case 's':
            $userObj = new Staff();
            $user_type = 'staff';
            break;
        case 'm':
            $userObj = new Management();
            $user_type = 'management';
            break;
        default:
            $userObj = new User();
            $user_type = 'user';
    }
}
Session::delete('user');
$userObj->logout();
Session::set('user_type',$user_type);
Redirect::to('login.php');
