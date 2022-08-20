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
    Session::set_flash('welcome back', '');
    Redirect::home('login.php', 1);
}
$data = $apm->data();
$id_col = $apm->getIdColumn();
$user_col = $apm->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $apm->getRank();
if ($rank !== 2) {
    exit(); // exits the page if the user is not the Apm
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <main>
            <?php
                $transfer = ['tableName'=>'admission','tableColumn'=>'score','maxScore'=>100,'idColumn'=>'adm_id'];
                Redirect::to($url->to('new_exam.php?transfer='. json_encode($transfer),4));
            ?>
            
        </main>
    </body>
</html>
