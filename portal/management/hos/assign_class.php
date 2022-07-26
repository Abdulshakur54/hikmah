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
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Assign Class</title>
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
            <div class="formhead">Assign Class</div>
             <?php 
                if(Input::submitted() && Token::check(Input::get('token'))){
                    
                    $classId = Utility::escape(Input::get('classid'));
                    $teacherId = Utility::escape(Input::get('teacherid'));
                    $msg = '';
                    $val = new Validation();
                    $values = [
                        'classid'=>[
                            'name'=>'Class',
                            'required'=>true,
                            'exists'=>'id/class'
                        ],
                        'teacherid'=>[
                            'name'=>'Teacher',
                            'required'=>true,
                            'exists'=>'staff_id/staff'
                        ]
                    ];
                    if($val->check($values)){
                        $names = $hos->getStaffNames($teacherId);
                        $fullname = $names->title.'. '.Utility::formatName($names->fname, $names->oname, $names->lname); 
                        $detail = $hos->getClassAndLevel($classId);
                        $level = $detail->level;
                        $class = $detail->class;
                        $submitType = Input::get('submitType');
                        $alert = new Alert(true);
                        $allClassStudents = $hos->getAllClassStudents($classId);
                        if($submitType === 'assign'){
                            if($hos->isClassTeacher($classId,$teacherId)){
                              $msg = '<div class="failure">'.$fullname.' is already the class teacher of '.School::getLevelName($sch_abbr, $level).' '.strtoupper($class).'</div>';
                            }else{
                                if(!$hos->isAClassTeacher($teacherId)){          
                                    //check if class already has a teacher
                                    if(!$hos->classHasATeacher($classId)){
                                        $hos->assignClass($classId,$teacherId);
                                        //notify all the students in that class of the development
                                        $notMsg = '<p>This is to notify that you now have a new Form Teacher '.$fullname.'. <a href="'.$url->to('profile.php?id='.$teacherId,2).'">View Form Teacher\'s Profile</a></p>';
                                        $msg = '<div class="success">'.$fullname.' is now the class teacher of '.School::getLevelName($sch_abbr, $level).' '.strtoupper($class).'</div>';  
                                        if(!empty($allClassStudents)){
                                            foreach($allClassStudents as $std){
                                                $alert->send($std->std_id, 'Form Teacher Introduction', $notMsg, true);
                                            }
                                        }
                                    }else{
                                        $msg = '<div class="failure">'.School::getLevelName($sch_abbr, $level).' '.strtoupper($class).' already has a teacher <br>Unassign the teacher to proceed</div>';
                                    }
                                    
                                }else{
                                     $msg = '<div class="failure">'.$fullname.' is already a class teacher<br>Unassign him from the class to proceed'.'</div>';
                                }
                            }
                        }
                        if($submitType === 'unassign'){
                                 if($hos->isAClassTeacher($teacherId)){
                                    $hos->unAssignClass($teacherId);
                                    //notify all the students in that class of the development
                                    $notMsg = '<p>This is to notify that '.$fullname.' is no longer your Form Teacher</p>';
                                    $msg = '<div class="success">'.$fullname.' is now the class teacher of '.School::getLevelName($sch_abbr, $level).' '.strtoupper($class).'</div>';  
                                    if(!empty($allClassStudents)){
                                        foreach($allClassStudents as $std){
                                            $alert->send($std->std_id, 'Form Teacher Status', $notMsg, true);
                                        }
                                    }
                                    $msg = '<div class="success">Unassignment was successful</div>';
                                }else{
                                     $msg = '<div class="failure">No class is assigned to '.$fullname.'</div>';
                                }
                        }
                        
                        
                    }else{
                        $errors = $val->errors();
                        foreach ($errors as $error){
                            $msg.= $error.'<br>';
                        }
                        $msg ='<div class="failure">'.$msg.'</div>';
                    }
                    
                    
                    Session::set_flash('message',$msg);
                }
            ?>
            
            
            <div><?php echo Session::get_flash('message')?></div>
            <div>
                 <label for="school">Select Teacher</label>
                 <div>
                    <select name="teacherid">
                        <?php
                            $availableTeachers  = $hos->getTeachers($sch_abbr);
                            
                            if(!empty($availableTeachers)){
                                foreach($availableTeachers as $availableTeacher){
                                    $name = $availableTeacher->title.'. '.Utility::formatName($availableTeacher->fname, $availableTeacher->oname, $availableTeacher->lname);
                                    echo '<option value="'.$availableTeacher->staff_id.'">'.$name.' &nbsp;('.$availableTeacher->staff_id.')</option>';
                                }
                                
                            }
                        ?>
                    </select>
                 </div>
            </div>
            
            <div>
                 <label for="school">Select Class</label>
                 <div>
                    <select name="classid">
                        <?php
                            $availableClass  = $hos->getClasses($sch_abbr);
                            if(!empty($availableTeachers) && !empty($availableClass)){
                                foreach ($availableClass as $avaClass){
                                    $cId = (int)$avaClass->id;
                                    echo '<option value="'.$cId.'">'.School::getLevelName(Utility::escape($sch_abbr), Utility::escape($avaClass->level)).' '. Utility::escape($avaClass->class).'</option>';
                                }
                            }
                        ?>
                    </select>
                 </div>
            </div>
            <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
            <input type = "hidden"  name="submitType" id="submitType" />
            <button name="assign" onclick="confirmSubmission('assign')">Assign</button>
            <button name="unassign" onclick="confirmSubmission('unassign')">Undo Assign</button>
            
        </form>
        
    </main>
    <script>
        let form = document.getElementById('form');
        let submitType = document.getElementById('submitType');
        function confirmSubmission(param){
            if(param === 'assign'){
                if(confirm('This will assign the selected class to the selected teacher')){
                    submitType.value = 'assign';
                    form.submit();
                }
            }else{
                if(confirm('This will proceed with Unassignment')){
                    submitType.value = 'unassign';
                    form.submit();
                }
            }
        }
    </script>
</body>
</html>