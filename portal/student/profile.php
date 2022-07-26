<?php
        //initializations
	spl_autoload_register(
		function($class){
			require_once'../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
        //end of initializations
	$std = new Student();
        if(Input::submitted('get') && $std->find(Utility::escape(Input::get('id')))){
            $profileData = $std->data();
            require_once '../profile.inc.php';
        }else{
            Redirect::to(404);
        }

?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Profile</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',3))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/profile.css',3))?>" />
</head>
<body>
    <main>
        <?php require_once '../profile.nav.inc.php'?>
        <div id="wrapper">
            <div>
                <span class="prop">First Name:</span>
                <span class="val"><?php echo Utility::escape($profileData->fname); ?></span>
            </div>
            <div>
                <span class="prop">Last Name:</span>
                <span class="val"><?php echo Utility::escape($profileData->lname); ?></span>
            </div>
            <div>
                <span class="prop">Other Name:</span>
                <span class="val"><?php echo Utility::escape($profileData->oname); ?></span>
            </div>
            <div>
                <span class="prop">Position:</span>
                <span class="val"><?php echo $std->getPosition(Utility::escape($profileData->rank)); ?></span>
            </div>
        </div>
    </main>
</body>
</html>