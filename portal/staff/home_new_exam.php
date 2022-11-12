<?php 
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
//end of initializatons

$url = new Url();
$staff = new Staff();
if (!$staff->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
    Session::setLastPage($url->getCurrentPage());
    Redirect::home('login.php', 0);
}
$data = $staff->data();
$id_col = $staff->getIdColumn();
$user_col = $staff->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $staff->getRank();
$allowedRank = [7, 8, 15, 16];
if ($data->active != 1) {
    exit('Sorry! you have been made inactive on the portal');
}
if (!in_array($rank, $allowedRank)) {
    exit(); // exits the page if the user is not a H.O.S
}
Redirect::to($url->to('index.php', 4));
