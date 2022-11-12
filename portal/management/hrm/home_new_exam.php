<?php
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
//end of initializatons

$url = new Url();
$alert = new Alert();
$req = new Request();
$hrm = new Hrm();
if (!$hrm->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
    Session::setLastPage($url->getCurrentPage());
    Redirect::home('login.php', 0);
}
$data = $hrm->data();
$id_col = $hrm->getIdColumn();
$user_col = $hrm->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $hrm->getRank();
if ($data->active != 1) {
    exit('Sorry! you have been made inactive on the portal');
}
if ($rank !== 6) {
    exit(); // exits the page if the user is not the hrm
}
Redirect::to($url->to('index.php', 4));
