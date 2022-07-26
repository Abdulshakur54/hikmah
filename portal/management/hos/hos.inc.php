<?php
    
    $url = new Url();
    $alert = new Alert();
    $req = new Request();
    $hos = new Hos();
    if(!$hos->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Session::set_flash('welcome back','');
        Redirect::home('login.php',1);
    }
    $data = $hos->data();
    $id_col = $hos->getIdColumn();
    $user_col = $hos->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $hos->getRank(); 
    
    if($rank!==5 && $rank!==17){
        exit(); // exits the page if the user is not a H.O.S
    }
    $sch_abbr = Utility::escape($data->sch_abbr);
    //checks if students needed to be assigned a class
    $noStdNeedClass = $hos->getNoStudentsNeedsClass($sch_abbr);
    if($noStdNeedClass > 0 && basename(Utility::myself()) !== 'assign_student.php'){ //checks if some students are not assigned classes and if it is not the assign class page
        echo '<p>'.$noStdNeedClass.' students needs to be assigned a class. <a href="assign_student.php">Assign Here</a></p>';
    }
    if(!$hos->isStdsSubRegComplete($sch_abbr) && basename(Utility::myself()) !== 'need_to_reg_sub.php'){
         echo '<p>Some students are yet to complete their Subject Registeration. <a href="need_to_reg_sub.php">View Here</a></p>';
    }
    $utils = new Utils();
    $currTerm = $utils->getCurrentTerm($sch_abbr);
    $currSession = $utils->getSession($sch_abbr);
?>