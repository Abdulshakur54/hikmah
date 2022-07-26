<?php
     //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once './nav1.inc.php';
    
    function getStatus(){
        global $data;
        switch((int) $data->status){
            case 0:
                return 'Awaiting Admission';
            case 1:
                return 'Admission Offered';
            case 2:
                return 'Admission Accepted';
            case 3: 
                return 'Admission Declined';
        }
    }
    
    $msg = '';
    $admId = $username;
    if(!$adm->hasApplied($id)){
        echo '<div>Complete you application. <a href="apply.php">Complete Application</a></div>';
        exit();
    }
   if(Input::submitted('get') && Input::get('download')==='true'){
       $school = Utility::escape($data->sch_abbr);
       //store the body content in a variable
       $body = '
           
                <main>
                    <div>
                        <div>Bio Details</div>
                        <div>
                            <img src="'.$url->to('uploads/passports/'. Utility::escape($data->picture),5).'" alt="picture" width="100" height="100"/>
                        </div>
                        <div>
                            <div class="label">First Name</div>
                            <div class="value">'.ucfirst(Utility::escape($data->fname)).'</div>
                        </div>
                        <div>
                            <div class="label">Last Name</div>
                            <div class="value">'.ucfirst(Utility::escape($data->lname)).'</div>
                        </div>
                        <div>
                            <div class="label">Other Name</div>
                            <div class="value">'.ucfirst(Utility::escape($data->oname)).'</div>
                        </div>
                        <div>
                            <div class="label">Date Of Birth</div>
                            <div class="value">'.Utility::formatDate($data->dob).'</div>
                        </div>
                    </div>
                    <div>
                        <div>Contacts</div>
                        <div>
                            <div class="label">Phone</div>
                            <div class="value">'.Utility::escape($data->phone).'</div>
                        </div>
                        <div>
                            <div class="label">Email</div>
                            <div class="value">'.Utility::escape($data->email).'</div>
                        </div>
                        <div>
                            <div class="label">Address</div>
                            <div class="value">'.Utility::escape($data->address).'</div>
                        </div>
                    </div>
        <div>
            <div>Application Information</div>
            <div>
                <div class="label">Application ID</div>
                <div class="value">'.strtoupper($admId).'</div>
            </div>
            <div>
                <div class="label">School</div>
                <div class="value">'.School::getFullName($school).'</div>
            </div>
            <div>
                <div class="label">Level</div>
                <div class="value">'.School::getLevelName($school, $data->level).'</div>
            </div>
            
            <div>
                <div class="label">Screening Score</div>
                <div class="value">'.$data->score.'</div>
            </div>
           
            <div>
                <div class="label">Admission Status</div>
                <div class="value">'.getStatus().'</div>
            </div>
        </div>
        <div>
            <div>Guardians Information</div>
            <div>
                <div class="label">Father Name</div>
                <div class="value">'.Utility::escape($data->fathername).'</div>
            </div>
            <div>
                <div class="label">Mother Name</div>
                <div class="value">'.Utility::escape($data->mothername).'</div>
            </div>
        </div>
    </main>
               ';
               
        require_once '../../libraries/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($body);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0; 

	//call watermark content and image
	$mpdf->SetWatermarkText(School::getFullName($sch_abbr));
	$mpdf->showWatermarkText = true;
	$mpdf->watermarkTextAlpha = 0.1;
       //output in browser
	$mpdf->Output();	
   }
	
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admission Application</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/apply.css',5))?>" />
</head>
<body>
    <main>
        <?php require_once './nav.inc.php';?>
        <div>
             <img src="<?php echo $url->to('apm/uploads/logo/'. Utility::escape($adm->getLogo($data->sch_abbr)),1)?>" alt="picture" width="100" height="100"/>
             <h2>Application Summary</h2>
             <div>
                <img src="<?php echo $url->to('uploads/passports/'. Utility::escape($data->picture),5)?>" alt="picture" width="100" height="100"/>
            </div>
        </div>
        <div>
            <h3>Bio Details</h3>    
            <div>
                <div class="label">First Name</div>
                <div class="value"><?php echo ucfirst(Utility::escape($data->fname)); ?></div>
            </div>
            <div>
                <div class="label">Last Name</div>
                <div class="value"><?php echo ucfirst(Utility::escape($data->lname)); ?></div>
            </div>
            <div>
                <div class="label">Other Name</div>
                <div class="value"><?php echo ucfirst(Utility::escape($data->oname)); ?></div>
            </div>
            <div>
                <div class="label">Date Of Birth</div>
                <div class="value"><?php echo Utility::formatDate($data->dob);?></div>
            </div>
        </div>
        <div>
            <h3>Contacts</h3>
            <div>
                <div class="label">Phone</div>
                <div class="value"><?php echo Utility::escape($data->phone); ?></div>
            </div>
            <div>
                <div class="label">Email</div>
                <div class="value"><?php echo Utility::escape($data->email); ?></div>
            </div>
            <div>
                <div class="label">Address</div>
                <div class="value"><?php echo Utility::escape($data->address); ?></div>
            </div>
        </div>
        <div>
            <h3>Application Information</h3>
            <div>
                <div class="label">Application ID</div>
                <div class="value"><?php echo strtoupper($admId); ?></div>
            </div>
            <?php $school = Utility::escape($data->sch_abbr);?>
            <div>
                <div class="label">School</div>
                <div class="value"><?php echo School::getFullName($school); ?></div>
            </div>
            <div>
                <div class="label">Level</div>
                <div class="value"><?php echo School::getLevelName($school, $data->level); ?></div>
            </div>
            
            <div>
                <div class="label">Screening Score</div>
                <div class="value"><?php echo $data->score ?></div>
            </div>
           
            <div>
                <div class="label">Admission Status</div>
                <div class="value"><?php echo getStatus();?></div>
            </div>
        </div>
        <div>
            <h3>Guardians Information</h3>
            <div>
                <div class="label">Father Name</div>
                <div class="value"><?php echo Utility::escape($data->fathername); ?></div>
            </div>
            <div>
                <div class="label">Mother Name</div>
                <div class="value"><?php echo Utility::escape($data->mothername); ?></div>
            </div>
        </div>
        <button><a href="<?php echo Utility::myself().'?adm_id='.$data->adm_id.'&download=true'?>">Download</a></button>
    </main>
    <style>
        
    </style>
    <script>
        window.onload = function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
            appendScript("scripts/application.js");
        }

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>

