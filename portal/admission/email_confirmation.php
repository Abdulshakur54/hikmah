<?php
     //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
	//end of initializatons
        $url = new Url();
        $msg = '';
        if(Input::submitted('get')){
            if(empty(Input::get('selector')) || empty(Input::get('resetToken'))){
                Redirect::to(404);
            }
            $selector = Utility::escape(Input::get('selector'));
               $resetToken = Utility::escape(Input::get('resetToken'));
               $now = date('U');
               //check if selector token exists and has not expired
               $db = DB::get_instance();
               $emailConfirmTable = 'email_confirmation';
               $db->query('select selector,email'.$emailConfirmTable.' where selector=? and reset_token=?',[$selector,$resetToken]);
               if($db->row_count() > 0){
                   $email = $db->one_result()->email;
                   //delete all token associated with the given email. Activate all account associate with the given email
                   $sql1 = 'delete from email_confirmation where email=?';
                   $val1 = [$email];
                   $admTable = Config::get('users/table_name3');
                   $sql2 = 'update '.$admTable.' set email_confirmed=? where email=?';
                   $val2 = [true,$email];
                   $db->trans_query([[$sql1,$val1],[$sql2,$val2]]);
                   Session::set_flash('emailConfirmed', $email.' is successfully activated and confirmed');
                   Redirect::home('success.php?emailConfirmed=true',5);
               }
            
        }
  