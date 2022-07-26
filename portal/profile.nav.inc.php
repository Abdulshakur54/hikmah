<?php
 
    switch($cat){
        case 1:
            if(@!include '../management/nav.inc.php'){ //check if page to be accessed is for management member
                @include './nav.inc.php';
            }
            break;
        case 2:
            if(@!include '../../staff/nav.inc.php'){ //check if page to be accessed is for management member
                @include '../staff/nav.inc.php';
            }
            break;
        case 3:
            if(@!include '../../student/nav.inc.php'){ //check if page to be accessed is for management member
                @include '../student/nav.inc.php';
            }
            break;
        case 5:
            if(@!include '../../admission/nav.inc.php'){ //check if page to be accessed is for management member
                @include '../admission/nav.inc.php';
            }
            break;
        case 100:
            if(@!include '../../../nav.inc.php'){
                @include '../../nav.inc.php'; //this should provide a navigation link if a non login user visits any of the management page
            }  
    }
