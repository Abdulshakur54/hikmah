<?php
	 //initializations
        spl_autoload_register(
                function($class){
                        require_once'../../classes/'.$class.'.php';
                }
        );
        session_start(Config::get('session/options'));
        //end of initializatons
        require_once './nav1.inc.php';
	if($adm->logout()){
		Redirect::to('login.php');
	}
	
?>