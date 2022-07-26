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
    <title>Schedules And Initializations</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/schedule.css" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
            //display alert
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
        <form method="post" action = "<?php echo Utility::myself() ?>" id="form" onsubmit="return false;" enctype="multipart/form-data">
            <div class="formhead">Schedules And Initializations</div>
             <?php 
                $errCode = 0;
                $genMsg = '';
                if(Input::submitted() && Token::check(Input::get('token'))){
                    $fa = (int)Input::get('fa');
                    $sa = (int)Input::get('sa');
                    $ft = (int)Input::get('ft');
                    $st = (int)Input::get('st');
                    $pro = (int)Input::get('pro');
                    $exam = (int)Input::get('exam');     
                    $ftto = (int)Input::get('ftto');
                    $stto = (int)Input::get('stto');
                    $ttto = (int)Input::get('ttto');
                    $ftrd = Utility::escape(Input::get('ftrd'));
                    $strd = Utility::escape(Input::get('strd'));
                    $ttrd = Utility::escape(Input::get('ttrd'));
                    $ftcd = Utility::escape(Input::get('ftcd'));
                    $stcd = Utility::escape(Input::get('stcd'));
                    $ttcd = Utility::escape(Input::get('ttcd'));
                    $a1 = Utility::escape(Input::get('a1'));
                    $b2 = Utility::escape(Input::get('b2'));
                    $b3 = Utility::escape(Input::get('b3'));
                    $c4 = Utility::escape(Input::get('c4'));
                    $c5 = Utility::escape(Input::get('c5'));
                    $c6 = Utility::escape(Input::get('c6'));
                    $d7 = Utility::escape(Input::get('d7'));
                    $e8 = Utility::escape(Input::get('e8'));
                    $f9 = Utility::escape(Input::get('f9'));
                    
                     if(($fa + $sa + $ft + $st + $pro + $exam) != 100){
                        $errCode = 1;
                        $genMsg = '<div class="failure">The scores must sum up to 100</div>';
                    }else{
                        $scoreTable = $utils->getFormatedSession($sch_abbr).'_score'; //the current score table
                        
                        if(!empty($_FILES['signature']['name'])){
                            $file = new File('signature');
                            $ext = $file->extension();
                            $signatureName = $sch_abbr.'.'.$ext;
                            //update schedule
                            if($hos->updateSchedule($sch_abbr,$fa,$sa,$ft,$st,$pro,$exam,$ftto,$stto,$ttto,$ftrd,$strd,$ttrd,$ftcd,$stcd,$ttcd,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$signatureName)){
                                 $file->move('uploads/signatures/'.$signatureName); //move picture to the destination folder
                                 //update all the scores of this school from the score table depending on the term
                                 $hos->updateScore($scoreTable,$currTerm,$sch_abbr);
                                $genMsg = '<div class="success">Changes has been successfully updated</div>';
                            }else{
                                $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                            }
                        }else{
                            //update schedule
                            if($hos->updateSchedule($sch_abbr,$fa,$sa,$ft,$st,$pro,$exam,$ftto,$stto,$ttto,$ftrd,$strd,$ttrd,$ftcd,$stcd,$ttcd,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9)){
                                //update all the scores of this school from the score table depending on the term
                                $hos->updateScore($scoreTable,$currTerm,$sch_abbr);
                                $genMsg = '<div class="success">Changes has been successfully updated</div>';
                            }else{
                                $genMsg = '<div class="failure">Problem encountered while trying to save changes</div>';
                            }
                        }
                           
                       
                    }
                }
                $schedule = $hos->getSchedule($sch_abbr);
            ?>
            
            <div><?php if($errCode !== 1){echo $genMsg;}?></div>
            <section>
                <h4>Scores Setting</h4>
                <div>
                    <label for="fa">First Assignment</label>
                    <input type="number" name="fa" value="<?php echo Utility::escape($schedule->fa)?>" id="fa" max="100" min="0" required />
                </div>
            
                <div>
                    <label for="sa">Second Assignment</label>
                    <input type="number" name="sa" value="<?php echo Utility::escape($schedule->sa)?>" id="sa" max="100" min="0" required />
                </div>
            
                <div>
                    <label for="ft">First Test</label>
                    <input type="number" name="ft" value="<?php echo Utility::escape($schedule->ft)?>" id="ft" max="100" min="0" required />
                </div>
            
                <div>
                     <label for="st">Second Test</label>
                     <input type="number" name="st" value="<?php echo Utility::escape($schedule->st)?>" id="st" max="100" min="0" required />
                </div>
                
                <div>
                     <label for="st">Project</label>
                     <input type="number" name="pro" value="<?php echo Utility::escape($schedule->pro)?>" id="st" max="100" min="0" required />
                </div>
            
                <div>
                    <label for="exam">Exam</label>
                    <input type="number" name="exam" value="<?php echo Utility::escape($schedule->exam)?>" id="exam" max="100" min="0" required />
                </div>
                <div><?php if($errCode == 1){echo $genMsg;}?></div>
            </section>
            
            <section>
                <section>
                    <h4>First Term Schedules</h4>
                    <div>
                        <label for="ftto">Times Opened</label>
                        <input type="number" name="ftto" value="<?php echo Utility::escape($schedule->ft_times_opened)?>" id="ftto" max="200" min="0" required />
                    </div>

                    <div>
                        <label for="ftrd">Resumption Date(Appears on result)</label>
                        <input type="date" name="ftrd" value="<?php echo Utility::escape($schedule->ft_res_date)?>" id="ftrd" required />
                    </div>

                    <div>
                        <label for="ftcd">Closing Date(Appears on result)</label>
                        <input type="date" name="ftcd" value="<?php echo Utility::escape($schedule->ft_close_date)?>" id="ftcd" required />
                    </div>
                </section> 
                
                <section>
                    <h4>Second Term Schedules</h4>
                    <div>
                        <label for="stto">Times Opened</label>
                        <input type="number" name="stto" value="<?php echo Utility::escape($schedule->st_times_opened)?>" id="stto" max="200" min="0" required />
                    </div>
                    
                    <div>
                        <label for="strd">Resumption Date(To appear on Second Term result)</label>
                        <input type="date" name="strd" value="<?php echo Utility::escape($schedule->st_res_date)?>" id="strd" required />
                    </div>
                    
                    <div>
                        <label for="ftcd">Closing Date(Appears on result)</label>
                        <input type="date" name="stcd" value="<?php echo Utility::escape($schedule->st_close_date)?>" id="stcd" required />
                    </div>
                    
                </section>
                
                <section>
                    <h4>Third Term Schedules</h4>
                    <div>
                    <label for="ttto">Times Opened</label>
                        <input type="number" name="ttto" value="<?php echo Utility::escape($schedule->tt_times_opened)?>" id="ttto" max="200" min="0" required />
                    </div>
                    
                    <div>
                        <label for="ttrd">Resumption Date(Appears on result)</label>
                        <input type="date" name="ttrd" value="<?php echo Utility::escape($schedule->tt_res_date)?>" id="ttrd" required />
                    </div>
                    
                    <div>
                        <label for="ttcd">Closing Date(Appears on result)</label>
                        <input type="date" name="ttcd" value="<?php echo Utility::escape($schedule->tt_close_date)?>" id="ttcd" required />
                    </div>
                </section>
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