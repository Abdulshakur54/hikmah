<?php
    //initializations
	spl_autoload_register(
		function($class){
			require_once'../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
    //end of initializatons
 ?>
    <!doctype html>
    <html>
        <head>
            <style>
                .success{
                    color: rgb(0,200,0);
                    font-family: Verdana, Geneva, Tahoma, sans-serif;
                }
            </style>
        </head>
        <body>
            <main>
                <?php
                 if(Session::exists('new_user')){
                    echo '<div class="success">'.Session::get_flash('new_user').'</div>';
                    echo 'Login <a href="'.$url->to('login.php',2).'">Here</a>';
                 }     
                ?>
            </main>
        </body>
    </html>
