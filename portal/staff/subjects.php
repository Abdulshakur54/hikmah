<?php
     //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once './nav1.inc.php';
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>My Subjects</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',2))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/subjects.css',2))?>" />
</head>

<body>
    <main>
        <?php
             require_once './nav.inc.php';
             if(Session::exists('welcome')){
                echo 'Good '.ucfirst(Utility::getPeriod()).', '.$staff->getPosition($rank);
                Session::delete('welcome');
            }

        ?>
        <?php
        $subjects = $staff->getSubjectsWithIds($username);
        if(!empty($subjects)){
        ?>
        <table>
            <thead><tr><th>Subject</th><th>Class</th><th>e-exam</th></tr></thead>
            <tbody>
                <?php 
                    foreach($subjects as $sub){
                        echo '<tr><td>'.$sub->subject.'</td><td>'.School::getLevName($sch_abbr, $sub->level).' '.$sub->class.'</td><td><a href="new_exam.php?subid='.$sub->id.'" />e-exam</a></td></tr>';
                    }
                ?>
            </tbody>
        </table>
        <?php
        }else{
            echo'<div class="message">No Subject found</div>';
        }
        ?>
    </main>

</body>
</html>