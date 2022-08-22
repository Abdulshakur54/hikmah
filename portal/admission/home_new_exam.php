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
$alert = new Alert();
$req = new Request();
$adm = new Admission();
if (!$adm->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
    Session::setLastPage($url->getCurrentPage());
    Session::set_flash('welcome back', '');
    Redirect::home('login.php', 1);
}
$data = $adm->data();
$id_col = $adm->getIdColumn();
$user_col = $adm->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $adm->getRank();
$admRank = [11, 12];
if (!in_array($rank, $admRank)) {
    exit();
}

Redirect::to($url->to('index.php', 4));