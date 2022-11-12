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
$student = new Student();
if (!$student->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
    Session::setLastPage($url->getCurrentPage());
    Redirect::home('login.php', 0);
}
$data = $student->data();
$id_col = $student->getIdColumn();
$user_col = $student->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $student->getRank();
$stdRank = [9, 10];
if ($data->active != 1) {
    exit('Sorry! you have been made inactive on the portal');
}
if (!in_array($rank, $stdRank)) {
    exit(); // exits the page if the user is not a student
}
Redirect::to($url->to('index.php', 4));
