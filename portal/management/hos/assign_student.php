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
    $db = DB::get_instance();
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
    
    if(Input::submitted() && Token::check(Input::get('token'))){
        $submitType = Input::get('submittype');
        $classId = (int) Input::get('classid');
        $msg = '';
        $val = new Validation();
        $values = [
            'classid'=>[
                'name'=>'Class',
                'required'=>true,
                'exists'=>'id/class'
            ]
        ];
        if(!$val->check($values)){
            $errors = $val->errors();
             foreach ($errors as $error){
                 $msg.= $error.'<br>';
             }
             $msg ='<div class="failure">'.$msg.'</div>';
         }else{

            if($submitType == 'assign'){
                $stdIdsString = Input::get('studentids');
                $sqlStdString = "'".str_replace(",", "','", $stdIdsString)."'";
                $db->query('update student3 inner join student on student3.std_id = student.std_id set student3.class_id = ?, student.class_id=? where student3.std_id in('.$sqlStdString.')',[$classId,$classId]);
                //populate student_psy table
                if($hos->populateStdPsy($classId,$sqlStdString)){
                    //notify the students
                    $cD = $hos->getClassDetail($classId);
                    $level = (int)$cD->level;
                    $teaId = Utility::escape($cD->teacher_id);
                    if(!empty($teaId)){ //this ensures that the class already has a Form Teacher so as to avoid error
                        $teaDetail = $hos->getTeacherDetail($teaId);
                        $teaName = Utility::escape($teaDetail->title).'. '.Utility::formatName(Utility::escape($teaDetail->fname), Utility::escape($teaDetail->oname), Utility::escape($teaDetail->lname));
                        $notMsg = '<p>You have been Admitted into '.School::getLevelName(Utility::escape($cD->sch_abbr), $level).' '.Utility::escape($cD->class).
                            '.</p><p>Your Form Teacher is '.$teaName.'. <a href="'.$url->to('profile.php?id='.$teaId,2).'">View Form Teacher\'s Profile</a></p>';
                    }else{
                            $notMsg = '<p>You have been Admitted into '.School::getLevelName(Utility::escape($cD->sch_abbr), $level).' '.Utility::escape($cD->class).'.</p>';
                    }
                    
                    $alert = new Alert(true);
                    if($rank == 5){
                        $alert->sendToRank(9, "Class Assignment", $notMsg, "std_id in(".$sqlStdString.")",false);
                    }

                    if($rank == 17){ //when mudir are the HOS
                        $alert->sendToRank(10, "Class Assignment", $notMsg, "std_id in(".$sqlStdString.")",false);
                    }
                    $msg ='<div class="success">Assignment was successful</div>';
                    Session::set_flash('message',$msg);
                }else{
                    $msg.='<div class="failure">Assignment not successful, something went wrong</div>';
                }

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
    <title>Assign Student To Classes</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/assign_class.css" />
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
        <form method="post" action = "<?php echo Utility::myself() ?>" onsubmit="return false;" id="form">
            <div class="formhead">Assign student to classes</div> 
            
                 <label for="classid">Select Class</label>
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
                 <div><?php echo $msg; ?></div>
                 <div id="studentsDiv">
                     <?php 
                        //get the class level
                        $db->query('select level from class where id = ?',[$initClassId]);
                        if($db->row_count() > 0){
                            $level = $db->one_result()->level;
                            //select all students that have same level and do not have a class
                            $stdNeedClass = $hos->getStudentsNeedsClass($sch_abbr,$level);
                        }
                        if(!empty($stdNeedClass)){
                            echo '<div>Students qualified for the selected class</div>';
                            echo '<div><label for="checkall">Check All</label><input type="checkbox" id="checkall" onclick="checkAll(this)" checked /></div>';
                            echo '<table><thead><td>Student ID</td><td>Name</td><td>Check</td></thead><tbody>';
                            $counter = 0;
                            foreach($stdNeedClass as $std){
                                $counter++;
                                echo '<tr><td>'.Utility::escape($std->std_id).'</td><td>'.Utility::formatName(Utility::escape($std->fname), Utility::escape($std->oname), Utility::escape($std->lname)).'</td><td><input type="checkbox" value="'.$std->std_id.'" id="chk'.$counter.'" checked /></td></tr>';
                            }
                            echo '</tbody></table>';
                            echo '<input type="hidden" id="counter" value="'.$counter.'" />';
                            echo '<div><button onclick="confirmSubmission()" name="assign">Assign</button></div>';
                        }else{
                            echo '<div class="message">No Student found for Assignment</div>';
                        } 
                        
                     ?>
                 </div>
            </div>
            <input type = "hidden" name="studentids" id="studentIds" /> 
            <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />  
            <input type = "hidden" name="submittype" id="submittype" /> 
        </form>
    </main>
    <script>
        window.onload = function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript("scripts/assign_student.js");
        }

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>