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
        if($level !== null && $lev == $level){
            return 'selected';
        }
    }
    
    $class = '';
    $nos = '';
    $petname = '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Add Class</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/add_class.css" />
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
            <div class="formhead">Add Class</div>
            <?php 
                $level = null; //this will only change when form is submitted via post
                if(Input::submitted() && Token::check(Input::get('token'))){
                    $class = Utility::escape(Input::get('class'));
                    $class = strtoupper($class);
                    $petname = Utility::escape(Input::get('petname'));
                    $level = (int)Input::get('levelid');
                    $nos = (int)Input::get('nos');
                    if(preg_match('/^[a-zA-Z]$/', $class)){
                        if(empty($petname) || preg_match('/^[a-zA-Z]+$/', $petname)){ //validate petname
                            $utils = new Utils();
                            //$currSession = $utils->getSession($sch_abbr);
                            if($hos->classExists($sch_abbr,$level,$class)){
                                $msg = '<div class="failure">'.School::getLevelName($sch_abbr, $level).' '.strtoupper($class). ' already exist</div>';
                            }else{
                                //insert into class table
                                $hos->addClass($sch_abbr,$level,$class,$nos,$petname);
                                $class = '';
                                $nos = '';
                                $petname = '';
                                //output success message
                                $msg = '<div class="success">Successfully Added '.School::getLevelName($sch_abbr, $level).' '.strtoupper($class).'</div>';
                            }
                        }else{
                             $msg = '<div class="failure">Invalid Petname</div>';
                        } 
                     
                    }else{
                        $msg = '<div class="failure">Invalid Class Name</div>';
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
                 <label for="class">Enter Class</label>
                 <div>
                     <input type="text" name="class" value="<?php echo $class;?>" />
                 </div>
            </div>
            <div>
                 <label for="petname">Petname</label>
                 <div>
                     <input type="text" name="petname" value="<?php echo $petname;?>" />
                 </div>
            </div>
             <div>
                 <label for="class">Min No of Subject</label>
                 <div>
                     <input type="number" name="nos" min="1" value="<?php echo $nos;?>" required />
                 </div>
            </div>
            
            <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
            <button name="addclass">Add</button>
        </form>
        
    </main>
</body>
</html>