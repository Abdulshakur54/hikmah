<?php

//initializations

spl_autoload_register(

    function ($class) {

        require_once '../classes/' . $class . '.php';
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

$message = '';

if ($mgt->isRemembered()) {

    $user = $mgt;
}

if ($staff->isRemembered()) {

    $user = $staff;
}

if ($std->isRemembered()) {

    $user = $std;
}

if ($adm->isRemembered()) {

    $user = $adm;
}

if (!isset($user)) { //ensure user is legally logged in

    Redirect::to('index.php'); //redirect to exam home page

}





?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta name="HandheldFriendly" content="True">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Take Exams</title>

    <link rel="stylesheet" type="text/css" href="styles/style.css" />

    <link rel="stylesheet" type="text/css" href="styles/take_exams.css" />

</head>

<body>

    <?php require_once 'nav.inc.php'; ?>

    <main>

        <?php require_once 'header.inc.php' ?>

        <?php

        $exam = new Exam();

        $availableExams = $exam->getAvailableExams($username);

        if (!empty($availableExams)) {

            echo '<table>

                        <tr id="headerRow"><th colspan="4">Published Exams</th></tr><tbody>

                    ';

            foreach ($availableExams as $exm) {

                //check if exam has not expired

                if (!(strtotime($exm->expiry) <= time())) { //exam yet to expire

                    echo '<tr><td>' . strtoupper($exm->exam_id) . '</td><td>' . ucwords($exm->title) . '<td><a href="ongoing_exam.php?examid=' . $exm->exam_id . '"><i class="fa fa-pencil"></i> take exam</a></td></td><td class="message">active</td></tr>';
                } else {

                    echo '<tr><td>' . strtoupper($exm->exam_id) . '</td><td>' . ucwords($exm->title) . '</td><td class="failure">expired</td></tr>';
                }
            }

            echo '</table>';
        } else {

            echo '<div class="message">No available exams</div>';
        }

        ?>

        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
        <!--generates a token to be used by javascript for get requests-->

    </main>

    <script>
        window.onload = function() {

            appendScript("scripts/script.js");

            appendScript("scripts/published_exam.js");

        }



        function appendScript(source) {

            let script = document.createElement("script");

            script.src = source;

            document.body.appendChild(script);

        }
    </script>

</body>

</html>