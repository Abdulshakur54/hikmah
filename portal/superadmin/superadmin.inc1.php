<?php

//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
    //end of initializatons