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
$apm = new Apm();
if (!$apm->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
    Session::setLastPage($url->getCurrentPage());
    Redirect::home('login.php', 0);
}
$data = $apm->data();
$id_col = $apm->getIdColumn();
$user_col = $apm->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $apm->getRank();
if ($data->active != 1) {
    exit('Sorry! you have been made inactive on the portal');
}
if ($rank !== 2) {
    exit(); // exits the page if the user is not the Apm
}
Redirect::to($url->to('index.php', 4));
