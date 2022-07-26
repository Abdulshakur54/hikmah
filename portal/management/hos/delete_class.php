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
    <title>Delete Class</title>
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
            <div class="formhead">Delete Class</div>
             <?php 
                if(Input::submitted() && Token::check(Input::get('token'))){
                    
                    $classId = Utility::escape(Input::get('classid'));
                    $msg = '';
                    $val = new Validation();
                    $values = [
                        'classid'=>[
                            'name'=>'Class',
                            'required'=>true,
                            'exists'=>'id/class'
                        ]
                    ];
                    if($val->check($values)){
                        if($hos->deleteClass($classId,$sch_abbr)){
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
                 <label for="school">Select Class</label>
                 <div>
                    <select name="classid">
                        <?php
                            $availableClass  = $hos->getClasses($sch_abbr);
                            if(!empty($availableClass)){
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
            <button>Delete</button>
        </form>
        
    </main>
    <script>
        
        function confirmSubmission(){
            return confirm('This will delete the selected class');
        }
    </script>
</body>
</html>