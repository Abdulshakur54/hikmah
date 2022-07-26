<?php
    
    $url = new Url();
    $alert = new Alert();
    $req = new Request();
    $dir = new Director();
    if(!$dir->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Session::set_flash('welcome back','');
        Redirect::home('login.php',1);
    }
    $data = $dir->data();
    $id_col = $dir->getIdColumn();
    $user_col = $dir->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $dir->getRank(); 
    if($rank!==1){
        exit(); // exits the page if the user is not the director
    }