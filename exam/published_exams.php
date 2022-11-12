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

    $rank = $user->getRank();

    if(!in_array($rank, $allowed)){ //ensure that only the allowed people can access page

		Redirect::to(404);

	}  

    $exam = new Exam();

    $qtn = new Question();





    if(Input::submitted('get') && Token::check(Input::get('token'))){//runs if page is entered via get request with params, this would be true for delete exam functionality

        $examId = Utility::escape(Input::get('examid'));

        if($exam->deletePubExam($examId)){

            Session::set_flash('deleteexam','<div class="success">Successfully deleted '.strtoupper($examId).'</div>');

        }else{

            Session::set_flash('deleteexam','<div class="failure">Unable to delete '.strtoupper($examId).'</div>');

        }

    }?>

    <!DOCTYPE html>

    <html lang="en">

        <head>

            <meta name="HandheldFriendly" content="True">

            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

            <meta http-equiv="X-UA-Compatible" content="IE=edge">

            <title>Published Exams</title>

            <link rel="stylesheet" type="text/css" href="styles/style.css" />

            <link rel="stylesheet" type="text/css" href="styles/published_exam.css" />

        </head>

        <body>

            <?php require_once 'nav.inc.php'?>

            <main>

                <?php require_once 'header.inc.php'?>

            <?php

                 $published = $exam->getPublished($username);

                 if(!empty($published)){

                     echo '<table>

                         <tr id="headerRow"><th colspan="8">Published Exams</th></tr><tbody>';

                         echo Session::get_flash('deleteexam'); //outputs a flash message

                     foreach($published as $val){

                         $examId = Utility::escape($val->exam_id);

                         if(!$exam->hasUnmarkedTheory($examId)){

                              $row =  '<tr><td>'.strtoupper($examId).'</td><td><a href="edit_passmark.php?examid='.$val->exam_id.'"><i class="fa fa-edit"></i> edit</a></td><td><a href="view_questions.php?examid='.$val->exam_id.'"><i class="fa fa-question question"></i> questions</a></td><td><a href="results.php?examid='.$val->exam_id.'"><i class="fa fa-file-text"></i> results</a></td><td><a href="scripts.php?examid='.$val->exam_id.'"><i class="fa fa-files-o"></i> scripts</a></td><td><a href="details.php?examid='.$val->exam_id.'"><i class="fa fa-th"></i> details</a></td><td><a href="#" onclick="deleteExam(\''.$examId.'\')"><i class="fa fa-trash"></i> delete</a></td><td></td>';

                         }else{

                              $row =  '<tr><td>'.strtoupper($examId).'</td><td><a href="edit_passmark.php?examid='.$val->exam_id.'"><i class="fa fa-edit"></i> edit</a></td><td><a href="view_questions.php?examid='.$val->exam_id.'"><i class="fa fa-question question"></i> questions</a></td><td>awaiting</td><td>awaiting</td><td><a href="details.php?examid='.$val->exam_id.'"><i class="fa fa-th"></i> details</a></td><td><a href="#" onclick="deleteExam(\''.$examId.'\')"><i class="fa fa-trash"></i> delete</a></td><td><a href="mark.php?examid='.$examId.'"><i class="fa fa-check"></i> mark scripts</a></td>';

                         }

                        

                         $row.='</tr>';

                         echo $row;

                     }

                     echo '</tbody></table>';

                 }else{

                     echo '<div class="message">No records available</div>';

                 }

            ?>

            <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" /><!--generates a token to be used by javascript for get requests-->

            </main>

            <script>

                window.onload = function(){

                    appendScript("scripts/script.js");

                    appendScript("scripts/published_exam.js");

                }



                function appendScript(source){

                    let script = document.createElement("script");

                    script.src=source;

                    document.body.appendChild(script);

                }

            </script>

        </body>

    </html>

       

    