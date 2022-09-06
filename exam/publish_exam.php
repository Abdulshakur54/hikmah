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

    /*

    2 -> APM

    4 -> IC

    5 -> HOS

    7 -> TS

    15 -> I_TS

    17 -> I_HOS

    */

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



    //to know the table we would be selecting examinee from together with their ranks

    switch($rank){

        case 2:

            $table = Config::get('users/table_name3');

            $examineeRank = 11;

            $usernameCol = Config::get('users/username_column3');

        break;

        case 4:

            $table = Config::get('users/table_name3');

            $examineeRank = 12;

            $usernameCol = Config::get('users/username_column3');

        break;

        case 5:

            $table = Config::get('users/table_name1');

            $examineeRank = 7;

            $usernameCol = Config::get('users/username_column1');

        break;

        case 17:

            $table = Config::get('users/table_name1');

            $examineeRank = 15;

            $usernameCol = Config::get('users/username_column1');

        break;

        case 7:

            $table = Config::get('users/table_name2');

            $examineeRank = 9;

            $usernameCol = Config::get('users/username_column2');

        break;

        case 15:

            $table = Config::get('users/table_name2');

            $examineeRank = 10;

            $usernameCol = Config::get('users/username_column2');

        break;

    }





    if(!in_array($rank, $allowed)){ //ensure that only the allowed people can access page

		Redirect::to(404);

	}  

    

    $message = '';

    $exam = new Exam();

    $user_col = $user->getUsernameColumn();

    if(Input::submitted('get') && !empty(Input::get('examid'))){

        $examId = Utility::escape(Input::get('examid'));

        if(!$exam->idExists($examId)){ //ensure that the exam id exists

            Redirect::to(404);

        }



        if($exam->isPublished($examId)){ //ensure that exam has not been published

            $message = '<div class="message">'.strtoupper($examId).' have been published</div>';

        }



        if(!$exam->isExaminer($examId,$user->data()->$user_col)){ //ensure that the user is the examiner of the exam to be edited

            Redirect::to(404);

        }



        if($exam->isExpired($examId)){ //ensure that the exam to be published have not expired

           $message = '<div class="message">'.strtoupper($examId).' has expired<br>You should edit the exam time to a future time to allow you publish  &nbsp<a href="edit_exam.php?examid='.$examId.'">edit</a></div>';

        }

    }else{

        Redirect::to('index.php');

    }

    $details = $exam->getDetails($examId);

    

    //this custom function helps to detemine which option in the select tag for sch_abbr is selected

    function getSelectedSchool($val){

        global $sch_abbr;

        return ($val === $sch_abbr)?'selected':'';

    }

    

    function getSelectedLevel($lev){

        global $level;

       return ($lev == $level)?'selected':'';

    }

    

?>

<!DOCTYPE html>

