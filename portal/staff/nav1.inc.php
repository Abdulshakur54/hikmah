<?php
   
    $url = new Url();
    $alert = new Alert();
    $req2 = new Request2();
    $staff = new Staff();
    if(!$staff->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Session::set_flash('welcome back','');
        Redirect::home('login.php',2);
    }
    $data = $staff->data();
    $id_col = $staff->getIdColumn();
    $user_col = $staff->getUsernameColumn();
    $id = $data->$id_col;
    $username = Utility::escape($data->$user_col);
    $rank = $staff->getRank(); 
    $allowedRank = [7,8,15,16];
    if(!in_array($rank, $allowedRank)){
        exit(); // exits the page if the user is not a H.O.S
    }
    $sch_abbr = Utility::escape($data->sch_abbr);
    $utils = new Utils();
    $currTerm = $utils->getCurrentTerm($sch_abbr);
    $currSession = $utils->getSession($sch_abbr);
?>
