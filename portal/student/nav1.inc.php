<?php
        $std = new Student();
        $url = new Url();
        if(!$std->isRemembered()){
            Session::setLastPage($url->getCurrentPage());
            Redirect::home('login.php',3);
        }

        $data = $std->data();
        $id_col = $std->getIdColumn();
        $user_col = $std->getUsernameColumn();
        $id = (int)$data->$id_col;
        $username = Utility::escape($data->$user_col);
        $rank = $std->getRank(); 
        $stdRank = [9,10];
        if(!in_array($rank,$stdRank)){
            exit(); // exits the page if the user is not a student
        }

        $sch_abbr = Utility::escape($data->sch_abbr); 
        $classId = (int)$data->class_id;
        
        $utils = new Utils();
        $currTerm = $utils->getCurrentTerm($sch_abbr);  //get current terrm
        $currSession = $utils->getSession($sch_abbr);
        $alert = new Alert();
        $req2 = new Request2();