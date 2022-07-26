<?php
    
	$mgt = new Management();
	$url = new Url();
        $alert = new Alert();
        $req = new Request();
    if(!$mgt->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Redirect::home('login.php',1);
    }
	$data = $mgt->data();
    $id_col = $mgt->getIdColumn();
    $user_col = $mgt->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $mgt->getRank();