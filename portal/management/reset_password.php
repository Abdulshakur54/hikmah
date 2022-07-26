<?php
    //initializations
	spl_autoload_register(
		function($class){
			require_once'../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializatons
        $url = new Url();
        $msg = '';
        if(Input::submitted('get')){
            if(empty(Input::get('selector')) || empty(Input::get('resetToken'))){
                Redirect::to(404);
            }
        }
        if(Input::submitted() && Token::check(Input::get('token'))){
           if(empty(Input::get('selector')) || empty(Input::get('resetToken'))){
                Redirect::to(404);
           }
           $values = [
                'pwd'=>[
                    'name'=>'Password',
                    'required'=>true,
                    'min'=>6,
                    'max'=>12,
                    'pattern'=>'^[a-zA-Z0-9]+$'
                ],
                'c_pwd'=>[
                    'name'=>'Confirm Password',
                    'required'=>true,
                    'same'=>'pwd'
                ]
           ];
           $val = new Validation();
           if($val->check($values)){
               $selector = Utility::escape(Input::get('selector'));
               $resetToken = Utility::escape(Input::get('resetToken'));
               $now = new Date('U');
               //check if selector token exists and has not expired
               $db = DB::get_instance();
               $db->query('select selector,email from password_reset where selector=? and reset_token=?',[$selector,$resetToken]);
               if($db->row_count() > 0){
                   $email = $db->one_result()->email; //store email
                   //delete token
                   $db->query('delete from password_reset where selector=? and reset_token=?',[$selector,$resetToken]);
                   //change the password of the user
                   $table = Config::get('users/table_name0');
                   $pwd_col = 'password';
                   $pwd = Utility::escape(Input::get('pwd'));
                   $newPwd = password_hash($pwd, PASSWORD_DEFAULT);
                   $db->query('update '.$table.' set '.$pwd_col.'=? where email = ?',[$newPwd,$email]); //update password
                   Session::set('resetSuccess', '<div style="success"><p>You have successfully changed your password<br>'
                           . 'Click <a href="login.php">here</a> to login</p>.</div>');
                   Redirect::to('success.php?resetSuccess=true');
               }
           }else{
                foreach($val->errors() as $err){
                    $msg .= $err. '<br>';
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
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
        <link rel="stylesheet" type="text/css" href="styles/reset_password.css" />
</head>

<body>
	<form method ="POST" action = "<?php echo Utility::myself()?>">
            <div class="formhead">Password Reset</div>
            <div>
                <input type="password" name="pwd" placeholder="Enter New Password"/>
            </div>
            <div>
                <input type="password" name="c_pwd" placeholder="Confirm New Password"/>
            </div>
             <div><?php echo $msg;?></div>
            <div>
                 <input type = "hidden" value = "<?php echo Token::generate() ?>" name = "token" />
                 <input type = "hidden" value = "<?php echo Utility::escape(Input::get('resetToken')) ?>" name = "resetToken" />
                 <input type = "hidden" value = "<?php echo Utility::escape(Input::get('selector')) ?>" name = "selector" />
                <button type="submit">Reset</button>
            </div>
	</form>
</body>
</html>
