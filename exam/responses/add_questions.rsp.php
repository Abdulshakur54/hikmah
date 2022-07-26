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
        $examId = Utility::escape(Input::get('examid'));
        $question = Input::getDecoded('qtn');
        $type = Utility::escape(Input::get('type'));

        $options = Input::getDecoded('options');

        $answers = Input::getDecoded('answers');

        $mark = Utility::escape(Input::get('mark'));

        $passage = Input::getDecoded('passage');

        $answerOrder = (Input::get('answerorder')==='false')?false:true;

        if(Utility::noScript($question) && Utility::noScript($options) && Utility::noScript($answers) && Utility::noScript($passage) && Utility::noScript($question)){

            $qtn = new Question();

            $exam = new Exam();

            $examDetails = $exam->getDetails($examId);

            //check that questions are not added more than required

            if($examDetails->no_qtn_added >= $qtn->getCount($examId)){
                if($qtn->add($examId,$question,$type,$options,$answers,$answerOrder,$mark,$passage)){

                    //message indicating question is successfully added

                    echo json_encode(['statuscode'=>1,'token'=>Token::generate()]);

                }

            }else{

                 //message indicating max question is reached

                 echo json_encode(['statuscode'=>2,'token'=>Token::generate()]);

            }

        }

    }