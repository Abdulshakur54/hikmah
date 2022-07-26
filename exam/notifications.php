<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    $alert = new ExamAlert();
     $url = new Url();
    $mgt = new Management();
    $staff = new Staff();
    $std = new Student();
    $adm = new Admission();
    $user = null;
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
    if(!isset($user)){ //ensure user is legally logged in
            Redirect::to('index.php'); //redirect to exam index
    }  
    
    $data = $user->data();
    $id_col = $user->getIdColumn();
    $user_col = $user->getUsernameColumn();
    $id = $data->$id_col;
    $username = $data->$user_col;
    $rank = $user->getRank(); 
   
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
        <?php require_once 'header.inc.php'?>
        <?php 
            require_once 'nav.inc.php';
            //echo welcome flash message
            if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$user->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$user->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        
        <?php
            $alerts = $alert->getMyAlerts($username);
            if(!empty($alerts)){
                foreach ($alerts as $alertt){
                    echo'<div>
                            <div class="title">'.$alertt->title.'</div>
                            <div class="message">'.$alertt->message.'</div>
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