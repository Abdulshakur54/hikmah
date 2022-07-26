<?php
   
    $url = new Url();
    $alert = new Alert();
    $req2 = new Request2();
    $adm = new Admission();
    if(!$adm->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Session::set_flash('welcome back','');
        Redirect::home('login.php',5);
    }
    $data = $adm->data();
    $id_col = $adm->getIdColumn();
    $user_col = $adm->getUsernameColumn();
    $id = $data->$id_col;
    $username = Utility::escape($data->$user_col);
    $rank = $adm->getRank(); 
    $admRank = [11,12];
    if(!in_array($rank, $admRank)){
        Redirect::to($url->to('login.php'), 5);
    }
    Session::setLastPage($url->getCurrentPage()); //set as last page
    $sch_abbr = Utility::escape($data->sch_abbr);
    $level = $data->level;
