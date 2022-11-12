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

    if(Input::submitted() && Token::check(Input::get('token'))){

        $val = new Validation(true);

        $values = [

            'examid'=>['name'=>'Exam ID','required'=>true, 'max'=>50, 'min'=>3, 'pattern'=>'^[a-zA-Z][a-zA-Z0-9 ]{2,}$', 'unique'=>'exam_id/ex_exam'],

            'title'=>['name'=>'Title', 'required'=>true, 'max'=>500, 'min'=>3, 'pattern'=>'^[a-zA-Z][a-zA-Z0-9 ]+$', 'unique'=>'title/ex_exam'],

            'no_of_qtns'=>['name'=>'No of questions', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,2}$'],

            'no_of_qtns_add'=>['name'=>'No of questions to be added', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,2}$'],

            'passmark'=>['name'=>'Pass Mark', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,2}$'],

            'duration'=>['name'=>'Duration', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,10}$'],

            'count'=>['name'=>'Times Allowed to take Exam', 'required'=>true, 'pattern'=>'^[1-9]$']

        ];



        if($val->check($values)){

            $examId = Utility::escape(Input::get('examid'));

            $title = Utility::escape(Input::get('title'));

            $noOfQtnsAdd = Utility::escape(Input::get('no_of_qtns_add'));

            $noOfQtns = Utility::escape(Input::get('no_of_qtns'));

            $passMark = (int)Utility::escape(Input::get('passmark'));

            $duration = Utility::escape(Input::get('duration'));

            $count = Utility::escape(Input::get('count'));

            $expiryDate = Utility::escape(Input::get('expiry_date'));

            $expiryTime = Utility::escape(Input::get('expiry_time'));



            //get the id(username)

            $user_col = $user->getUsernameColumn();

            $username = strtoupper($user->data()->$user_col);

            //ensure that $noOfQtns is not greater than $noOfQtnsAdd

            if($noOfQtns > $noOfQtnsAdd){

                $message.= 'No of questions cannot be greater than No of questions to be added<br>';

            }



            if(empty($expiryDate) || empty($expiryTime)){ //ensure that expiry date and time is not empty

                $message.= 'Expiry Date and Time must be set<br>';

            }



            if(strtotime($expiryDate.' '.$expiryTime) <= time()){ //ensure that expiry date and time is not empty

                $message.= '<div class="failure">Set a future Date and Time</div>';

            }

            $transfer =  (!empty(Input::get('transfer'))) ? Input::get('transfer'):null; //transfer here means that this score for this e-exam is to be copied to the main portal

            

            if(empty($message)){ //confirms there is no error

                if($exam->add($examId, $title, $username, $noOfQtnsAdd, $noOfQtns,$passMark, $duration, $count, $expiryDate, $expiryTime,$transfer)){

                    Session::set_flash('exam created','<div class="success paragraph centered">Successfully initiated '.strtoupper($examId).' ('.ucwords($title).')</div>');

                    Redirect::to('add_questions.php?examid='.$examId);

                } 

            }

        }else{

            foreach($val->errors() as $error){

                $message.= $error . '<br>';

            }

        }

        

    }

    

    //custom function

    function transferDetails(){

       if(isset($_GET['transfer'])){

           return Utility::escape($_GET['transfer']);

       }else if(isset($_POST['transfer'])){

            return Utility::escape($_POST['transfer']);

       }else{

           return '';

       }

    }

?>

<!DOCTYPE html>

<html lang="en">

    <head>

        <meta name="HandheldFriendly" content="True">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Initiate Exam</title>

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/new_exam.css" />

    </head>

    <body>

            <?php require_once 'nav.inc.php'?>

        <main>

            <?php require_once 'header.inc.php';?>

            <form method="post" action="<?php echo Utility::myself();?>">

                <h2 class="formhead">Initiate An Exam <?php if(!empty(transferDetails())){echo '(formal)';}//this is to help indicate to the user the exam is a transferrable exam?></h2>

                <div id="gridContainer">

                    <div class="inputWithMsgBox">

                        <label for="examId">Choose ID:</label>

                        <input type="text" name="examid" id="examId" value="<?php echo Utility::escape(Input::get('examid'))?>" />

                        <div id="idMsg"  class="leftPad"></div>

                    </div>

                    <div class="inputWithMsgBox">

                        <label for="title">Title:</label>

                        <input type="text" name="title" id="title" value="<?php echo Utility::escape(Input::get('title'))?>" />

                        <div id="titleMsg"  class="leftPad"></div>

                    </div>

                    <div>

                        <label for="noOfQtns">No of questions:</label>

                        <input type="number" name="no_of_qtns" id="noOfQtns" value="<?php echo Utility::escape(Input::get('no_of_qtns'))?>"/>

                    </div>

                    <div>

                        <label for="noOfQtnsAdd">No of questions to be added:</label>

                        <input type="number" name="no_of_qtns_add" id="noOfQtnsAdd" value="<?php echo Utility::escape(Input::get('no_of_qtns_add'))?>"/>

                    </div>

                    <div>

                        <label for="passMark">Pass Mark (%):</label>

                        <input type="number" name="passmark" id="passMark" max="100" min="1" value="<?php echo empty(Input::get('passmark')) ? 50:Utility::escape(Input::get('passmark'))?>"/>

                    </div>

                    <div>

                        <label for="duration">Duration (mins):</label>

                        <input type="number" name="duration" id="duration" value="<?php echo Utility::escape(Input::get('duration'))?>"/>

                    </div>

                    <div>

                        <label for="count">Times Allowed to take Exam:</label>

                        <input type="number" name="count" id="count" value="<?php echo empty(Input::get('count')) ? 3:Utility::escape(Input::get('count'))?>"/>

                    </div>

                    <div>

                        <label for="expiry_date">Expiry Date:</label>

                        <input type="date" name="expiry_date" id="expiryDate" value="<?php echo Utility::escape(Input::get('expiry_date'))?>"/>

                    </div>

                    <div>

                        <label for="times_allowed">Expiry Time:</label>

                        <input type="time" name="expiry_time" id="expiryTime" value="<?php echo Utility::escape(Input::get('expiry_time'))?>"/>

                    </div>

                </div>

                <input type = "hidden" value = "<?php echo Token::generate() ?>" name = "token" /> <!--hidden token -->

                <textarea name="transfer" style="display:none"><?php echo transferDetails() ?></textarea><!--necessary so as to give access to set exam from the main portal -->

                <div style="text-align: center;"><?php echo $message;?></div>

                <div>

                    <input type="submit" value="Initiate" id="createExam"/>

                </div>

                

            </form>

        </main>

    </body>

</html>