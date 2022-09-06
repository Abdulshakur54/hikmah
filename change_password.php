<?php
spl_autoload_register(function ($class) {
    require_once 'classes/' . $class . '.php';
});
$db = DB::get_instance();

// $usernames = ['HCK/20/1/012', 'HCK/20/1/013', 'HCB/20/3/001', 'HCB/20/3/002','HCB/20/3/003','HCB/20/1/004','HCB/20/2005', 'HCB/20/2/006'];
$usernames= ['S001','S002', 'S003', 'S004', 'S005', 'S006', 'S007', 'S008'];
$password = 111111;
foreach($usernames as $username){
    if ($db->update('staff', ['password' => password_hash($password, PASSWORD_DEFAULT)], "staff_id='$username'")) {
        echo 'successfully changed password';
    } else {
        echo 'password change not successful';
    }
}
