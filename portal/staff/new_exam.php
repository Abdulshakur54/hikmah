<?php 
     //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once 'nav1.inc.php';
    require_once 'sub_teacher.inc.php';
    $msg = '';
    if(Input::submitted() && Token::check(Input::get('token'))){ //page has been submitted via post method
        $utils = new Utils();
        $table = $utils->getFormatedSession($sch_abbr).'_score';
        $column = Utility::escape(Input::get('dtn'));
        $maxScore = Utility::escape(Input::get('maxscore'));
        switch($currTerm){
            case 'ft':
                $tableColumn = 'ft_'.$column;
                break;
            case 'st':
                $tableColumn = 'st_'.$column;
                break;
            case 'tt':
                $tableColumn = 'tt_'.$column;
                break;
        }
        $transfer = ['tableName'=>$table,'tableColumn'=>$tableColumn,'maxScore'=>$maxScore,'idColumn'=>'std_id','subid'=>$subId];
        Redirect::to($url->to('new_exam.php?transfer='. json_encode($transfer),4));
    }
?>
<!doctype html>
<html>
    <head>
        <meta name="HandheldFriendly" content="True">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Initiate New Exam</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
        <link rel="stylesheet" type="text/css" href="styles/new_exam.css" />
    </head>
    <body>
        <?php require_once 'nav.inc.php';?>
        <main>
            <?php
                $scoreSettings = $staff->getScoreSettings($sch_abbr);
                
            ?>
            <form method="post" action="<?php echo Utility::myself()?>" id="form" onsubmit="return false;">
                <label for="dtn">Select Category</label>
                <select name="dtn" id="dtn">
                    <?php 
                        if($scoreSettings['fa'] > 0){
                            echo '<option value="fa">First Assignment ('.$scoreSettings['fa'].')</option>';
                        }
                        if($scoreSettings['sa'] > 0){
                            echo '<option value="sa">Second Assignment ('.$scoreSettings['sa'].')</option>';
                        }
                        if($scoreSettings['ft'] > 0){
                            echo '<option value="ft">First Test ('.$scoreSettings['ft'].')</option>';
                        }
                        if($scoreSettings['st'] > 0){
                            echo '<option value="st">Second Test ('.$scoreSettings['st'].')</option>';
                        }
                        if($scoreSettings['pro'] > 0){
                            echo '<option value="pro">Project ('.$scoreSettings['pro'].')</option>';
                        }
                        if($scoreSettings['exam'] > 0){
                            echo '<option value="exam">Exam ('.$scoreSettings['exam'].')</option>';
                        }
                    
                    ?>
                </select>
                <!-- this is needed to pass values from PHP to javascript -->
                <div id="scores" class="none">  
                    <?php echo json_encode($scoreSettings)?>
                </div>
                <input type="hidden" name="token" id="token" value="<?php echo Token::generate()?>"/>
                <input type="hidden" name="maxscore" id="maxscore" />
                <input type="hidden" name="subid" id="subid" value="<?php echo $subId?>"/>
                <div id="msg"><?php echo $msg;?></div>
                <button id="continue" onclick="submitForm()">Continue</button>
            </form>
        </main>
        <script>
            window.addEventListener('load',function(){
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
                appendScript('scripts/new_exam.js');
            });
            function appendScript(source){
                let script = document.createElement('script');
                script.src=source;
                document.body.appendChild(script);
            }
            
        </script>
    </body>
</html>
