<?php
	//initializations
	spl_autoload_register(
		function($class){
			require_once'../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializations
	$url = new Url();
	$adm = new Admission();
        if(!$adm->isRemembered()){
            if(Input::submitted() && Token::check(Input::get('token'))){
            $val = new Validation(true);
			$form_values = array(
				'username'=>array(
					'name' => 'Username',
					'required'=>true,
					'pattern'=>'^[a-zA-Z`][a-zA-Z0-9`]+$'
				),
				'password' => array(
					'name'=>'Password',
					'required' => true,
					'pattern'=>'^[a-zA-z0-9]+$'
				)
			);

				if($val->check($form_values)){
					$remember = (Input::get('remember') === 'on') ? true: false;
					if($adm->pass_match(Input::get('username'), Input::get('password'))){
						if($adm->login($remember)){
							Session::set_flash('welcome','');
							if(Session::lastPageExists()){
								Redirect::to(Session::getLastPage());
							}
							Redirect::to('index.php');
						}
					}else{
						echo 'Wrong username and password combination';
					}			
				}else{
					foreach($val->errors() as $err){
						echo $err .'<br>';
					}
				}
            }
			
        }else{
			if(Session::lastPageExists()){
				Redirect::to(Session::getLastPage());
			}
            Redirect::to('index.php');
        }
		
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/login.css',1))?>" />
</head>

<body>
	<main>
            <form method ="POST" action = "<?php echo Utility::myself() ?>" id="wrapper" onsubmit="return submitForm();">
                    <?php echo Session::get_flash('resetSuccess')?>
                    <div class="formhead">Admission Login</div>
                    <div>
                        <label for="username">Username</label>
                        <div>
                            <input type = "text" name = "username" id ="username"/>
                            <div id="unameMsg"></div>
                        </div>
                    </div>
                    <div>
                         <label for="password">Password</label>
                         <div>
                            <input type ="password" name = "password" id = "password"/>
                            <div id="pwdMsg"></div>
                         </div>
                    </div>
                    <div>
                        <label for = "remember">
                            <input type = "checkbox" name = "remember" id = "remember" /> Remember me
                        </label>

                    </div>
                    <div>
                        <input type = "hidden" value = "<?php echo Token::generate() ?>" name = "token" />
                        <input type="submit" value = "Login">
                    </div>
                    <p><a href = "register.php">Register</a> <a href="recover_password.php">Recover Password</a></p>
            </form>
	</main>
	<script>
        window.onload = function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
            appendScript("scripts/login.js");
        }

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>