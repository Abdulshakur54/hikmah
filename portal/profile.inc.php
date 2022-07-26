<?php 

    $mgt = new Management();
    $staff = new Staff();
    $std = new Student();
    $adm = new Admission();
    $url = new Url();
    if($mgt->isRemembered()){
        if(!include '../management/nav1.inc.php'){ //check if page to be accessed is for management member
            include './nav1.inc.php';
        }
        $cat = 1;
    }else if($staff->isRemembered()){
        if(@!include '../../staff/nav1.inc.php'){ //check if page to be accessed is for management member
            if(@!include '../staff/nav1.inc.php'){
                @include'./nav1.inc.php';
            }
        }
         $cat = 2;
    }else if($std->isRemembered()){
        if(@!include '../../student/nav1.inc.php'){ //check if page to be accessed is for management member
            if(@!include '../student/nav1.inc.php'){
                @include'./nav1.inc.php';
            }
        }
        $cat = 3;
    }
    else if($adm->isRemembered()){
        if(@!include '../../admission/nav1.inc.php'){ //check if page to be accessed is for management member
            if(@!include '../admission/nav1.inc.php'){
                @include'./nav1.inc.php';
            }
        }
        $cat = 5;
    }else{
        $cat = 100; // for a visitor that do not have account
    }
    
    

