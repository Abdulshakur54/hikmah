<?php
   
    $url = new Url();
    $alert = new Alert();
    $req = new Request();
    $acct = new Accountant();
    if(!$acct->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Redirect::home('login.php',1);
    }
    $data = $acct->data();
    $id_col = $acct->getIdColumn();
    $user_col = $acct->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $acct->getRank(); 
    if($rank!==3){
        exit(); // exits the page if the user is not the director
    }