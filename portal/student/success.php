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
                    if(Input::submitted('get')){
                        //this handles the different scenarios of redirects to the success page
                        if(Input::get('appliedAdmission')=='true'){
                            echo '<div class="success">'.Session::get_flash('admissionApplication').'</div>';
                            exit();//this page is exited here so as to stop script execution
                        }

                        if(Input::get('recoverPassword')=='true'){
                            echo '<div class="success">'.Session::get_flash('recoverPassword').'</div>';
                            exit();//this page is exited here so as to stop script execution
                        }

                         if(Input::get('resetSuccess')=='true'){
                            echo '<div class="success">'.Session::get_flash('resetSuccess').'</div>';
                            echo '<div><a href="login.php">Click to Login</a></div>';
                            exit();//this page is exited here so as to stop script execution
                        }

                    }
                   echo '<div class="success">'.Session::get_flash('new_user').'</div>';
                   echo 'Login <a href="'.$url->to('login.php',3).'">Here</a>'; ?>
            </main>
        </body>
    </html>

   