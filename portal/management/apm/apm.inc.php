<?php
    
    $url = new Url();
    $alert = new Alert();
    $req = new Request();
    $apm = new Apm();
    if(!$apm->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Session::set_flash('welcome back','');
        Redirect::home('login.php',1);
    }
    $data = $apm->data();
    $id_col = $apm->getIdColumn();
    $user_col = $apm->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $apm->getRank(); 
    if($rank!==2){
        exit(); // exits the page if the user is not the H.R.M
    }
