<?php
	//initializations
	spl_autoload_register(
		function($class){
			require_once'../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializatons
	$mgt = new Management();
	$url = new Url();
        /* the sole function of this page is redirecting users*/
	if($mgt->isRemembered()){
		switch($mgt->getRank()){
			case 1:
				Redirect::to('director/index.php');
			break;
			case 2:
				Redirect::to('apm/index.php');
			break;
			case 3:
				Redirect::to('accountant/index.php');
			break;
			case 4:
				Redirect::to('ic/index.php');
			break;
			case 5:
                        case 17:
				Redirect::to('hos/index.php');
			break;
			case 6:
				Redirect::to('hrm/index.php');
			break;
		}
	}
	Redirect::to('login.php');
?>