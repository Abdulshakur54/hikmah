<?php
//initializations

spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
if(Input::submitted('get') && !empty(Input::get('username'))){
    $username = Input::get('username');
    echo $username.' \'s profile';
}
?>