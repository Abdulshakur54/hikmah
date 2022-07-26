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
    if(Input::submitted() && Token::check(Input::get('token'))){
        $val = new Validation();
        $values = array(
            'old_pwd'=>array(
                'name'=>'Old Password',
                'required'=>true,
                'min'=>6,
                'max'=>12,
                'pattern'=>'^[a-zA-Z0-9]+$'
            ),
            'new_pwd'=>array(
                'name'=>'New Password',
                'required'=>true,
                'min'=>6,
                'max'=>12,
                'pattern'=>'^[a-zA-Z0-9]+$'
            ),
            'c_new_pwd'=>array(
                'name'=>'Confirm New Password',
                'required'=>true,
                'same'=>'new_pwd'
            )
            );
        if($val->check($values)){
           if($adm->conf_pwd(Input::get('old_pwd'))){
               $adm->changePassword(Input::get('new_pwd'));
               Session::set_flash('change_pwd', 'You have Successfuly change Your Password<br>');
               Redirect::to('index.php');
           }else{
               echo 'Old Password not correct';
           }
        }else{
            foreach($val->errors() as $err){
                echo $err. '<br>';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',5))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/changepassword.css',5))?>" />
</head>

<body>
    <?php
         require_once './nav.inc.php';
         if(Session::exists('welcome')){
            echo 'Good '.ucfirst(Utility::getPeriod()).', '.$adm->getPosition($rank);
            Session::delete('welcome');
        }
         
    ?>
    
	<form method ="POST" action = "<?php echo Utility::myself()?>" onsubmit="return submitForm();">
		<div>
			<label for = "old_pwd" >Enter Old Password</label>
			<input type = "password" name = "old_pwd" id ="old_pwd" />
            <div id="oldPwdMsg" class="leftDiv"></div>
		</div>
		<div>
			<label for="new_pwd">Enter New Password</label>
			<input type ="password" name = "new_pwd" id = "new_pwd"/>
            <div id="newPwdMsg" class="leftDiv"></div>
		</div>
		<div>
			<label for="c_new_pwd">Confirm New Password</label>
			<input type ="password" name = "c_new_pwd" id = "c_new_pwd"/>
            <div id="conNewPwdMsg"  class="leftDiv"></div>
		</div>
		<input type = "hidden" value = "<?php echo Token::generate() ?>" name = "token" />
		<input type="submit" value = "Change" id="updBtn">
	</form>
    <script>
        window.onload = function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
            appendScript("scripts/changepassword.js");
        }

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>