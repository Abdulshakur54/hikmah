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
    
     function selectedTeacher($teaId){
        global $teacherId;
        if(!empty($teacherId) && $teaId == $teacherId){
            return 'selected';
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
    <title>Assign Subject</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/assign_subject.css" />
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
            <div class="formhead">Assign Subject</div>
             <?php 
                if(Input::submitted() && Token::check(Input::get('token'))){
                    $subjectId = Utility::escape(Input::get('subject'));
                    $teacherId = Utility::escape(Input::get('teacherid'));
                    $msg = '';
                    $val = new Validation();
                    $values = [
                        'subject'=>[
                            'name'=>'Subject',
                            'required'=>true,
                            'exists'=>'id/subject'
                        ],
                        'teacherid'=>[
                            'name'=>'Teacher',
                            'required'=>true,
                            'exists'=>'staff_id/staff'
                        ]
                    ];
                    if($val->check($values)){
                        $names = $hos->getStaffNames($teacherId);
                        $fullname = Utility::formatName($names->fname, $names->oname, $names->lname);
                        $submitType = Input::get('submitType');
                    
                        if($submitType === 'assign'){
                            
                            if(!$hos->isSubjectTeacher($teacherId,$subjectId)){
                                if(!$hos->subjectHasTeacher($subjectId)){
                                    //update subject table
                                    $hos->updateSubjectTeachers($subjectId,$teacherId);
                                    $msg = '<div class="success">'.$fullname.' is now a Teacher for the selected subject</div>';
                                }else{
                                    $msg = '<div class="failure">Subject already has a teacher<br>You should unassign the current teacher to create way to assign another teacher</div>';
                                }
                            }else{
                                $msg = '<div class="failure">'.$fullname.' is already the teacher for the selected subject</div>';
                            }
                            
                        }
                        
                        if($submitType === 'unassign'){
                            if($hos->isSubjectTeacher($teacherId,$subjectId)){
                                //update subject and teachers table
                                $hos->updateSubjectTeachers($subjectId,$teacherId,true);
                                $msg = '<div class="success">Unassignment was successful</div>';
                            }else{
                               $msg = '<div class="failure">'.$fullname.' is not a teacher for the selected subject</div>'; 
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
                                    $name = Utility::formatName($availableTeacher->fname, $availableTeacher->oname, $availableTeacher->lname);
                                    echo '<option value="'.$availableTeacher->staff_id.'" '.selectedTeacher($availableTeacher->staff_id).'>'.$name.' &nbsp;['.$availableTeacher->staff_id.']</option>';
                                }
                                
                            }
                        ?>
                    </select>
                 </div>
            </div>
            
            <div>
                 <label for="subject">Select Subject</label>
                 <div>
                    <select name="subject">
                        <?php
                            $subjects  = $hos->getSubjects($sch_abbr);
                            if(!empty($subjects)){
                                foreach ($subjects as $subject){
                                    echo '<option value="'.$subject->id.'">'.$subject->subject.' ['.School::getLevelName($sch_abbr, $subject->level).$subject->class.']'.'</option>';
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
                if(confirm('This will assign the selected subject to the selected teacher')){
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