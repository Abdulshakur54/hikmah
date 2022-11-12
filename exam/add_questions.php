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

    $allowed = [2,4,6,7,15,17]; //stores the rank of people allowed

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

		Redirect::to('index.php'); //redirect to exam index

	} 

    $rank = $user->getRank();

    if(!in_array($rank, $allowed)){ //ensure that only the allowed people can access page

		Redirect::to(404);

	}  

    $exam = new Exam();

    $qtn = new Question();

    if(Input::submitted('get') && !empty(Input::get('examid'))){

        $examId = Utility::escape(Input::get('examid'));

        if(!$exam->idExists($examId)){ //ensure that the exam id exists

            Redirect::to(404);

        }

        $details = $exam->getDetails($examId);

        $qtnNo = $qtn->getCount($examId);

    }else{

        Redirect::to('index.php'); //redirect to exam index

    }

    

?>

<!DOCTYPE html>

<html lang="en">

    <head>

        <meta name="HandheldFriendly" content="True">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Add Questions</title>

        <link rel="stylesheet" type="text/css" href="ld_loader/ld_loader.css" />

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/add_questions.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php'?>

        <main>

            <?php  echo Session::get_flash('exam created'); //output the flash message ?>

            <form method="post" action="<?php echo Utility::myself();?>" onsubmit="return false;">



                <h2 class="formhead">Add Questions to <?php echo strtoupper($details->exam_id);?></h2>

                <div id ="questionContainer">

                    <div id="hiddenDetails"><?php echo json_encode($details);?></div>

                    <div class="settings">

                        <div>

                            <label for="qtnType">Select Type:</label>

                            <select id="qtnType">

                                <option value="1">Multiple Choice</option>

                                <option value="2">True or False</option>

                                <option value="3">German</option>

                                <option value="4">Multiple Answers</option>

                                <option value="5">Theory</option>

                            </select>

                        </div>

                        <div>

                        <label for="mark">Mark(s):</label>

                            <input type="number" value="1" id="mark"/>

                        </div>

                    </div>

                    <div id="qtnIndicatorDiv">

                        <span id="qtnIndicator">Question <?php echo Utility::escape(($qtnNo + 1).'/'.$details->no_qtn_added)?></span>

                    </div>

                    <div id="qtnWrapper">

                        <div class="passagesetting">

                            <label for="addPassage">Add Passage: </label>

                            <input type="checkbox" id="addPassage"/>

                        </div>

                        <div id="passage">

                            <div id="psgBox">

                                <label for="psg">Passage:</label>

                                <textarea id="psgText"></textarea>

                            </div>

                            <label for="showPassage">Show:</label>

                            <input type="checkbox" id="showPassage" checked/> 

                        </div>

                        <div id="qtn">

                            <label for="qtnText" id="qtnLabel">Question:</label>

                            <textarea id="qtnText"></textarea>

                            <div id="msgQtn"></div>

                        </div>

                        <div class="optionsDiv">

                            <span id="optMsg">Options with Answers</span><br>

                            <div class="options" id="optionBox">



                            </div>

                        </div>

                    </div>



                    <div id="submitDiv">

                        <input type = "hidden" value = "<?php echo $qtnNo; ?>" id="qtnNo"/>

                        <input type = "hidden" value = "<?php echo Token::generate(); ?>" name = "token" id="token"/> <!--hidden token -->

                        <div id="msgGen"></div>

                        <button id="addBtn"><i class="fa fa-plus"></i> Add</button><span id="ld_loader"></span>

                        <?php echo '&nbsp; <a href="upload_questions.php?examid='.$examId.'"><i class = "fa  fa-upload"></i>Upload Questions</a>';?>

                    </div>

                </div>

                <div id="pubEditContainer">

                    <div class="message" id="msgBox">Maximum no of questions have been added</div>

                    <button id="editExamBtn">Edit Exam</button>

                    <button id="publishExamBtn">Publish Exam</button>

                </div>

            </form>

        </main>

        <script>

            window.onload = function(){

                appendScript('ld_loader/ld_loader.js');

                appendScript('scripts/script.js');

                appendScript('scripts/validation.js');

                appendScript('scripts/ajaxrequest.js');

                appendScript('scripts/add_questions.js');

            }



            function appendScript(source){

                let script = document.createElement('script');

                script.src=source;

                document.body.appendChild(script);

            }

        </script>

    </body>

</html>