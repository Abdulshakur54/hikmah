<?php 
     //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once 'apm.inc.php';
?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <main>
            <?php
                require_once 'nav.inc.php';
                $transfer = ['tableName'=>'admission','tableColumn'=>'score','maxScore'=>100,'idColumn'=>'adm_id'];
                Redirect::to($url->to('new_exam.php?transfer='. json_encode($transfer),4));
            ?>
            
        </main>
    </body>
</html>
