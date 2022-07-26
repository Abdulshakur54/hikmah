<?php
    
    $url = new Url();
    $alert = new Alert();
    $req = new Request();
    $hrm = new Hrm();
    if(!$hrm->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Redirect::home('login.php',1);
    }
    $data = $hrm->data();
    $id_col = $hrm->getIdColumn();
    $user_col = $hrm->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $hrm->getRank(); 
    if($rank!==6){
        exit(); // exits the page if the user is not the director
    }