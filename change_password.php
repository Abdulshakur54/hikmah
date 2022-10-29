<?php
spl_autoload_register(function ($class) {
    require_once 'classes/' . $class . '.php';
});
$db = DB::get_instance();

// $usernames = ['HCK/20/1/012', 'HCK/20/1/013', 'HCB/20/3/001', 'HCB/20/3/002','HCB/20/3/003','HCB/20/1/004','HCB/20/2005', 'HCB/20/2/006'];
$usernames= ['M004','M005', 'M006', 'M007', 'M008', 'M009', 'M010', 'M011','M012','M013','M014'];
$password = 111111;
foreach($usernames as $username){
    if ($db->update('management', ['password' => password_hash($password, PASSWORD_DEFAULT)], "mgt_id='$username'")) {
        echo 'successfully changed password';
    } else {
        echo 'password change not successful';
    }
}
