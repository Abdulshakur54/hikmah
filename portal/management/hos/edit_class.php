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
    <title>Edit Class</title>
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
        <form method="post" action = "<?php echo Utility::myself() ?>">
            <div class="formhead">Edit Class</div>
             <?php 
                $newClass = '';
                $petname = '';
                if(Input::submitted() && Token::check(Input::get('token'))){                   
                    $msg = '';
                    $val = new Validation();
                    $values = [
                        'classid'=>[
                            'name'=>'Class',
                            'required'=>true
                        ],
                        'newclass'=>[
                            'name'=>'New Name',
                            'required'=>false,
                            'pattern'=>'^[a-zA-Z]$'
                        ],
                        'petname'=>[
                            'name'=>'Petname',
                            'required'=>false,
                            'pattern'=>'^[a-zA-Z]+$',
                        ]
                    ];
                    if($val->check($values)){
                       $classId = Utility::escape(Input::get('classid'));
                       $level = Utility::escape(Input::get('level'));
                       $newClass = Utility::escape(Input::get('newclass'));
                       $newClass = strtoupper($newClass);
                       $petname = Utility::escape(Input::get('petname'));
                       if($hos->classExists($sch_abbr,$level,$newClass)){
                           
                            $msg = '<div class="failure">'.School::getLevelName($sch_abbr, $level).' '.strtoupper($newClass). ' already exist</div>';
                        }else{
                             //insert into class table
                            $hos->editClass($classId,$newClass,$petname);
                            $newClass = '';
                            $petname = '';
                            //output success message
                            $msg = '<div class="success">Update was successful</div>';
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
                     <select name="classid" onchange="changePetname()" id ="classid">
                        <?php
                            $availableClass  = $hos->getClasses($sch_abbr);
                            if(!empty($availableClass)){
                                $classPetnameLevArr = []; //an associative array
                                foreach ($availableClass as $avaClass){
                                    $cId = (int)$avaClass->id;
                                    $classPetnameLevArr[$avaClass->id] = [$avaClass->petname,$avaClass->level]; //populate the $classPetName array
                                    echo '<option value="'.$cId.'">'.School::getLevelName(Utility::escape($sch_abbr), Utility::escape($avaClass->level)).' '. Utility::escape($avaClass->class).'</option>';
                                }
                                $classPetnameJson = json_encode($classPetnameLevArr);
                            }
                        ?>
                    </select>
                 </div>
            </div>
            <div>
                 <label for="newclass">New Name</label>
                 <div>
                     <input type="text" name="newclass" id="newclass" value="<?php echo $newClass?>"/>
                 </div>
            </div>
            <div>
                 <label for="petname">Petname</label>
                 <div>
                     <input type="text" name="petname" id="petname" value="<?php echo $petname?>" />
                 </div>
            </div>
            <div class="none" id="classPetname">
                <?php echo $classPetnameJson; ?>
            </div>
            <input type = "hidden" name="level" id="level" />
            <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
            <button>Save changes</button>
            
        </form>
        
    </main>
    <script>
        let classPetname = JSON.parse(document.getElementById('classPetname').innerHTML);
        let petname = document.getElementById('petname');
        let level = document.getElementById('level');
        let classId = document.getElementById('classid');
        changePetname();
        function changePetname(){
            petname.value = classPetname[classId.value][0];
            level.value = classPetname[classId.value][1];
        }
    </script>
</body>
</html>