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
    $std = new Student();
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
        $examId = Utility::escape(Input::get('examid'));
        $question = trim(Input::get('qtn'));
        $type = Utility::escape(Input::get('type'));
        $options = Input::get('options');
        $answers = trim(Input::get('answers'));
        $mark = Utility::escape(Input::get('mark'));
        $passage = Input::get('passage');
        $answerOrder = (Input::get('answerorder')==='false')?false:true;
        $qtnId = Utility::escape(Input::get('qtnid'));
        if(Utility::noScript($question) && Utility::noScript($options) && Utility::noScript($answers) && Utility::noScript($passage) && Utility::noScript($question)){
            $qtn = new Question();
            $exam = new Exam();
            $examDetails = $exam->getDetails($examId);
            //check that questions are not added more than required
            if($qtn->update($examId,$question,$type,$options,$answers,$answerOrder,$mark,$passage,$qtnId)){
                Session::set_flash('updatequestion','<div class="success">Question successfully updated</div>'); //sets a flash message for the view_questions.php page
                //message indicating question is successfully added
                echo json_encode(['statuscode'=>1,'token'=>Token::generate()]);
            }
        }
    }