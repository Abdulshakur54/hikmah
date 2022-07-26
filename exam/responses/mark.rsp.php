<?php
    //initializations
	spl_autoload_register(
		function($class){
			require_once'../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
    //end of initializatons
    $mgt = new Management();
    $staff = new Staff();
    $user = null;
    $message='';
    if($mgt->isRemembered()){
        $user = $mgt;
    }
    if($staff->isRemembered()){
        $user = $staff;
    }
    
    if(!isset($user)){ //ensure user is legally logged in
        Redirect::to('index.php'); //redirect to exam home page
    } 

    header("Content-Type: application/json; charset=UTF-8");
    if(Input::submitted()&& Token::check(Input::get('token'))){
        
        $data = json_decode(Input::get('data'),true);
        $action = Utility::escape(Input::get('action'));
        $examId = Utility::escape(Input::get('examid'));
        $examId = strtoupper($examId);
        $qId = Input::get('qtnid');
        $maxMark = Input::get('maxmark');
       
        $msg='';
        foreach ($data as $examineeId=>$dat){
            if(!Utility::noScript($dat[0])){
                $msg.='invalid comment for '.$examineeId.'<br>';
            }
            if($dat[1] === '' && $action === 'submit'){
                 $msg.='mark not entered for '.$examineeId.'<br>';
            }
            
            if(!preg_match('/^[0-9]{1,3}$/', $dat[1]) && !$dat[1] === ''){
                $msg.='invalid mark for '.$examineeId.'<br>';
            }
                
                
            if($dat[1] > $maxMark){
                $msg.='exceeded max value for mark at  '.$examineeId.'<br>';
            }
            
        }
        
        if(empty($msg)){ //save the data by updating the database
            $exam = new Exam();
             if($action ==='submit'){
                 $passMark = Utility::escape(Input::get('passmark'));
                 foreach ($data as $examineeId=>$dat){
                     $exam->updateTheoryQtn($examId,$examineeId,$qId,$dat[1],$dat[0],true);
                      if($exam->noMoreTheoryQtns($examId,$examineeId)){
                          //update the answer in the ex_completed table
                           $exam->updateCompletedExam($examId,$examineeId,$passMark);
                           $alert = new ExamAlert();
                           $alert->send($examineeId, $examId.' Result', '<p style="font-size:14px">Hi, your result for '.$examId.' is ready</p><p>You can view it <a href="result.php?examid='.$examId.'&examineeid='.$examineeId.'">here</a></p>');
                           //notifies the examinee that his result is ready
                           //email the examinee that his result is ready
                           $url = new Url();
                           $examPortal = $url->to('index.php',4);
                           $emailMessage = '<p style="font-size:14px">Hi, your result for '.$examId.' is ready</p><p>Login <a href="'.$examPortal.'">here</a> to the school portal, then navigate to view result</p>';
                       } 
                 }
                 echo json_encode(['success'=>true,'token'=>Token::generate(),'qtnid'=>$qId]); //message indicating that it was  successful
    
               
            }else{
                if($action === 'save'){
                    foreach ($data as $examineeId=>$dat){
                        $exam->updateTheoryQtn($examId,$examineeId,$qId,$dat[1],$dat[0]);
                    }
                    echo json_encode(['success'=>true,'token'=>Token::generate(),'qtnid'=>$qId]); //message indicating that it was successful
                }
            }
           
        }else{
            echo json_encode(['success'=>false,'message'=>$msg,'token'=>Token::generate(),'qtnid'=>$qId]); //message indicating that it was not successful
        } 
                    
        
    }