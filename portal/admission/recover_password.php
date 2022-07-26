<?php
	 //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
        require_once "../../libraries/vendor/autoload.php";

        $url = new Url();
        $msg = '';
        if(Input::submitted() && Token::check(Input::get('token'))){
           $email = Utility::escape(Input::get('email')); //username 
           $db = DB::get_instance();
           $table = Config::get('users/table_name3');
           $db->query('select count(email) as counter from '.$table.' where email=?',[$email]);
           if($db->one_result()->counter > 0){
                //generate the tokens
                $selector = Token::create(8);
                $resetToken = Token::create();
                $expiry = date('U') + 1200; //token to expire after 20 minutes
                $db->query('delete from password_reset where email = ?',[$email]);//delete any previous token
                $link = $url->to('reset_password.php',5).'?selector='.$selector.'&resetToken='.$resetToken;
                $message = '<p>We received a password reset request. The link to reset your password is shown below, '
                        . 'you can either click it or copy and paste in a browser to reset your password. If you did'
                        . ' not make this request, <span style="color: blue">you can ignore this email</span></p>'
                        . '<p>Reset Link: <a href="'.$link.'">'.$link.'</a></p>'; //email body
                $db->query('insert into password_reset(email,selector,reset_token,expiry) values(?,?,?,?)',[$email,$selector,$resetToken,$expiry]);
                $mail = new Email();
                if($mail->send($email, 'Reset Password Link', $message)){
                    Session::set_flash('recoverPassword','<div class="success">A reset link has been successfully sent to your email</div>');
                    Redirect::home('success.php?recoverPassword=true',5);
                }else{
                    $msg.='<div class="failure">Something went wrong sending the mail</div>'; //output message
                }
                
           }else{
               $msg.='<div class="failure">Email not found</div>'; //output message
           }
          
        }
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Recover Password</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/recover_password.css" />
</head>
<body>
    <main>
        <form method="POST" action="<?php echo Utility::myself();?>">
            <div class="formhead">Recover Your Password</div>
            <div>
                <input type="email" value="<?php Utility::escape(Input::get('email')) ?>" name="email" placeholder="Enter Email Here"/>
            </div>
            <div><?php echo $msg;?></div>
            <div>
                <input type = "hidden" value = "<?php echo Token::generate() ?>" name = "token" />
                <button type="submit">Send Reset Link</button>
            </div>
        </form>
    </main>
</body>
</html>