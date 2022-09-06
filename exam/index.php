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

    $mgt = new Management();

    $staff = new Staff();

    $std = new Student();

    $adm = new Admission();

    $user = null;

	//ensures that right people have logged

    if($mgt->isRemembered()){

        $user = $mgt;

    }

    if($staff->isRemembered()){

        $user = $staff;

    }

	if($std->isRemembered()){

        $user = $std;

    }

	if($adm->isRemembered()){

        $user = $adm;

    }

	//end of ensure that right people logged in

	if(!isset($user)){ //ensure user is legally logged in

		Redirect::home('index.php',0); //redirect to home portal

	} 

	?>

<!DOCTYPE html>

<html lang="en">	

	<head>

		<meta name="HandheldFriendly" content="True">

		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<title>Exam Portal</title>

		<link rel="stylesheet" type="text/css" href="styles/index.css"/>

	</head>

	<body>

            <?php require_once 'nav.inc.php'?>

            <main>

                <div id="examiners">

                <?php require_once 'header.inc.php'?>

                <?php

		$rank = $user->getRank(); 

		$canCreateExam = [2,4,5,6,7];

		if(in_array($rank, $canCreateExam)){

			?>

                        <div class="headDiv">What would you like to do today?</div>

			

                            <?php

                            if(!in_array($rank, [7,15])){

                                ?>

                                <div id="examinerDiv">

                                    <a href="new_exam.php"><i class="fa fa-plus"></i> Initiate New Exam</a>

                                    <a href="unpublished_exams.php"><i class="fa fa-folder-open"></i> Unpublished Exams</a>

                                    <a href="published_exams.php"><i class="fa fa-book"></i> Published Exams</a>

                                    <a href="<?php echo $url->to('logout.php', $cat) ?>"><i class="fa fa-sign-out"></i>Logout</a>

				</div>

                            

                                    <?php

                            }else{ ?>

                                <div class="headDiv innerheadDiv">As an Examiner</div>

				<div id="examinerDiv">

                                    <a href="new_exam.php"><i class="fa fa-plus"></i> Initiate New Exam</a>

                                    <a href="unpublished_exams.php"><i class="fa fa-folder-open"></i> Unpublished Exams</a>

                                    <a href="published_exams.php"><i class="fa fa-book"></i> Published Exams</a>

				</div>

				<div class="headDiv innerheadDiv">As an Examinee</div>

				<div  id="examineeDiv">

                                    <a href="take_exams.php"><i class="fa fa-pencil"></i> Take Exam</a>

                                    <a href="completed_exams.php"><i class="fa fa-check-square"></i> Completed Exams</a>

                                    <a href="<?php echo $url->to('logout.php', $cat) ?>"><i class="fa fa-sign-out"></i>Logout</a>

				</div>

                            <?php

                            }

                            

		}else{

			?>

                        <div class="headDiv">What would you like to do today?</div>

                        <div  id="examineeDiv">

                            <a href="take_exams.php"><i class="fa fa-pencil"></i> Take Exam</a>

                            <a href="completed_exams.php"><i class="fa fa-check-square"></i> Completed Exams</a>

                            <a href="<?php echo $url->to('logout.php', $cat) ?>"><i class="fa fa-sign-out"></i>Logout</a>

                        </div>

			

			<?php

		}

			?>

                    </div>

            </main>

		



	</body>

</html>