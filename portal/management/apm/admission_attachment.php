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
    
     //this custom function helps to detemine which option in the select tag for sch_abbr is selected
    function getSelectedSchool($val){
        global $sch_abbr;
        return ($val === $sch_abbr)?'selected':'';
    }
    
    function getSelectedLevel($lev){
        global $level;
       return ($lev == $level)?'selected':'';
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admission Attachments</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/admission_attachment.css" />
</head>
<body>
    <main>
        <?php
        require_once '../nav.inc.php'; 
        $msg = '';
        $agg = new Aggregate();
       if(Input::submitted() && Token::check(Input::get('token'))){
                if(!empty(Input::get('sch_abbr')) && !empty(Input::get('level'))){
                    $sch_abbr = Utility::escape(Input::get('sch_abbr'));
                    $level = Utility::escape(Input::get('level'));
                    
                    
                    if(Utility::escape(Input::get('attachment')==='true')){ //if attachment, upload attachment to the server after validation
                        $val = new Validation();
                        $fileValues = [
                            'selectedFile'=>[
                            'name'=>'File',
                            'required'=>true,
                            'maxSize'=>3072
                            ]
                        ];
                        if($val->checkFile($fileValues)){
                             $selectedFile = new File('selectedFile');
                             $name = $selectedFile->name();
                             
                             $att_name = 'f'.($agg->max('id', 'attachment') + 1).'_'.$name;
                             //input the values into attachment table and also store the file in attachment folder
                            $apm->insertAttachment($att_name,$name,$sch_abbr,$level);
                            $selectedFile->move('uploads/attachment/'.$att_name);
                            Session::set_flash('fileUploaded','<div class="success">Successfully uploaded attachment</div>');
                            $attachment = $apm->selectAdmissionAttatchments($sch_abbr,$level);
                             
                        }else{
                            $errors = $val->errors();
                            foreach ($errors as $error){
                                $msg.=$error.'<br>';
                            }
                            
                            $msg.='<div class="failure">'.$msg.'</div>';
                        }
                    }
                }
            }else{
                if(Input::submitted('get') && Input::get('delete')==='true'){
                    $idToDel = Utility::escape(Input::get('idToDelete'));
                    $fileToDel = $agg->lookUp('attachment', 'attachment', 'id,=,'.$idToDel);
                    $apm->delAttachment($idToDel);
                    unlink('attachment/'.$fileToDel); //deletes file from the server
                    Session::set_flash('fileDeleted', '<div class="success">File successfully deleted</div>');
                }
            }
            if(Utility::escape(Input::get('filter')==='true')){ //if filteree
                $attachment = $apm->selectAdmissionAttatchments($sch_abbr,$level);
            }else{
                $attachment = $apm->selectAdmissionAttatchments();
            }    
            
            
            
   
            ?>
            <form enctype="multipart/form-data" action="<?php echo Utility::myself()?>" method="POST" onsubmit="return false">
            <?php
            echo '<label>Select School</label> <select name="sch_abbr" id="sch_abbr" onchange="populateLevel(this)"><option value="ALL" selected>ALL</option>';
            $sch_abbrs = School::getConvectionalSchools(2); //this returns the abbreviation for each of the convectional schools
            
            foreach ($sch_abbrs as $sch_ab){
                echo '<option value="'.$sch_ab.'" '.getSelectedSchool($sch_ab).'>'.$sch_ab.'</option>';
            }
            echo '</select>'; 
            echo '<label>Select Level</label>
                <select name="level" id="level"><option value="ALL" '.getSelectedLevel("ALL").'>ALL</option>';
                $levels = School::getLevels($sch_abbr);
                if(!empty($levels)){
                    foreach ($levels as $levName=>$lev){
                        echo '<option value="'.$lev.'" '.getSelectedLevel($lev).'>'.$levName.'</option>';
                   }
                }
            
             echo '</select> <button onclick="filterDisplay()">Filter</button>';
             ?>
        <div>
            <label for = "selectedFile" id="uploadTrigger" style="cursor: pointer; color:green;">Browse File</label>
           
            <input type="file" name="selectedFile" id="selectedFile" style="display: none" onchange="displayName(this,30)" /> <!-- 30 means 30Mb -->
            <button onclick="uploadFile()">Attach</button>
            <span id="nameIndicator"><?php echo Session::get_flash('fileUploaded').Session::get_flash('fileDeleted');?></span><!-- the two session flash are concatenated because any of it that dosent't exist would not output anything-->
            <div id="genMsg"><?php echo $msg; ?></div>
            <input type="hidden" name="hiddenFileName" value="" id="hiddenFileName"/>
            <div id="errMsg"></div>
           
        </div>
        
        <?php
            if(!empty($attachment)){?>
        
                <table>
                    <thead>
                        <tr><th>File Name</th><th>School</th><th>Level</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res=$attachment;
                        $count = 1;
                        foreach($res as $val){
                           $sch_abbr = Utility::escape($val->sch_abbr);
                           $level = Utility::escape($val->level);
                            echo '<tr id="row'.$val->id.'"><td>'. Utility::escape($val->name).'</td><td>'.$sch_abbr.'</td><td>'.School::getLevelName($sch_abbr,$level).'</td><td onclick="deleteAttachment('.$val->id.')" class="tdLink">delete</td></tr>';
                            $count++;
                        }
                        ?>
                    </tbody>
                </table> 
                <input type = "hidden" value = "<?php echo($count-1); ?>" id="counter" /> <!--hidden counter -->
                <?php
                
                }else{
                    echo '<div class="message">No Attachments Available</div>';
                }
        ?>
                <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
                <input type="hidden" name="attachment" value="false" id="attachment"/>
                <input type="hidden" name="filter" value="false" id="filter"/>
            </form>
    </main>
    <script>
        window.onload = function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
            appendScript('<?php echo Utility::escape($url->to('scripts/portalscript.js',0))?>');  
            appendScript("scripts/admission_attachment.js");
        }

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>

