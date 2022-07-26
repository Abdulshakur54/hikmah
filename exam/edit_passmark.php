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

    $message = '';

    $exam = new Exam();

    $user_col = $user->getUsernameColumn();

    if(Input::submitted('get') || Input::submitted()){ //if the form is submitted by either get or post method with a valid token

        $examId = Utility::escape(Input::get('examid'));

        if(Input::submitted('get') && !empty(Input::get('examid'))){

            if(!$exam->idExists($examId)){ //ensure that the exam id exists

                Redirect::to(404);

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

                'passmark'=>['name'=>'Pass Mark', 'required'=>true, 'pattern'=>'^[1-9][0-9]{0,2}$']

            ];



            if($val->check($values)){

                $passMark = (int)Utility::escape(Input::get('passmark'));

                $examId = Utility::escape(Input::get('examid'));

                $expiryDate = Utility::escape(Input::get('expiry_date'));

                $expiryTime = Utility::escape(Input::get('expiry_time'));

                

                $username = $user->data()->$user_col;



                if(empty($expiryDate) || empty($expiryTime)){ //ensure that expiry date and time is not empty

                    $message.= '<div class="failure">Expiry Date and Time must be set</div>';

                }



                if(strtotime($expiryDate.' '.$expiryTime) <= time()){ //ensure that expiry date  is not set in the past

                    $message.= '<div class="failure">Set a future Date and Time</div>';

                }



                if(empty($message)){ //confirms there is no error

                    if($exam->editPassMark($examId,$expiryDate.' '.$expiryTime, $passMark)){

                        //updated passed status

                        $exam->updatePassedStatus($examId, $passMark);

                        $message = '<div class="success">Successfully saved changes</div>';

                    } 

                }

            }else{

                foreach($val->errors() as $error){

                    $message.= '<div class="failure">'.$error.'</div>';

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

        <link rel="stylesheet" type="text/css" href="styles/edit_passmark.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php'?>

        <main>

            <?php require_once 'header.inc.php'?>

        <form method="post" action="<?php echo Utility::myself();?>">

            <h2 class="formhead"><?php echo strtoupper($details->exam_id).' ('.ucwords($details->title).')"'?></h2>

            <div>

                <label for="passMark">Pass Mark(%):</label>

                <input type="number" name="passmark" id="passMark" max="100" min="1" value="<?php echo Utility::escape($details->pass_mark)?>"/>

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

            <div id="message"><?php echo $message; ?></div>

            <input type="hidden" name="examid" value="<?php echo Utility::escape($details->exam_id)?>"/>

            <div id="submitDiv">

                <button id="saveChanges"><i class="fa fa-save"></i> Save Changes</button>

            </div>

        </form>

        </main>

    </body>

</html>