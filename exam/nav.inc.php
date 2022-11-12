<?php

    $url = new Url();

    $user_col = $user->getUsernameColumn();

    $id_col = $user->getIdColumn();

    $data = $user->data();

    $id = $data->$id_col;

    $username = $data->$user_col;

    $rank = $user->getRank(); 

	$canCreateExam = [2,4,5,6,7];



    switch($user_col){

        case 'mgt_id':

            $cat = 1;

        break;

        case 'staff_id':

            $cat = 2;

        break;

        case 'std_id':

            $cat = 3;

        break;

        case 'adm_id':

            $cat = 5;

        break;

    }

    

    function isActive($link){

        if(basename(Utility::escape($_SERVER['PHP_SELF'])) === $link){

            return 'class="active"';

        }

    }

?>

<link href="styles/nav.inc.css" type="text/css" rel="stylesheet"/>

<script src="https://use.fontawesome.com/cffe8fbd41.js"></script>

<header>

    <div id="logoContainer">

        <img src="<?php echo Utility::escape($url->to('images/hkm_logo.jpg')) ?>" id="school_logo"/>

    </div>

</header>

<nav>

    <div id="toggleDiv"><a href="<?php echo Utility::escape($url->to('index.php',4)) ?>"><i class="fa fa-home"></i></a><span id="toggleBar"><i class="fa fa-bars" aria-hidden="true"></i></span><span id="toggleBarLg"><i class="fa fa-bars" aria-hidden="true"></i></span></div>

    <div id="toggleBody">

    <?php

    if(in_array($rank, $canCreateExam)){?>

    <?php    

        if(!in_array($rank, [7,15])){?>

        <div>

            <a href="new_exam.php" <?php echo isActive('new_exam.php') ?>>Initiate New Exam</a>

            <a href="unpublished_exams.php" <?php echo isActive('unpublished_exams.php') ?>>Unpublished Exams</a>

            <a href="published_exams.php" <?php echo isActive('published_exams.php') ?>>Published Exams</a>


        </div>

        <?php



        }else{?>



            <div>

                <p>As an Examiner</p>

                <a href="new_exam.php" <?php echo isActive('new_exam.php') ?>>Initiate New Exam</a>

                <a href="unpublished_exams.php" <?php echo isActive('unpublished_exams.php') ?>>Unpublished Exams</a>

                <a href="published_exams.php" <?php echo isActive('published_exams.php') ?>>Published Exams</a>

            </div>

            <div>

                <p>As an Examinee</p>

                <a href="take_exams.php" <?php echo isActive('take_exams.php') ?>>Take Exam</a>

                <a href="completed_exams.php" <?php echo isActive('completed_exams.php') ?>>Completed Exams</a>


            </div>



            <?php

        }

    }else{

        ?>

        <div>

            <a href="take_exams.php">Take Exam</a>

            <a href="completed_exams.php">Completed Exams</a>


        </div>

        <?php

    } ?> 

     </div> 

     <div id="schoolPortal">

        <a href="<?php echo $url->to('dashboard.php', 0) ?>">School Portal</a>

    </div>

</nav>



<script>

    window.addEventListener('load',function(){

        let script = document.createElement('script');

        script.src = 'scripts/nav.inc.js';

        document.body.appendChild(script);

    });

</script>