<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once './hrm.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>HomePage</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',1))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/index.css',1))?>" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
            echo Session::get_flash('change_pwd');
            if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$hrm->getPosition($rank,$data->asst).'</div>';
                Session::delete('welcome');
            }
            echo Session::get_flash('update');
        ?>
        <div id="navDiv">
            <a href="<?php echo $url->to('index.php',1) ?>">Home</a>
            <a href="<?php echo $url->to('profile.php?id='.$id,1) ?>">View Profile</a>
            <a href="<?php echo $url->to('update.php',1) ?>">Update Details</a>
            <a href="<?php echo $url->to('changepassword.php',1) ?>">Change Password</a>
            <a href="<?php echo $url->to('logout.php',1) ?>">Logout</a>
        </div>
    </main>
</body>
</html>