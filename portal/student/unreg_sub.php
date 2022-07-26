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
     if(Input::submitted() && Token::check(Input::get('token'))){
         $submitType = Input::get('submittype');
         if($submitType == 'deregister'){
             $subIds = explode(',', Utility::escape(Input::get('subjectids')));
             $req2 = new Request2();
             $classTeacherId = $std->getClassTeacherId($classId); //get class teacher id
             if(!empty($classTeacherId)){
                $subjs = $std->getSubjectNames($subIds);
                $subjects = implode(', ',$subjs);
                $request = Utility::formatName(Utility::escape($data->fname), Utility::escape($data->oname), Utility::escape($data->lname)).' with Registration No: '.$username.
                        ' needs your approval to deregister the following subjects:<br>'.$subjects;
                $req2->send($username, $classTeacherId, $request, 1, json_encode($subjs));
                $msg.='<div class="success">The operation was successful but you would need approval from your Class Teacher to derigester the selected courses<br>A request has been sent to your Class Teacher for approval</div>';
             }else{
                 $msg.='<div class="failure>Sorry, you cannot proceed, you don\'t have have a Class Teacher to approve you request</div>';
             }
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
        <title>Deregister Subject</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
        <link rel="stylesheet" type="text/css" href="styles/unreg_sub.css" />
    </head>
    <body>
        <main>
            <?php require_once './nav.inc.php'; ?>
            <?php echo $msg; ?>
            <?php
               
                $regSubArr = $std->getRegisteredSubjectsId($username);
      
                $minNoSub = $std->getMinNoSub($classId);     
                $regSubsIdArray = array_keys($regSubArr);
                ?>
                <form method="post" action = "<?php echo Utility::myself() ?>" onsubmit="return false;" id="form" />
                <div class="formhead">My Registered Subjects</div> 
                <?php
                if(!empty($regSubsIdArray)){
                 
                    echo '<div><label for="checkall">Check All</label><input type="checkbox" id="checkall" onclick="checkAll(this)" /></div>';
                    echo '<table><thead><th>Subject Name</th><th>Check</th></thead><tbody>';
                    $counter = 0;
                    foreach($regSubArr as $subId=>$subName){
                        $counter++;
                        echo '<tr><td>'.$subName.'</td><td><input type="checkbox" id="chk'.$counter.'" value="'.$subId.'"/></td></tr>'; 
                        
                    }
                    echo '</tbody></table>';
                    echo '<input type="hidden" id="counter" value="'.$counter.'" />';
             ?>
                    <input type = "hidden" name="subjectids" id="subjectIds" /> 
                    <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />  
                    <input type = "hidden" name="submittype" id="submittype" /> 
                    <button name="deregister" onclick="confirmSubmission()">Deregister</button>
            <?php
                }else{
                    echo '<div class="message">No record found</div>';
                }
            ?>
            </form>
        </main>
        <script>
            window.onload = function(){
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
                appendScript("scripts/unreg_sub.js");
            }

            function appendScript(source){
                let script = document.createElement("script");
                script.src=source;
                document.body.appendChild(script);
            }
        </script>
    </body>
</html>
