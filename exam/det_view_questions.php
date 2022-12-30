<?php

	//initializations

	spl_autoload_register(

		function($class){

			require_once'../classes/'.$class.'.php';

		}

	);

	session_start(Config::get('session/options'));

	//end of initializatons



	$url = new Url();

    $allowed = [2,4,5,7,15,17]; //stores the rank of people allowed

    $mgt = new Management();

    $staff = new Staff();

    $user = null;

    if($mgt->isRemembered()){

        $user = $mgt;

    }

    if($staff->isRemembered()){

        $user = $staff;

    }

	if(!isset($user)){ //ensure user is legally logged in

        Redirect::to('index.php'); //redirect to exam home page

	} 

    $rank = $user->getRank();

    if(!in_array($rank, $allowed)){ //ensure that only the allowed people can access page

		Redirect::to(404);

	}  



    //this function helps to return 1,2,3 or 4 when A,B,C or D is passed

    function equivalentAnswer($ans){

        $ans = strtoupper($ans);

        switch($ans){

            case 'A':

                return 1;

            case 'B':

                return 2;

            case 'C':

                return 3;

            case 'D':

                return 4;

        }

    }



    function equivalentLetter($num){

        switch($num){

            case 1:

                return 'A';

            case 2:

                return 'B';

            case 3:

                return 'C';

            case 4:

                return 'D';

        }

    }





    $qtn = new Question();

    if(Input::submitted('get') && !empty(Input::get('examid'))){//runs if page is entered via get request with params, this would be true for delete and publish exam functionality

        

    }else{

        Redirect::to('index.php'); //redirect to exam index

    }?>

<!DOCTYPE html>

<html lang="en">

    <head>

        <title>View Questions</title>

        <meta name="HandheldFriendly" content="True">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/det_view_questions.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php'?>

        <main>

            <?php require_once 'header.inc.php'?>

        <?php

           $examId = Utility::escape(Input::get('examid'));

           $qtns = $qtn->getQuestions($examId);

           if(!empty($qtns)){

               echo '<h2 class="sectionhead">'.strtoupper($examId).' Questions and Answers</h2>';

               $x=1; //holds the count for the questions

               $qtnCount = $qtn->getCount($examId);

               echo '<div id="qtnContainer">';

               foreach($qtns as $val){

                  echo '<div id="wrapper">';

                  echo'<div class="questionNo">Q '.$x.'/'.$qtnCount.'</div>'; //output the question no

                  echo '<div class="questionDiv">'.$val->qtn.'</div>'; //output the question

                  $output='<div class="options">';

                  switch($val->type){

                       case 1:

                           $equivAns = equivalentAnswer($val->answers);

                           $options = json_decode($val->options,true);

                           for($i=1;$i<=4;$i++){

                               if($equivAns === $i){

                                   $output.='<input type="radio" checked />';

                               }else{

                                   $output.='<input type="radio" disabled />';

                               }

                               $output .= equivalentLetter($i).'.&nbsp;&nbsp;'.$options['opt'.$i].'<br>';

                           }

                           break;

                       case 2:

                           if($val->answers === 'true'){

                               $output.='<input type="radio" checked /> True &nbsp; <input type="radio" disabled /> False';

                           }else{

                               $output.='<input type="radio" disabled /> True &nbsp; <input type="radio" checked/> False';

                           }

                           break;

                       case 3:

                           $answers = json_decode($val->answers,true);

                           $ansLen = count($answers);

                           for($i=1;$i<=$ansLen;$i++){

                               $output.='<span class="germanboxlabel">Answer '.$i.': </span> <span>'.$answers["ans".$i].'</span><br>';

                           }

                           break;

                       case 4:

                           $answers = json_decode($val->answers,true);

                           $options = json_decode($val->options,true);

                           for($i=1;$i<=4;$i++){

                               if(array_key_exists('ans'.$i,$answers)){

                                   $output.='<span class="checkboxlabel">option '.$i.'</span>:&nbsp; <span>'.$options["opt".$i].'</span> <input type="checkbox" checked disabled /><br>';

                               }else{

                               }

                                   $output.='option '.$i.':&nbsp; <span>'.$options["opt".$i].'</span> <input type="checkbox" disabled /><br>';

                               

                           }

                           break;

                       case 5:

                           $output.='<span>'.$val->answers.'</span>';

                  } 

                  echo $output.='</div>'; //output the options with the answers;

                  $x++;

                  echo '</div>';

               }

              

               echo'</div>';

           }else{

               echo '<div class="message">No questions available</div>';

           }

        ?>

        </main>

    </body>

</html>