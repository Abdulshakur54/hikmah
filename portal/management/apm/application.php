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
    $msg = '';
    if(!(Input::submitted('get') && !empty(Input::get('adm_id')))){
        Redirect::to(404);
    }else{
        $admId = Utility::escape(Input::get('adm_id'));
    }
   if($rank === 2){
       $adm = new Admission();
       $data = $adm->getData($admId);
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
        <?php require_once '../nav.inc.php'?>
        <div>
            <div>Bio Details</div>
            <div>
                <img src="<?php echo $url->to('uploads/passports/'. Utility::escape($data->picture),5)?>" alt="picture" width="100" height="100"/>
            </div>
            <div>
                <div class="label">First Name</div>
                <div class="value"><?php echo ucwords(Utility::escape($data->fname)); ?></div>
            </div>
            <div>
                <div class="label">Last Name</div>
                <div class="value"><?php echo ucwords(Utility::escape($data->lname)); ?></div>
            </div>
            <div>
                <div class="label">Other Name</div>
                <div class="value"><?php echo ucwords(Utility::escape($data->oname)); ?></div>
            </div>
        </div>
        <div>
            <div>Contacts</div>
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
            <div>Application Information</div>
            <div>
                <div class="label">Application ID</div>
                <div class="value"><?php echo $admId; ?></div>
            </div>
            <?php $school = strtoupper(Utility::escape($data->sch_abbr));?>
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
                <div class="value"><?php $data->score; ?></div>
            </div>
            <div>
                <div class="label">Admission Status</div>
                <div class="value">Awaiting Admission</div>
            </div>
        </div>
        <div>
            <div>Guardians Information</div>
            <div>
                <div class="label">Father Name</div>
                <div class="value"><?php echo ucwords(Utility::escape($data->fathername)); ?></div>
            </div>
            <div>
                <div class="label">Mother Name</div>
                <div class="value"><?php echo ucwords(Utility::escape($data->mothername)); ?></div>
            </div>
        </div>
        <div><a href="admission_decision.php">Decision Page</a></div>
        
    </main>
    <script>
        window.onload = function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
            appendScript("scripts/apply.js");
        }

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>

