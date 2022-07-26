<?php[2,4,6,7,15,17]

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

		Redirect::to('index.php'); //redirect to exam index

	} 

    $rank = $user->getRank();

    if(!in_array($rank, $allowed)){ //ensure that only the allowed people can access page

		Redirect::to(404);

	}  

    $alert = new ExamAlert();

    $exam = new Exam();

    $user_col = $user->getUsernameColumn();

    $message = '';

    if(Input::submitted('get') || Input::submitted()){ //if the form is submitted by either get or post method with a valid token

        $examId = Utility::escape(Input::get('examid'));

        if(Input::submitted('get') && !empty(Input::get('examid'))){

            if(!$exam->idExists($examId)){ //ensure that the exam id exists

                Redirect::to(404);

            }



            if($exam->isPublished($examId)){ //ensure that exam has not been published

                exit('<span class="message">Exam have been published</span>');

            }



            if(!$exam->isExaminer($examId,$user->data()->$user_col)){ //ensure that the user is the examiner of the exam to be edited

                Redirect::to(404);

            }

        }else{ //this will only run when the form is submitted using post method

            if(!Token::check(Input::get('token'))){

                exit();

            }

            $val = new Validation(true);

            $values = [

                'no_of_qtns'=>['name'=>'No of questions', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,2}$'],

                'no_of_qtns_add'=>['name'=>'No of questions to be added', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,2}$'],

                'passmark'=>['name'=>'Pass Mark', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,2}$'],

                'duration'=>['name'=>'Duration', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,10}$'],

                'count'=>['name'=>'Times Allowed to take Exam', 'required'=>true, 'pattern'=>'^[1-9]$']

            ];



            if($val->check($values)){

                $noOfQtnsAdd = Utility::escape(Input::get('no_of_qtns_add'));

                $noOfQtns = Utility::escape(Input::get('no_of_qtns'));

                $passMark = (int)Utility::escape(Input::get('passmark'));

                $duration = Utility::escape(Input::get('duration'));

                $count = Utility::escape(Input::get('count'));

                $expiryDate = Utility::escape(Input::get('expiry_date'));

                $expiryTime = Utility::escape(Input::get('expiry_time'));

                $examId = Utility::escape(Input::get('examid'));



                $username = strtoupper($user->data()->$user_col);

                //ensure that $noOfQtns is not greater than $noOfQtnsAdd

                if($noOfQtns > $noOfQtnsAdd){

                    $message.= '<div class="failure centered paragraph">No of questions cannot be greater than No of questions to be added<div>';

                }



                if(empty($expiryDate) || empty($expiryTime)){ //ensure that expiry date and time is not empty

                    $message.= '<div class="failure centered paragraph">Expiry Date and Time must be set</div>';

                }



                if(strtotime($expiryDate.' '.$expiryTime) <= time()){ //ensure that expiry date  is not set in the past

                    $message.= '<div class="failure centered paragraph">Set a future Date and Time</div>';

                }



                if(empty($message)){ //confirms there is no error

                    if($exam->edit($examId, $noOfQtnsAdd, $noOfQtns,$passMark, $duration, $count, $expiryDate, $expiryTime)){

                        $message = '<span class="success centered paragraph">Successfully saved changes</span>';

                    } 

                }

            }else{

                foreach($val->errors() as $error){

                    $message.= '<div class="failure centered paragraph">'.$error.'</div>';

                }

            }



        }

     

        $details = $exam->getDetails($examId);

        

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

        <title>Edit Exam</title>

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/edit_exam.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php'?>

        <main>

            <?php require_once 'header.inc.php'?>

            <?php 

                //display alert

                if($alert->hasAlerts($username)){

                    $count = $alert->getUnseenCount($username);

                    echo $count.' <a href="notifications.php">notifications</a>';

                }

            ?>

        <form method="post" action="<?php echo Utility::myself();?>">

            <h2 class="formhead"><?php echo strtoupper($details->exam_id).' ('.ucwords($details->title).')'?></h2>



            <div>

                <label for="noOfQtns">No of questions:</label>

                <input type="number" name="no_of_qtns" id="noOfQtns" value="<?php echo Utility::escape($details->no_qtn_req)?>"/>

            </div>

            <div>

                <label for="noOfQtnsAdd">No of questions to be added:</label>

                <input type="number" name="no_of_qtns_add" id="noOfQtnsAdd" value="<?php echo Utility::escape($details->no_qtn_added)?>"/>

            </div>

            <div>

                <label for="passMark">Pass Mark (%):</label>

                <input type="number" name="passmark" id="passMark" max="100" min="1" value="<?php echo Utility::escape($details->pass_mark)?>"/>

            </div>

            <div>

                <label for="duration">Duration (mins):</label>

                <input type="number" name="duration" id="duration" value="<?php echo Utility::escape($details->duration)?>"/>

            </div>

            <div>

                <label for="count">Times Allowed to take Exam:</label>

                <input type="number" name="count" id="count" value="<?php echo Utility::escape($details->count)?>"/>

            </div>

            <div>

                <label for="times_allowed">Expiry Date:</label>

                <input type="date" name="expiry_date" id="expiryDate" value="<?php echo Utility::escape(Utility::getDate($details->expiry))?>"/>

            </div>

            <div>

                <label for="times_allowed">Expiry Time:</label>

                <input type="time" name="expiry_time" id="expiryTime" value="<?php echo Utility::escape(Utility::getTime($details->expiry))?>"/>

            </div>

            <input type = "hidden" value = "<?php echo Token::generate() ?>" name = "token" /> <!--hidden token -->

            <div id="message" class="noGrid"><?php echo $message; ?></div>

            <input type="hidden" name="examid" value="<?php echo Utility::escape($details->exam_id)?>"/>

            <div id="submitDiv" class="noGrid">

                <button id="saveChanges"><i class="fa fa-save"></i> Save Changes</button>

            </div>

        </form>

        </main>

    </body>

</html>