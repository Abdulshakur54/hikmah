<?php
//initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once 'hos.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Delete Subject</title>
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
        <form method="post" action = "<?php echo Utility::myself(); ?>" onsubmit="return confirmSubmission();">
            <div class="formhead">Delete Subject</div>
             <?php 
                if(Input::submitted() && Token::check(Input::get('token'))){
                    
                    $subjectId = Utility::escape(Input::get('subject'));
                    $msg = '';
                    $val = new Validation();
                    $values = [
                        'subject'=>[
                            'name'=>'Subject',
                            'required'=>true,
                            'exists'=>'id/subject'
                        ]
                    ];
                    if($val->check($values)){
                        if($hos->deleteSubject($subjectId,$sch_abbr)){
                             $msg = '<div class="success">Deletion was successful</div>';
                        }else{
                             $msg = '<div class="failure">Error encountered: Deletion not successful</div>';
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
            <button>Delete</button>
        </form>
        
    </main>
    <script>
        
        function confirmSubmission(){
            return confirm('This will delete the selected subject');
        }
    </script>
</body>
</html>