<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once './hos.inc.php';
    $initClassId = null;
    $msg='';
    
    function selectedClass($id){
        if(Input::submitted()){
            global $classId;
            if($classId == $id){
                global $initClassId;
                $initClassId = $id; //this will help to dynamically load the Transfer to select box
                return 'selected';
            }
            return '';
        }
    }
    
    function selectedClassTo($id){
        if(Input::submitted()){
            global $classIdTo;
            if($classIdTo == $id){
                return 'selected';
            }
            return '';
        }
    }
    
    if(Input::submitted() && Token::check(Input::get('token'))){
        $classId = (int) Input::get('classid');
        $classIdTo = (int) Input::get('classidto');
        $submitType = Input::get('submittype');
        $msg = '';
        $db = DB::get_instance();
        if($submitType == 'transfer'){
                $stdIdsString = Input::get('studentids');
                $sqlStdString = "'".str_replace(",", "','", $stdIdsString)."'";
                $classIdTo = (int) Input::get('classidto');
                $cD = $hos->getClassDetail($classId);
                $cDTo = $hos->getClassDetail($classIdTo);
               
                //update class_id for student and student3 table
                $db->query('update student3 inner join student on student3.std_id = student.std_id set student3.class_id = ?, student.class_id=? where student3.std_id in('.$sqlStdString.')',[$classIdTo,$classIdTo]);
                //update score table
                $utils = new Utils();
                $formSession = $utils->getFormatedSession($sch_abbr);
                $db->query('update '.$formSession.'_score set class_id =? where std_id in('.$sqlStdString.')',[$classIdTo]);
                if($hos->populateStdPsy($classIdTo,$sqlStdString)){
                     //notify the students
                    $level = (int)$cDTo->level;
                    $teaId = Utility::escape($cDTo->teacher_id);
                    if(!empty($teaId)){ //this ensures that the class already has a Form Teacher so as to avoid error
                        $teaDetail = $hos->getTeacherDetail($teaId);
                        $teaName = Utility::escape($teaDetail->title).'. '.Utility::formatName(Utility::escape($teaDetail->fname), Utility::escape($teaDetail->oname), Utility::escape($teaDetail->lname));
                        $notMsg = '<p>You have been transfered to '.School::getLevelName(Utility::escape($cD->sch_abbr), $level).' '.Utility::escape($cDTo->class).
                            '.</p><p>Your Form Teacher is now '.$teaName.'. <a href="'.$url->to('profile.php?staffId='.$teaId,2).'">View Form Teacher\'s Profile</a></p>';
                    }else{
                            $notMsg = '<p>You have been transferred to '.School::getLevelName(Utility::escape($cDTo->sch_abbr), $level).' '.Utility::escape($cDTo->class).'.</p>';
                    }
                    
                    //ensures the student have registered the necessary subjects for their class
                    $minNoSub = (int)$cDTo->nos;
                    $stdIds = explode(',', $stdIdsString);
                    $db2 = DB::get_instance2();
                    $db->query('select subject_ids,std_id from student3 where std_id in('.$sqlStdString.')');
                    $res = $db->get_result();
                    $stdIdsWithMinNoSub = [];
                    foreach($res as $r){
                        if(count(json_decode($r->subject_ids,true)) < $minNoSub){
                            $stdIdsWithMinNoSub[] = $r->std_id;
                        }
                    }      
                    //update student table for those that need to complete subject registration
                    $stdIdsWithMinNoSubString = "'".implode("','", $stdIdsWithMinNoSub)."'"; //formatting so it can be used in query
                    $db->query('update student set sub_reg_comp = false where id in('.$stdIdsWithMinNoSubString.')');
                    //notify all students that have been transferred
                    $alert = new Alert(true);
                    if($rank == 5){
                        $alert->sendToRank(9, "Class Transfer", $notMsg, "std_id in(".$sqlStdString.")",false);
                    }

                    if($rank == 17){ //when mudir are the HOS
                        $alert->sendToRank(10, "Class Transfer", $notMsg, "std_id in(".$sqlStdString.")",false);
                    }
                    $msg ='<div class="success">Transfer was successful</div>';
                    Session::set_flash('message',$msg);
                }else{
                    $msg.='<div class="failure">Transfer not unsuccessful, something went wrong</div>';
                }
            }

        Session::set_flash('message',$msg);
    }

    

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Transfer Students to Another Class</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/transfer_student.css" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
            
            //echo welcome flash message
            if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$hos->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$hos->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        <form method="post" action = "<?php echo Utility::myself() ?>" onsubmit="return false;" id="form" />
            <div class="formhead">Transfer Students to Another Class</div> 
            <div>
                <label for="classid">From</label>
                <div>
                   <select name="classid" onchange="submitForm()">
                       <?php

                           $availableClass  = $hos->getClasses($sch_abbr);
                           if(!empty($availableClass)){
                               $initClassId = ($availableClass[0])->id;
                               foreach ($availableClass as $avaClass){
                                   $cId = (int)$avaClass->id;
                                   echo '<option value="'.$cId.'" '.selectedClass($cId).'>'.School::getLevelName(Utility::escape($sch_abbr), Utility::escape($avaClass->level)).' '. Utility::escape($avaClass->class).'</option>';
                               }
                           }
                       ?>
                   </select>
                </div>
            </div>
            <div>
                <label for="classidto">To</label>
                <div>
                    <select name="classidto">
                       <?php

                           $classSameLevel = $hos->getClassSameLevel($initClassId);
                           if(!empty($classSameLevel)){
                               foreach ($classSameLevel as $cls){
                                   $cId = (int)$cls->id;
                                   echo '<option value="'.$cId.'" '.selectedClassTo($cId).'>'.School::getLevelName(Utility::escape($sch_abbr), Utility::escape($cls->level)).' '. Utility::escape($cls->class).'</option>';
                               }
                           }
                       ?>
                   </select>
                   <div><?php echo Session::get_flash('message')?></div>
                </div>
            </div>
            
            <div id="studentsDiv">
                <?php 
                $hos2 = new Hos2();
                $transStudent = $hos2->getStudents($initClassId);
                if(!empty($transStudent)){
                   echo '<div><label for="checkall">Check All</label><input type="checkbox" id="checkall" onclick="checkAll(this)" /></div>';
                   echo '<table><thead><th>Student Id</th><th>Fullname</th><th>Check</th></thead><tbody>';
                   $counter = 0;
                   foreach($transStudent as $transStd){
                       $counter++;
                       $stdId = Utility::escape($transStd->std_id);
                       echo '<tr><td>'.$stdId.'</td><td>'.Utility::formatName(Utility::escape($transStd->fname), Utility::escape($transStd->oname), Utility::escape($transStd->lname)).'</td><td><input type="checkbox" id="chk'.$counter.'" value="'.$stdId.'"/></td></tr>';  
                   }
                   echo '</tbody></table>';
                    echo '<input type="hidden" id="counter" value="'.$counter.'" />';
                }else{
                    echo '<div class="message">No record found</div>';
                }
                ?>
            </div>
          
            <input type = "hidden" name="studentids" id="studentIds" /> 
            <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />  
            <input type = "hidden" name="submittype" id="submittype" /> 
            <?php 
                if(!empty($classSameLevel)){ //only show the transer button when there are students to transfer
                    echo '<button name="transfer" onclick="confirmSubmission()">Transfer</button>';
                }
            ?>
            
        </form>
        
    </main>
    <script>
        window.onload = function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript("scripts/transfer_student.js");
        }

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>