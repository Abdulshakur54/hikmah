<?php
     //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once 'nav1.inc.php';
    require_once 'class_teacher.inc.php';
    //custom function
    function selectedParam($param,$selParam){
        if($param == $selParam){
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
    <title>Schedules And Initializations</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/schedule.css" />
</head>
<body>
    <main>
        <?php 
            require_once './nav.inc.php';
            
        ?>
        <form method="post" action = "<?php echo Utility::myself() ?>" id="form" onsubmit="return false;" enctype="multipart/form-data">
            <div class="formhead">Schedules And Initializations</div>
             <?php 
 
                $genMsg = '';
                if(Input::submitted() && Token::check(Input::get('token'))){
                    $punc = Utility::escape(Input::get('punc'));
                    $hon = Utility::escape(Input::get('hon'));
                    $dhw = Utility::escape(Input::get('dhw'));
                    $rap = Utility::escape(Input::get('rap'));
                    $sot = Utility::escape(Input::get('sot'));
                    $rwp = Utility::escape(Input::get('rwp'));
                    $ls = Utility::escape(Input::get('ls'));
                    $atw = Utility::escape(Input::get('atw'));
                    $ho = Utility::escape(Input::get('ho'));
                    $car = Utility::escape(Input::get('car'));  
                    $con = Utility::escape(Input::get('con'));
                    $wi = Utility::escape(Input::get('wi'));
                    $ob = Utility::escape(Input::get('ob'));
                    $hea = Utility::escape(Input::get('hea'));
                    $vs = Utility::escape(Input::get('vs'));
                    $pig = Utility::escape(Input::get('pig'));
                    $pis = Utility::escape(Input::get('pis'));
                    $ac = Utility::escape(Input::get('ac'));
                    $pama = Utility::escape(Input::get('pama'));
                    $ms = Utility::escape(Input::get('ms'));
                      
                    $a1 = Utility::escape(Input::get('a1'));
                    $b2 = Utility::escape(Input::get('b2'));
                    $b3 = Utility::escape(Input::get('b3'));
                    $c4 = Utility::escape(Input::get('c4'));
                    $c5 = Utility::escape(Input::get('c5'));
                    $c6 = Utility::escape(Input::get('c6'));
                    $d7 = Utility::escape(Input::get('d7'));
                    $e8 = Utility::escape(Input::get('e8'));
                    $f9 = Utility::escape(Input::get('f9'));
                    
                    $height_beg = Utility::escape(Input::get('height_beg'));
                    $height_end = Utility::escape(Input::get('height_end'));
                    $weight_beg = Utility::escape(Input::get('weight_beg'));
                    $weight_end = Utility::escape(Input::get('weight_end'));
                    
                    if(!empty($_FILES['signature']['name'])){
                        $file = new File('signature');
                        $ext = $file->extension();
                        $signatureName = $sch_abbr.'_'.$level.$class.'.'.$ext;
                        //update schedule
                        if($staff->updateSchedule($classId,$punc,$hon,$dhw,$rap,$sot,$rwp,$ls,$atw,$ho,$car,$con,$wi,$ob,$hea,$vs,$pig,$pis,$ac,$pama,$ms,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$height_beg,$height_end,$weight_beg,$weight_end,$signatureName)){
                            $file->move('uploads/signatures/'.$signatureName); //move picture to the destination folder
                            //update psycometry for students
                            $stdIds = $staff->getStudentsIds($classId);
                            if(!empty($stdIds)){
                                $stdIdsString= "'".implode("','", $stdIds)."'";
                                $staff->populateStdPsy($classId,$stdIdsString); //update psycometry
                            }
                            $genMsg = '<div class="success">Changes has been successfully updated</div>';
                        }else{
                            $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                        }
                    }else{
                        //update schedule
                        if($staff->updateSchedule($classId,$punc,$hon,$dhw,$rap,$sot,$rwp,$ls,$atw,$ho,$car,$con,$wi,$ob,$hea,$vs,$pig,$pis,$ac,$pama,$ms,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$height_beg,$height_end,$weight_beg,$weight_end)){
                            //update psycometry for students
                            $stdIds = $staff->getStudentsIds($classId);
                            if(!empty($stdIds)){
                                $stdIdsString= "'".implode("','", $stdIds)."'";
                                $staff->populateStdPsy($classId,$stdIdsString); //update psycometry
                            }
                            $genMsg = '<div class="success">Changes has been successfully updated</div>';
                        }else{
                            $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                        }
                    }
                    
                }
                $schedule = $staff->getSchedule($classId);
                echo $genMsg;
            ?>
            
            <section>
                <h4>Psychometry Setting</h4>
                <div>
                    <label for="punc">Punctuality</label>
                    <select name="punc" id="punc">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy1)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy1)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy1)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy1)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy1)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy1)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="hon">Honesty</label>
                    <select name="hon" id="hon">
                       <option value="A" <?php echo selectedParam('A',$schedule->psy2)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy2)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy2)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy2)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy2)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy2)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="dhw">Does Homework</label>
                    <select name="dhw" id="dhw">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy3)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy3)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy3)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy3)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy3)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy3)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="rap">Respect and Politeness</label>
                    <select name="rap" id="rap">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy4)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy4)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy4)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy4)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy4)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy4)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="sot">Spirit of Teamwork</label>
                    <select name="sot" id="sot">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy5)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy5)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy5)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy5)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy5)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy5)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="rwp">Relationship with Peers</label>
                    <select name="rwp" id="rwp">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy6)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy6)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy6)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy6)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy6)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy6)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="ls">Leadership skills</label>
                    <select name="ls" id="ls">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy7)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy7)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy7)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy7)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy7)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy7)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="atw">Attitude to work</label>
                    <select name="atw" id="atw">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy8)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy8)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy8)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy8)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy8)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy8)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="ho">Helping others</label>
                    <select name="ho" id="ho">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy9)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy9)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy9)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy9)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy9)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy9)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="car">Carefulness</label>
                    <select name="car" id="car">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy10)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy10)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy10)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy10)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy10)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy10)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="con">Consideration</label>
                    <select name="con" id="con">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy11)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy11)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy11)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy11)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy11)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy11)?>>NULL</option>
                    </select>
                </div>
                 <div>
                    <label for="wi">Works Independently</label>
                    <select name="wi" id="wi">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy12)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy12)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy12)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy12)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy12)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy12)?>>NULL</option>
                    </select>
                </div>
                 <div>
                    <label for="ob">Obedience</label>
                    <select name="ob" id="ob">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy13)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy13)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy13)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy13)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy13)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy13)?>>NULL</option>
                    </select>
                </div>
                 <div>
                    <label for="hea">Health</label>
                    <select name="hea" id="hea">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy14)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy14)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy14)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy14)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy14)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy14)?>>NULL</option>
                    </select>
                </div>
                 <div>
                    <label for="vs">Verbal Skills</label>
                    <select name="vs" id="vs">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy15)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy15)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy15)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy15)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy15)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy15)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="pig">Participation in games</label>
                    <select name="pig" id="pig">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy16)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy16)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy16)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy16)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy16)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy16)?>>NULL</option>
                    </select>
                </div>
                 <div>
                    <label for="pis">Participation in sports</label>
                    <select name="pis" id="pis">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy17)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy17)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy17)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy17)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy17)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy17)?>>NULL</option>
                    </select>
                </div>
                 <div>
                    <label for="ac">Artistic Creativity</label>
                    <select name="ac" id="ac">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy18)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy18)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy18)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy18)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy18)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy18)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="pama">Physical and Mental Agility</label>
                    <select name="pama" id="pama">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy19)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy19)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy19)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy19)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy19)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy19)?>>NULL</option>
                    </select>
                </div>
                <div>
                    <label for="ms">Manual Skill(Dexterity)</label>
                    <select name="ms" id="ms">
                        <option value="A" <?php echo selectedParam('A',$schedule->psy20)?>>A</option>
                        <option value="B+" <?php echo selectedParam('B+',$schedule->psy20)?>>B+</option>
                        <option value="B" <?php echo selectedParam('B',$schedule->psy20)?>>B</option>
                        <option value="C" <?php echo selectedParam('C',$schedule->psy20)?>>C</option>
                        <option value="D" <?php echo selectedParam('D',$schedule->psy20)?>>D</option>
                        <option value="" <?php echo selectedParam("",$schedule->psy20)?>>NULL</option>
                    </select>
                </div>

            </section>
            
            <section>
                <h4>Commentary Settings (For overall average)</h4>
                <div>
                    <label for="a1">A1(75-100)</label>
                    <input type="text" name="a1" value="<?php echo Utility::escape($schedule->a1)?>" id="a1" required />
                </div>
                
                <div>
                    <label for="b2">B2(70-74)</label>
                    <input type="text" name="b2" value="<?php echo Utility::escape($schedule->b2)?>" id="b2" required />
                </div>
                
                <div>
                    <label for="b3">B3(65-69)</label>
                    <input type="text" name="b3" value="<?php echo Utility::escape($schedule->b3)?>" id="b3" required />
                </div>
                
                <div>
                    <label for="c4">C4(60-64)</label>
                    <input type="text" name="c4" value="<?php echo Utility::escape($schedule->c4)?>" id="c4" required />
                </div>
                
                <div>
                    <label for="c5">C5(55-59)</label>
                    <input type="text" name="c5" value="<?php echo Utility::escape($schedule->c5)?>" id="c5" required />
                </div>
                
                <div>
                    <label for="c6">C6(50-54)</label>
                    <input type="text" name="c6" value="<?php echo Utility::escape($schedule->c6)?>" id="c6" required />
                </div>
                
                <div>
                    <label for="d7">D7(45-49)</label>
                    <input type="text" name="d7" value="<?php echo Utility::escape($schedule->d7)?>" id="d7" required />
                </div>
                
                <div>
                    <label for="e8">E8(40-44)</label>
                    <input type="text" name="e8" value="<?php echo Utility::escape($schedule->e8)?>" id="e8" required />
                </div>
                
                <div>
                    <label for="f9">F9(0-39)</label>
                    <input type="text" name="f9" value="<?php echo Utility::escape($schedule->f9)?>" id="f9" required />
                </div>
            </section>
            
            <section>
                <h4>Height and Weight</h4>
                <div>
                    <label for="height_beg">Height at term began(m)</label>
                    <input type="text" name="height_beg" value="<?php echo Utility::escape($schedule->height_beg)?>" id="height_beg" />
                </div>
                <div>
                    <label for="height_end">Height at term End(m)</label>
                    <input type="text" name="height_end" value="<?php echo Utility::escape($schedule->height_end)?>" id="height_end" />
                </div>
                <div>
                    <label for="weight_beg">Weight at term began(kg)</label>
                    <input type="text" name="weight_beg" value="<?php echo Utility::escape($schedule->weight_beg)?>" id="weight_beg" />
                </div>
               <div>
                    <label for="weight_end">Weight at term end(kg)</label>
                    <input type="text" name="weight_end" value="<?php echo Utility::escape($schedule->weight_end)?>" id="weight_end" />
                </div>
            </section> 
            
            <section>
                <h4>Signature</h4>
                <div>
                    <label for = "signature" id="uploadTrigger" style="cursor: pointer; color:blue;">Upload Signature</label>
                    <div>
                        <input type="file" name="signature" id="signature" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png"/>
                        <img id="image" width="100" height="100" src="<?php echo 'uploads/signatures/'.Utility::escape($schedule->signature) ?>" />
                        <input type="hidden" name="hiddenPic" value="" id="hiddenPic"/>
                        <div id="picMsg" class="errMsg"></div>
                    </div>
                </div>
            </section>
    
            <button onclick="saveChanges()">Save changes</button>
            <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />            
        </form>
        
    </main>
    <script>
        window.addEventListener('load',function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript('scripts/schedule.js');  
        });
        function appendScript(source){
            let script = document.createElement('script');
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>