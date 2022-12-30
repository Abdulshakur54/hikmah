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









        //the functions enclosed here is to help generate dynamic content for the page based on the question type

    

        function selectedType($type){

            global $qtnDetails;

            if((int)$qtnDetails->type === $type){

                return 'selected';

            }

            return '';

        }



        function dynamicMark(){

            global $qtnDetails;

            switch($qtnDetails->type){

                case 3:

                    $ansCount = count(json_decode($qtnDetails->answers,true));

                    return ($qtnDetails->mark)/$ansCount;

                case 4:

                    $ansCount = count(json_decode($qtnDetails->answers,true));

                    return ($qtnDetails->mark)/$ansCount;

                default:

                    return $qtnDetails->mark;

            }

        }



        function passageExists(){

            global $qtnDetails;

            return (!empty($qtnDetails->passage)) ? 'checked':'';

        }

       



        function outputPassage(){

            global $qtnDetails;

            return (!empty($qtnDetails->passage)) ? $qtnDetails->passage:'';

        }



        function showPassageDiv(){

            return '<div id="passage">

                            <div id="psgBox">

                                <div><label for="psg">Passage:</label></div>

                                <textarea id="psgText">'.outputPassage().'</textarea>

                            </div>

                            <label for="showPassage">Show:</label>

                            <input type="checkbox" id="showPassage" checked />

                        </div>';

           

        }



        function outputOptions(){

            global $qtnDetails;

            $output = '';

            switch($qtnDetails->type){

                case 1:

                    $equivAns = equivalentAnswer($qtnDetails->answers);

                    $options = json_decode($qtnDetails->options,true);

                    for($i=1;$i<=4;$i++){

                        $equivLetter = equivalentLetter($i);

                        $output.='<div>';

                        if($equivAns === $i){

                            $output.='<input type="radio" checked id="opt'.$equivLetter.'" name="multichoice" />';

                        }else{

                            $output.='<input type="radio" id="opt'.$equivLetter.'" name="multichoice" />';

                        }

                      

                        $output .= ' <label for="opt'.$equivLetter.'">'.$equivLetter.'</label>&nbsp;<input type="text" value="'.$options['opt'.$i].'" id="optText'.$equivLetter.'" name="multichoice" /></div>';

                    }

                break;

                case 2:

                    if($qtnDetails->answers === 'true'){

                        $output.='<input type="radio" checked id="trueRad" name="radBtn" /><label for="trueRad">True</label>  &nbsp;<input type="radio" id="falseRad" name="radBtn" /><label for="falseRad">False</label>';

                    }else{

                        $output.='<input type="radio" id="trueRad" name="radBtn" /><label for="trueRad">True</label>  &nbsp;<input type="radio" id="falseRad" name="radBtn" checked /><label for="falseRad">False</label>';

                    }

                break;

                case 3:

                    $answers = json_decode($qtnDetails->answers,true);

                    $ansLen = count($answers);

                    for($i=1;$i<=$ansLen;$i++){

                        $output.='<label for="ans'.$i.'">Answer '.$i.': </label> <input type="text" value="'.$answers["ans".$i].'" id="ans'.$i.'" /><br>';

                    }

                    $output.='<label for="ans_ord">Answer in order:</label> <input type="checkbox" id="ans_ord" '.outputAnsOrd().'/>';

                break;

                case 4:

                    $answers = json_decode($qtnDetails->answers,true);

                    $options = json_decode($qtnDetails->options,true);

                    for($i=1;$i<=4;$i++){

                        $output.='<div>';

                        if(array_key_exists('ans'.$i,$answers)){

                            $output.='<label>option '.$i.':</label> <input type="text" value="'.$options["opt".$i].'" id="opt'.$i.'" /> <input type="checkbox" checked id="chk'.$i.'"/><br>';

                        }else{

                            $output.='<label>option '.$i.':</label> <input type="text" value="'.$options["opt".$i].'" id="opt'.$i.'" /> <input type="checkbox" id="chk'.$i.'"/><br>';

                        }

                         $output.='</div>';

                        

                    }

                break;

                case 5:

                    $answers = $qtnDetails->answers;

                    $output.='<textarea id="theoryAns">'.$answers.'</textarea>';

                break;

            }

            return $output;

        }



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

    



        function outputAnsOrd(){

            global $qtnDetails;

            if($qtnDetails->answer_order){

                return 'checked';

            }

            return '';

        }

        

        //this function helps make the optionsAnswer header dynamic

        function optionHeaderHTML(){

            global $qtnDetails;

            if($qtnDetails->type === 5){

                return 'Answer';

            }else{

                return 'Options with Answers';

            }

            

        }

    

    //end of enclosed functions



    if(Input::submitted('get') && !empty(Input::get('qtnid')) && !empty(Input::get('examid'))){

       

    }else{

        Redirect::to('index.php'); //redirect to exam homepage

    }

