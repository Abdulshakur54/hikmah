<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once './hos.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Students that haven't complete subject registration</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/std_sub_reg.css" />
</head>
<body>
    <main>
       <?php 
            require_once '../nav.inc.php';
            //echo welcome flash message
            if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$hos->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$hos->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        <?php
            $stds = $hos->getStdsNotCompSubReg($sch_abbr);
            if(!empty($stds)){
                $numRows = count($stds);
                echo'<div class="message">'.$numRows.' records found</div>';
                echo '<table><thead><th>Student ID</th><th>Fullname</th><td>Class</td></thead><tbody>';
                foreach($stds as $std){
                    echo '<tr><td>'.Utility::escape($std->std_id).'</td><td>'.Utility::formatName(Utility::escape($std->fname), Utility::escape($std->oname), Utility::escape($std->lname)).'</td><td>'.School::getLevelName($sch_abbr, (int)$std->level).' '. Utility::escape($std->class).'</td></tr>';
                }
                echo '</tbody></table>';
                echo '<button onclick="alert(\'Students have been persistently notified\')">Notify Students</button>';
            }else{
                echo'<div class="message">No record found</div>';
            }
        ?>
    </main>
</body>
</html>