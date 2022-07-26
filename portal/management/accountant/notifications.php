<?php
     //initializations
	spl_autoload_register(
		function($class){
			require_once'../../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializatons
    require_once './accountant.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notifications</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/notifications.css" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
            //echo welcome flash message
            if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$acct->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$acct->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        
        <?php
            $alerts = $alert->getMyAlerts($username);
            if(!empty($alerts)){
                foreach ($alerts as $alertt){
                    echo'<div>
                            <div class="alertTitle">'.$alertt->title.'</div>
                            <div class="alertMessage">'.$alertt->message.'</div>
                         </div>';
                }
                $alert->seen($username);
            }else{
                echo '<div class="message">No notifications available</div>';
            }
        ?>
        
    </main>
</body>
</html>