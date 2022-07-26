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

    $exam = new Exam();

    $qtn = new Question();

    if(!Input::submitted('get') || empty(Input::get('examid'))){

        exit(); //this is to ensure that the page is accessed properly

    }

    $examId = Utility::escape(Input::get('examid'));

        if(!$exam->isPublished($examId)){

            exit(); //ensure that only published exam details can be viwed

        }



        function passCount(){

            global $passcount, $failcount;

            $total = $passcount + $failcount;

            if($total === 0){

                return 0;

            }

            return round((($passcount/$total) * 100),2);

        }

        ?>



<!DOCTYPE html>

<html lang="en">

    <head>

        <title>Exam Details</title>

        <meta name="HandheldFriendly" content="True">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/details.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php'?>

        <main>

            <?php require_once 'header.inc.php'?>

        <?php

             $details = $exam->getDetails($examId);

             echo '<table>

                  <tr id="headerRow"><th colspan="2">'.strtoupper($examId).' Details</th></tr><tbody>';

             $row = '<tr><td>Title: </td><td>'.ucwords($details->title).'</td></tr>';

             $row .= '<tr><td>No of Questions: </td><td>'.$details->no_qtn_req.'</td></tr>';

             $row .= '<tr><td>Passmark: </td><td>'.$details->pass_mark.'%</td></tr>';

             $row .= '<tr><td>Duration: </td><td>'.$details->duration.'mins</td></tr>';

             $row .= '<tr><td>Expiry: </td><td>'.Utility::formatDate($details->expiry).'</td></tr>';

             $row .=  '<tr id="tablesectionhead"><td colspan=2>Performance details</td></tr>';

             $totalExaminees = $exam->getExamineeCount($examId);

             $participated = $exam->getCompletedExamCount($examId);

             $passcount = $exam->getExamineePassCount($examId);

             $failcount = $exam->getExamineeFailCount($examId);

             $row .= '<tr><td>Total no of Examinees: </td><td>'.$totalExaminees.'</td></tr>';

             $row .= '<tr><td>No of examiness participated: </td><td>'.$participated.'</td></tr>';

             $row .= '<tr><td>No of examiness yet to participate: </td><td>'.($totalExaminees - $participated).'</td></tr>';

             $row .= '<tr><td>No of examiness that passed: </td><td>'.$passcount.'</td></tr>';

             $row .= '<tr><td>No of examiness that failed: </td><td>'.$failcount.'</td></tr>';

             $row .= '<tr><td>Percentage passed: </td><td>'.passCount().'%</td></tr>'; //pass percentage is calculated then appended here

             echo $row;

             echo '</tbody></table>';



        ?>

        </main>

    </body>

</html>

    