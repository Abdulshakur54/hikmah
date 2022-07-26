<?php
    //initializations
	spl_autoload_register(
		function($class){
			require_once'../../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializatons
    $url = new Url();
    $hos = new Ihos();
    if(!$hos->isRemembered()){ //runs for people that are not logged in and automatically log in those that have cookie
        Session::setLastPage($url->getCurrentPage());
        Redirect::home('login.php',1);
    }
    $data = $hos->data();
    $id_col = $hos->getIdColumn();
    $user_col = $hos->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $hos->getRank(); 
    if($rank!==17){
        exit(); // exits the page if the user is not the director
    }


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
    <?php 
        require_once '../nav.inc.php';
        echo Session::get_flash('change_pwd');
        if(Session::exists('welcome')){
            echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$hos->getPosition($rank).'</div>';
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
</body>
</html>