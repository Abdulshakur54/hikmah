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
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>HomePage</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',3))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/index.css',3))?>" />
</head>
<body>
    <?php
        require_once 'nav.inc.php';
        echo '<div class="success">'.Session::get_flash('change_pwd').'</div>';
        if(Session::exists('welcome')){
            echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$data->fname.'</div>';
            Session::delete('welcome');
        }
        echo '<div class="success">'.Session::get_flash('update').'</div>';
     ?>
   
    <div id="navDiv">
        <a href="<?php echo $url->to('index.php',3) ?>">Home</a>
        <a href="<?php echo $url->to('profile.php?id='.$id,3) ?>">View Profile</a>
        <a href="<?php echo $url->to('update.php',3) ?>">Update Details</a>
        <a href="<?php echo $url->to('changepassword.php',3) ?>">Change Password</a>
        <a href="<?php echo $url->to('logout.php',3) ?>">Logout</a>
    </div>
</body>
</html>