<?php
	//initializations
	spl_autoload_register(
		function($class){
			require_once'../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializations
        require_once './nav1.inc.php';
	$std = new Student();
	if($std->logout()){
		Redirect::to('login.php');
	}
	
?>