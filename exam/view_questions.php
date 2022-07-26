<?php [2,4,6,7,15,17]

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

    $qtn = new Question();

    $exam = new Exam();

    if(Input::submitted('get')){

        $published = null; //initialize $published variable

        if(!empty(Input::get('token')) && Token::check(Input::get('token'))){ //this would return true if the intended action is to delete a question

            $examId = Utility::escape(Input::get('examid'));

            $qtnId = Utility::escape(Input::get('qtnid'));

            if($exam->isPublished($examId)){

                echo '<div class="message centered paragraph">'.strtoupper($examId).' questions cannot be deleted after being published</div>';

                exit();

            }

            if($qtn->delete($qtnId, $examId)){

                Session::set_flash('deletequestion','<div class="success centered paragraph">Question deleted successfully</div>');

            }else{

                Session::set_flash('deletequestion','<div class="failure centered paragraph">Unable to delete '.strtoupper($examId).'</div>');

            }

       }

       

    }else{

        Redirect::to('index.php'); //exam home page

    }

?>

<!DOCTYPE html>

<html lang="en">

    <head>

        <meta name="HandheldFriendly" content="True">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>View Questions</title>

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/view_questions.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php'?>

        <main>

            <?php require_once 'header.inc.php'?>

        <?php

            function insertClass(){

                global $published;

                return ($published) ? 'published':'unpublished';

            }



            function insertColSpan(){

                global $published;

                return ($published) ? 1:3;

            }



            $examId = Utility::escape(Input::get('examid'));

            $qtns = $qtn->getQuestions($examId);

            $published = (!empty($published)) ?$published : $exam->isPublished($examId);

            echo '<div id="otherLinks"><a href="det_view_questions.php?examid='.$examId.'"><i class = "fa fa-th"></i> Detailed View</a>';

            if(!$published){ //add 'Add more' link if exam is not published

                echo '<a href="add_questions.php?examid='.$examId.'"><i class = "fa fa-plus"></i> Add</a>';

                echo '<a href="upload_questions.php?examid='.$examId.'"><i class = "fa  fa-upload"></i> Upload</a>';

            }

            echo '</div>'; //close the div tag

            if(!empty($qtns)){

                

                echo '<table>

                    <tr id="headerRow"><th colspan="'.insertColSpan().'">'.strtoupper($examId).' Questions</th></tr><tbody class="'.insertClass().'">';

                    echo Session::get_flash('deletequestion'); //outputs a flash message

                    echo Session::get_flash('updatequestion'); //outputs a flash message

                foreach($qtns as $val){

                    $examId = Utility::escape($val->exam_id);

                    $qtnId = Utility::escape($val->id);

                    $row =  '<tr><td>'.Utility::escape($val->qtn).'</td>';

                    if(!$published){ //add edit and delete functionality only if exam have not been published

                        $row.='<td><a href="edit_question.php?qtnid='.$qtnId.'&examid='.$examId.'"><i class="fa fa-edit"></i> Edit</a></td><td><a href="#" onclick="deleteQuestion(\''.$qtnId.'\',\''.$examId.'\')"><i class="fa fa-trash"></i> Delete</a></td>';

                    }

                    $row.='</tr>';

                    echo $row;

                }

                echo '</tbody></table>

                ';

                echo '<input type="hidden" id="token" value="'.Token::generate().'"/>'; //generates a token to be used by javascript for get requests

            }else{

                echo '<div class="message">No questions available</div>';

            }

        ?>

        </main>

        <script>

            window.onload = function(){

                appendScript("scripts/script.js");

                appendScript("scripts/view_questions.js");

            }



            function appendScript(source){

                let script = document.createElement("script");

                script.src=source;

                document.body.appendChild(script);

            }

        </script>

    </body>

</html>