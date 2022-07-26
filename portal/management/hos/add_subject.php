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
    function selectLevel($lev){
        global $level;
        if($level !== null and $lev == $level){
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
    <title>Add Subject</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/add_subject.css" />
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
        <form method="post" action = "<?php echo Utility::myself() ?>">
            <div class="formhead">Add Subject</div>
            <?php 
                $level = null; //this will only change when form is submitted via post
                if(Input::submitted() && Token::check(Input::get('token'))){
                    $subject = Utility::escape(Input::get('subject'));
                    $subject = ucwords($subject);
                    $level = (int)Input::get('levelid');
                    if(preg_match('/^[a-zA-Z ]{3,50}$/', $subject)){
                        if($hos->subjectExists($sch_abbr,$level,$subject)){
                            $msg = '<div class="failure">'.$subject.' already exist for '.School::getLevelName($sch_abbr, $level).'</div>';
                        }else{
                            $classIds = $hos->getLevelClasses($level);
                            
                            if(!empty($classIds)){
                            foreach($classIds as $id){
                                //insert into subject table
                                $hos->addSubject($sch_abbr,$subject,$id->id,$level);
                                $msg = '<div class="success">Successfully Added '.$subject.' for '.School::getLevelName($sch_abbr, $level).'</div>';
                            }
                            }else{
                                $msg = '<div class="failure">No class has been added to '.School::getLevelName($sch_abbr, $level).' yet</div>';
                            }
                        }
                     
                    }else{
                        $msg = '<div class="failure">Invalid Subject Name</div>';
                    }
                    Session::set_flash('message',$msg);
                }
            ?>
            <div><?php echo Session::get_flash('message')?></div>
            <div>
                 <label for="school">Select Level</label>
                 <div>
                    <select name="levelid">
                        <?php
                            $schLevels = School::getLevels($sch_abbr);
                            foreach ($schLevels as $levName=>$lev){
                                echo '<option value="'.$lev.'"'.selectLevel($lev).'>'.$levName.'</option>';
                            }
                        ?>
                        
                    </select>
                 </div>
            </div>
            
            <div>
                 <label for="subject">Subject Name</label>
                 <div>
                     <input type="text" name="subject" />
                 </div>
            </div>
            
            <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
            <button name="addclass">Add</button>
        </form>
        
    </main>
</body>
</html>