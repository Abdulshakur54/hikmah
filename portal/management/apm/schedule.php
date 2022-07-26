<?php
        //initializations
	spl_autoload_register(
		function($class){
			require_once'../../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializatons
        require_once './apm.inc.php';
        function selectedTerm($val){
           global $schedules;
           $curr_term = Utility::escape($schedules->current_term);
           if(strtolower($curr_term) == $val){
              return 'selected'; 
           }
        }
        
        function selectedSchool($abbr){
            if(Input::submitted()){
                $sch_abbr = Input::get('school');
                if($sch_abbr == $abbr){
                    return 'selected';
                }
            }
           
        }
        
        //end of custom functions
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
    <link rel="stylesheet" type="text/css" href="styles/schedules.css" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
            //echo welcome flash message
            if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$apm->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$apm->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        <form method="post" action = "<?php echo Utility::myself() ?>" onsubmit="return false;" id="form" enctype="multipart/form-data">
            <div class="formhead">Schedules And Initializations</div>
            <div>
                 <label for="school">Select School</label>
                 <div>
                     <select name="school" id="school" onchange="submitForm()">
                        <?php 
                        $schools = School::getConvectionalSchools();
                        $genHtml = '';
                        foreach($schools as $sch=>$sch_abbr){
                            $genHtml.='<option value="'.$sch_abbr.'" '.selectedSchool($sch_abbr).'>'.$sch.'</option>';
                        }
                        echo $genHtml;
                        ?>
                    </select>
                 </div>
            </div>
           
            <input type = "hidden" name="submittype" id="submitType" /> <!-- To help detect the type of submission -->
            <div id="genMsg"></div>
            <button name="browse" onclick="submitForm()">Go to schedules</button>
            <div>
                <?php 
                    if(Input::submitted() && Token::check(Input::get('token'))){
                        $submitType = Input::get('submittype');
                        $sch_abbr = Input::get('school');
                        $msg = ''; 
                        if($submitType === 'browse'){
                            $schedules = $apm->getSchedules($sch_abbr);
                            $formFee = Utility::escape($schedules->form_fee);
                            $regFee = Utility::escape($schedules->reg_fee);
                            $ftsf = Utility::escape($schedules->ft_fee);
                            $stsf = Utility::escape($schedules->st_fee);
                            $ttsf = Utility::escape($schedules->tt_fee);
                            $logo = Utility::escape($schedules->logo);
                            echo '<div><label>Current Term </label>'
                            . '<div><select name="term"><option value="ft" '.selectedTerm("ft").'>First Term</option><option value="st"  '.selectedTerm("st").'>Second Term</option><option value="tt" '.selectedTerm("tt").'>Third Term'
                                    . '</option></select></div><div>';
                            echo'<div><label for="formfee">Form Fee(&#8358;)</label><div><input type="text" value="'.$formFee.'" name="formfee" id="formfee"/></div>';
                            echo'<div><label for="regfee">Registration Fee(&#8358;)</label><div><input type="text" value="'.$regFee.'" name="regfee" id="regfee"/></div>';
                            echo'<div><label for="ftsf">First Term School Fee(&#8358;)</label><div><input type="text" value="'.$ftsf.'" name="ftsf" id="ftsf"/></div>';
                            echo'<div><label for="stsf">Second Term School Fee(&#8358;)</label><div><input type="text" value="'.$stsf.'" name="stsf" id="stsf"/></div>';
                            echo'<div><label for="ttsf">Third Term School Fee(&#8358;)</label><div><input type="text" value="'.$ttsf.'" name="ttsf" id="ttsf"/></div>';
                            //for school logo
                            echo '<div>
                                    <label for = "logo" id="uploadTrigger" style="cursor: pointer; color:blue;">Change Logo</label>
                                    <div>
                                        <input type="file" name="logo" id="logo" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png"/>
                                        <img id="image" width="100" height="100" src="uploads/logo/'.$logo.'" />
                                        <input type="hidden" name="hiddenPic" value="" id="hiddenPic"/>
                                        <div id="picMsg" class="errMsg"></div>
                                    </div>
                                </div>';
                            //echo submit button
                            echo'<button onclick="saveChanges()">Save Changes</button>';
                        }
                        if($submitType === 'save'){
                            $formvalues = [
                                'term'=>[
                                    'name'=>'Current Term',
                                    'required'=>true,
                                    'in'=>['ft','st','tt'], 
                                ],
                                'formfee'=>[
                                    'name'=>'Form Fee',
                                    'required'=>true,
                                    'pattern'=>'^[0-9.]+$'
                                ],
                                'regfee'=>[
                                    'name'=>'Registration Fee',
                                    'required'=>true,
                                    'pattern'=>'^[0-9.]+$'
                                ],
                                'ftsf'=>[
                                    'name'=>'First Term School Fee',
                                    'required'=>true,
                                    'pattern'=>'^[0-9.]+$'
                                ],
                                'stsf'=>[
                                    'name'=>'Second Term School Fee',
                                    'required'=>true,
                                    'pattern'=>'^[0-9.]+$'
                                ],
                                'ttsf'=>[
                                    'name'=>'Third Term School Fee',
                                    'required'=>true,
                                    'pattern'=>'^[0-9.]+$'
                                ]
                            ];
                             $fileValues = [
                                'logo'=>[
                                'name'=>'Logo',
                                'required'=>false,
                                'maxSize'=>100,
                                'extension'=>['jpg','jpeg','png']
                                ]
                            ];
 
                            
                            $val = new Validation();
                            if($val->check($formvalues) && $val->checkFile($fileValues)){
                                $term = Utility::escape(Input::get('term'));
                                $formFee = Utility::escape(Input::get('formfee'));
                                $regFee = Utility::escape(Input::get('regfee'));
                                $ftsf = Utility::escape(Input::get('ftsf'));
                                $stsf = Utility::escape(Input::get('stsf'));
                                $ttsf = Utility::escape(Input::get('ttsf'));
                                if(!empty($_FILES['logo']['name'])){
                                    $file = new File('logo');
                                    $ext = $file->extension();
                                    $logoName = $sch_abbr.'.'.$ext;
                                    if($apm->updateSchedule($sch_abbr,$term,$formFee,$regFee,$ftsf,$stsf,$ttsf,$logoName)){
                                        $file->move('uploads/logo/'.$logoName); //move picture to the destination folder
                                        $msg.='<div class="success">Update was successful</div>';
                                    }else{
                                        $msg.='<div class="success">An error is preventing changes to being saved</div>';
                                    }
                                }else{
                                    if($apm->updateSchedule($sch_abbr,$term,$formFee,$regFee,$ftsf,$stsf,$ttsf)){
                                        $msg.='<div class="success">Update was successful</div>';
                                    }else{
                                        $msg.='<div class="success">An error is preventing changes to being saved</div>';
                                    }
                                }
                                
                                
                            }else{
                                foreach($val->errors() as $val){
                                    $msg .= $val . '<br>';
                                }
                            }
                        }
                        echo $msg;
                    }
                    
                ?>
            </div>
             <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
        </form>
    </main>
    <script>
        window.addEventListener('load',function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
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