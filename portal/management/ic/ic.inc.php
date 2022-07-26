<?php
    
    $url = new Url();
    $ic = new Ic();
    if(!$ic->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Redirect::home('login.php',1);
    }
    $data = $ic->data();
    $id_col = $ic->getIdColumn();
    $user_col = $ic->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $ic->getRank(); 
    if($rank!==4){
        exit(); // exits the page if the user is not the director
    }