?>

<!DOCTYPE html>

<html lang="en">

    <head>

        <meta name="HandheldFriendly" content="True">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Edit Question</title>

        <link rel="stylesheet" type="text/css" href="ld_loader/ld_loader.css" />

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/edit_question.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php'; ?>

        <main>

        <?php

            $examId = Utility::escape(Input::get('examid'));

            $qtnId = Utility::escape(Input::get('qtnid'));

            $qtn = new Question();

            $exam = new Exam();

            $qtnDetails = $qtn->getQuestionDetails($qtnId,$examId);

            if($exam->isPublished($examId)){

                echo '<div class="message">'.strtoupper($examId).' cannot be edited after being published</div>';

                exit();

            }

        





        echo '<h2 class="formhead">Edit '.strtoupper($examId).' Question</h2>'; //output the heading

        //output question settings

        echo '<div class="settings">

        <div>

        <label for="qtnType">Select Type:</label>

            <select id="qtnType">

                <option value="1"'.selectedType(1).'>Multiple Choice</option>

                <option value="2"'.selectedType(2).'>True or False</option>

                <option value="3"'.selectedType(3).'>German</option>

                <option value="4"'.selectedType(4).'>Multiple Answers</option>

                <option value="5"'.selectedType(5).'>Theory</option>

            </select>

        </div>

        <div>

            <label for="mark">Mark:</label>

            <input type="number" value="'.dynamicMark().'" id="mark"/>mk(s)

        </div>

    </div>';

    //output a passage with its settings if available

    echo '   <div class="qtnpassage">

                <div class="passagesetting">

                    <label for="addPassage">Add Passage: </label>

                    <input type="checkbox" id="addPassage" '.passageExists().'/>

                </div>

            </div>'.showPassageDiv();

    

    //outputs the question

    echo '<div id="qtn">

        <label for="qtnText" id="qtnLabel">Question</label>

        <textarea id="qtnText">'.$qtnDetails->qtn.'</textarea>

        <div id="msgQtn"></div>

    </div>';

    //output the options with its answer

    echo '<div class="optionsDiv">

        <span id="optMsg">'.optionHeaderHTML().'</span>

        <div class="options" id="optionBox">

        '.outputOptions().'

        </div>

    </div>';

    echo'<div id="msgGen"></div>'; //outputs a div to display error messages

    echo ' <div id="hiddenDetails">'.json_encode($qtnDetails).'</div>'; //store the qtnDetails so it is accessible via javascript

    echo '<input type = "hidden" value = "'.Token::generate().'" name = "token" id="token"/>';

    echo '<div>

        <button id="updBtn"><i class="fa fa-save"></i> Save</button><span id="ld_loader"></span>

    </div>';

    //include scripts

    echo '<script>

        window.onload = function(){

            appendScript("ld_loader/ld_loader.js");

            appendScript("scripts/script.js");

            appendScript("scripts/validation.js");

            appendScript("scripts/ajaxrequest.js");

            appendScript("scripts/edit_question.js");

        }



        function appendScript(source){

            let script = document.createElement("script");

            script.src=source;

            document.body.appendChild(script);

        }

    </script>';

    ?>

        </main>

    </body>

</html>