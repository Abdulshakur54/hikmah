<?php
    //initializations
      spl_autoload_register(
              function($class){
                      require_once'../../../classes/'.$class.'.php';
              }
      );
      session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
//end of initializatons

//copied from staff.inc.php

$url = new Url();

$staff = new Staff();
if (!$staff->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
   exit();
}
$data = $staff->data();
$id_col = $staff->getIdColumn();
$user_col = $staff->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $staff->getRank();
$allowedRank = [7, 8, 15, 16];
if (!in_array($rank, $allowedRank)) {
    exit(); // exits the page if the user is not a H.O.S
}
$sch_abbr = Utility::escape($data->sch_abbr);
$utils = new Utils();
$currTerm = $utils->getCurrentTerm($sch_abbr);
$currSession = $utils->getSession($sch_abbr);

//end of copied from staff.inc.php


    require_once '../includes/sub_teacher.inc.php';
    
    header("Content-Type: application/json; charset=UTF-8");
    if(Input::submitted()){
        $msg = '';
        $errors = []; //to hold errors
        $utils = new Utils();
        $table = $utils->getFormatedSession($sch_abbr).'_score';
        $scoreSettings = Subject::getScoreSettings($sch_abbr);
        $subject = new Subject($subId,$table,$scoreSettings);
        $columns = $subject->getNeededColumns($sch_abbr); //this returns an array  of the needed columns
        
        $data = json_decode(Input::get('updatedData'),true); //make the json data gotten associative arrays for easy access
        $hasProject =  Input::get('hasproject');
        if(Token::check(Input::get('token'))){
            $scrRows =  $data['updatedData']; //$scrRow is an array of indexed arrays whose first index has the score_id and second index is an indexed array of the corresponding scores
            //update score table
            $rowCount = 0;
            foreach($scrRows as $scrRow){
                $rowCount++;
                $scores = $scrRow[1];
                foreach ($scores as $scoreColumn=>$score){
                    if($score != null){
                        if(!preg_match('/^[1-9]{1,2}0{0,2}(\.[0-9]{1,2})?$/', $score)){
                            $errors[] ='Row '.$rowCount.': Enter a valid number for '.strtoupper($columns[$scoreColumn]);
                        }else{
                            if($score > $scoreSettings[$columns[$scoreColumn]]){
                                $errors[] = 'Row '.$rowCount.': Maximum value allowed for '.strtoupper($columns[$scoreColumn]).' is '.$scoreSettings[$columns[$scoreColumn]];
                            }
                        }
                    }
                    
                }
            }
            
            if(empty($errors)){
                foreach($scrRows as $scrRow){
                    $subject->update($scrRow[0],$scrRow[1],$currTerm);
                }
                 echo json_encode(['statuscode'=>1,'token'=>Token::generate()]);
            }else{
                 echo json_encode(['statuscode'=>0,'token'=>Token::generate(),'errors'=>$errors]);
            }
           
        }
        
    }
    
  