<html lang="en">

    <head>

        <meta name="HandheldFriendly" content="True">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Publish Exam</title>

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/publish_exam.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php';?>

        <main>

            <?php require_once 'header.inc.php'?>

            <?php

                if(!empty($message)){

                    echo $message;

                    exit();

                }

            ?>

        <div id="publishContainer">

            <form method="post" action="<?php echo Utility::myself();?>" onsubmit="return false;">

            <div id="instructionDiv">Exam Instruction: </div>

            <textarea id="instruction">Answer all questions</textarea>    

            <div class="sectionhead"></div>

                   

                <?php 

                    if(!empty($details->transfer)){
                        $transfer = 'true'; //indicates that the exam is instantiated from portal
                        $transferDetails = json_decode($details->transfer);
                        if($rank===2){
                            if(!empty(Input::get('sch_abbr'))){
                                $sch_abbr = Input::get('sch_abbr');

                                $level = Input::get('level');

                                $exData = $exam->chooseAdmissionExamineeData($examineeRank, $table, $usernameCol,'sch_abbr,=,'.$sch_abbr,'level,=,'.$level);

                            }else{

                                $exData = $exam->chooseAdmissionExamineeData($examineeRank, $table, $usernameCol);

                            }



                            echo '<label>Select School</label> <select name="sch_abbr" id="sch_abbr" onchange="populateLevel(this)"><option value="ALL" selected>ALL</option>';

                            $sch_abbrs = School::getConvectionalSchools(2); //this returns the abbreviation for each of the convectional schools

                            foreach ($sch_abbrs as $sch_ab){

                                echo '<option value="'.$sch_ab.'" '.getSelectedSchool($sch_ab).'>'.$sch_ab.'</option>';

                            }

                            echo '</select>'; 

                            echo '<label>Select Level</label>

                                <select name="level" id="level"><option value="ALL" '.getSelectedLevel("ALL").'>ALL</option>';

                                $levels = School::getLevels($sch_abbr);

                                if(!empty($levels)){

                                     foreach ($levels as $levName=>$lev){

                                        echo '<option value="'.$lev.'" '.getSelectedLevel($lev).'>'.$levName.'</option>';

                                     }

                                }



                                echo '</select> <button onclick="reSubmit()">Filter</button>';

                            if(empty($exData)){

                                echo '<div class="message">No records Available</div>';

                            }else{

                                echo '<table>

                                <tr id="headerRow"><th>Id</th><th>Name</th><th>School</th><th>Level Name</th><th>Action</th></tr><tbody>';

                                $count = 1;

                                foreach($exData as $val){

                                    $idVal = Utility::escape(strtoupper($val->$usernameCol));

                                    echo '<tr><td>'.$idVal.'</td><td>'.Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)).'</td><td>'.$val->sch_abbr.'</td><td>'.School::getLevelName($val->sch_abbr, $val->level).'</td><td><input type="checkbox" id="chk'.$count.'" checked /><input type="hidden" id="val'.$count.'" value="'.$idVal.'"/></td></tr>';

                                    $count++;

                                }

                                echo '</table>';

                            }
                        }
                        
                        //the examiners below are expected to be the teacher either islamic or convectional
                        if($rank == 7 || $rank == 15){
                            $exData = $exam->chooseTeacherExamineeData($transferDetails->tableName, $usernameCol,$transferDetails->subid);
                            if(empty($exData)){

                                echo '<div class="message">No records Available</div>';

                            }else{

                                echo '<table>

                                <tr id="headerRow"><th>Student Id</th><th>Name</th><th>Action</th></tr><tbody>';

                                $count = 1;

                                foreach($exData as $val){

                                    $idVal = Utility::escape(strtoupper($val->$usernameCol));

                                    echo '<tr><td>'.$idVal.'</td><td>'.Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)).'</td><td><input type="checkbox" id="chk'.$count.'" checked /><input type="hidden" id="val'.$count.'" value="'.$idVal.'"/></td></tr>';

                                    $count++;

                                }

                                echo '</table>';

                            }
                        }
                        

                    }else{
                        $transfer = 'false'; //indicates that the exam is not initiated from  portal
                        $exData = $exam->chooseExamineeData($examineeRank, $table, $usernameCol);

                        if(empty($exData)){

                            echo '<div class="message">No records Available</div>';

                        }else{

                            echo '<table>

                            <tr id="headerRow"><th colspan="3">Tick Examinees</th></tr><tbody>';

                            $count = 1;

                            foreach($exData as $val){

                                $idVal = Utility::escape(strtoupper($val->$usernameCol));

                                echo '<tr><td>'.Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)).'</td><td>'.$idVal.'</td><td><input type="checkbox" id="chk'.$count.'" checked /><input type="hidden" id="val'.$count.'" value="'.$idVal.'"/></td></tr>';

                                $count++;

                            }

                            echo '</table>';

                        }

                    }

                    

                ?>

                
                <input type="hidden" value="<?php echo $transfer?>" name = "transfer" id="transfer" />
                <input type = "hidden" value = "<?php echo $examineeRank; ?>" id="type" /> <!-- hidden examinee rank -->

                <input type = "hidden" value = "<?php echo $examId ?>" id="examId" /> <!--hidden examId -->

                <input type = "hidden" value = "<?php echo($count-1); ?>" id="counter" /> <!--hidden counter -->

                <input type = "hidden" value = "<?php echo Token::generate() ?>" name = "token" id="token" /> <!--hidden token -->

                

                <div>

                    <button id="publishBtn"><i class="fa  fa-arrow-circle-right"></i> Publish</button><span id="ld_loader"></span>

                </div>

            </form>

        </div>

        <div id="message">



        </div>

        </main>

        <script>

            window.onload = function(){

                appendScript('ld_loader/ld_loader.js');

                appendScript('scripts/script.js');

                appendScript('scripts/ajaxrequest.js');

                 appendScript('scripts/portalscript.js');

                appendScript('scripts/publish_exam.js');

            }



            function appendScript(source){

                let script = document.createElement('script');

                script.src=source;

                document.body.appendChild(script);

            }

        </script>

    </body>



</html>