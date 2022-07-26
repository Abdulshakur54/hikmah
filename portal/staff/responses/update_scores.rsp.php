<?php
    //initializations
      spl_autoload_register(
              function($class){
                      require_once'../../../classes/'.$class.'.php';
              }
      );
      session_start(Config::get('session/options'));
      //end of initializatons
      
    require_once '../nav1.inc.php';
    require_once '../sub_teacher.inc.php';
    
    header("Content-Type: application/json; charset=UTF-8");
    if(Input::submitted()){
        $msg = '';
        $errors = []; //to hold errors
        $utils = new Utils();
        $table = $utils->getFormatedSession($sch_abbr).'_score';
        $scoreSettings = $staff->getScoreSettings($sch_abbr);
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
    
  
