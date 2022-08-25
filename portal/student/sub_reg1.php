<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializations
    require_once 'nav1.inc.php';
    $msg = '';
    $regSubArr=$minNoSub = null; 
     if(Input::submitted() && Token::check(Input::get('token'))){
         $submitType = Input::get('submittype');
         if($submitType == 'register'){
             $subIds = explode(',', Utility::escape(Input::get('subjectids')));
             $std->instantUtil();
             $std->registerSubjects($username,$subIds,$classId,$sch_abbr);
             $minNoSub = $std->getMinNoSub($classId);
             $regSubArr = $std->getRegisteredSubjectsId($username);
             if(($minNoSub <= count($regSubArr)) && !$data->sub_reg_comp){
                 //update table indicating that the minimum no of subjects has been registered
                 $std->updateCompSubReg($username);
             }
             $msg = '<div class="success">Registration was successful</div>';
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
        <title>Register Subject</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
        <link rel="stylesheet" type="text/css" href="styles/sub_reg.css" />
    </head>
    <body>
        <main>
            <?php require_once './nav.inc.php'; ?>
            <?php echo $msg; ?>
            <?php
                if(!empty($classId)){
                    if(empty($regSubArr)){
                        $regSubArr = $std->getRegisteredSubjectsId($username); //to avoid requery of the database for a data that is already available, especially when the user submits
                    }

                    $regListArr = $std->getRegistrationList($classId);
                    if(empty($minNoSub)){
                        $minNoSub = $std->getMinNoSub($classId);  //to avoid requery of the database for a data that is already available, especially when the user submits
                    }    
                    
                    $noOfRegSub = count($regSubArr);
                    $noOfSubToReg = $minNoSub - $noOfRegSub;
                    $regSubsIdArray = array_keys($regSubArr);
                    if(!empty($regSubArr)){
                        echo'<div class="registeredSub"><h3>Already Registered Subject</h3><p><em>'.implode(', ', array_values($regSubArr)).'</em></p></div>';
                    }
                    ?>
                <form method="post" action = "<?php echo Utility::myself() ?>" onsubmit="return false;" id="form" />
                    <div class="formhead">Available Subjects For Registration</div> 
                    <?php
                    if(count($regListArr) > 0){
                        if($noOfSubToReg > 0){
                            echo '<div>Register at least '.$noOfSubToReg.' subjects</div>';
                        }

                        echo '<div><label for="checkall">Check All</label><input type="checkbox" id="checkall" onclick="checkAll(this)" /></div>';
                        echo '<table><thead><th>Subject Name</th><th>Check</th></thead><tbody>';
                        $counter = 0;
                        foreach($regListArr as $subId=>$subName){
                            if(!in_array($subId, $regSubsIdArray)){
                                $counter++;
                                echo '<tr><td>'.$subName.'</td><td><input type="checkbox" id="chk'.$counter.'" value="'.$subId.'"/></td></tr>'; 
                            }

                        }
                        echo '</tbody></table>';
                        echo '<input type="hidden" id="counter" value="'.$counter.'" />';
                 ?>
                        <input type = "hidden" name="subjectids" id="subjectIds" /> 
                        <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />  
                        <input type = "hidden" name="submittype" id="submittype" /> 
                        <button name="register" onclick="confirmSubmission()">Register</button>
                <?php
                    }else{
                        echo '<div class="message">No record found</div>';
                    }
                }else{
                    echo '<div class="message">You can only register subjects after you have been assigned to a class</div>';
                }
            ?>
            </form>
        </main>
        <script>
            window.onload = function(){
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
                appendScript("scripts/sub_reg.js");
            }

            function appendScript(source){
                let script = document.createElement("script");
                script.src=source;
                document.body.appendChild(script);
            }
        </script>
    </body>
</html>
