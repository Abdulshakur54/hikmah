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
    require_once "../../libraries/vendor/autoload.php";
    $msg = '';
    //check if user has already applied
    $show = true; //helps to detect when to show other part of the page or not
    if($adm->hasApplied($id)){
        $msg = '<div class="success">You have already applied for admission <a href="application.php?adm_id='.$id.'">View My Application</a></div>';
        $show = false;
    }else{
        if(Input::submitted() && Token::check(Input::get('token'))){
            $val  = new Validation();
            $formvalues = array(
                    'fatherName' => array('name'=>'FatherName', 'required'=>true, 'min'=>3, 'max'=>'50', 'pattern' => '[a-zA-z` ]+$'),
                    'motherName' => array('name'=>'MotherName', 'required'=>true, 'min'=>3, 'max'=>'50', 'pattern' => '[a-zA-z` ]+$'),
                    'phone'=>array('name'=>'Phone','pattern'=>'^(080|070|090|081|091|071)[0-9]{8}$'),
                    'email'=>array('name'=>'Email','max'=>70,'min'=>10,'pattern'=>'^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$')
            );
            $fileValues = [
                'picture'=>[
                'name'=>'Picture',
                'required'=>true,
                'maxSize'=>100,
                'extension'=>['jpg','jpeg','png']
                ]
            ];
            $file = new File('picture');
            if(!$file->isUploaded()){
                $msg.='Picture not uploaded';
            }
            if($val->check($formvalues) && $val->checkFile($fileValues) && Utility::noScript(Input::get('address'))){
                $utils = new Utils();
                //apply for admission
                $fatherName = Utility::escape(Input::get('fatherName'));
                $motherName = Utility::escape(Input::get('motherName'));
                $phone = Utility::escape(Input::get('phone'));
                $email = Utility::escape(Input::get('email'));
                $address = Input::get('address');
                $ext = $file->extension();
                $pictureName = $data->sch_abbr.$utils->getSessionYearAbbreviation($data->sch_abbr).$data->adm_id.'.'.$ext;
                if($adm->apply($fatherName, $motherName, $phone, $email,$pictureName,$address)){ //apply for admission
                    $file->move('uploads/passports/'.$pictureName); //move picture to the destination folder
                    //notify the APM(s) and Islamiyah Coordinators
                    $alert = new Alert(true);
                    $message = '<p>This is to inform you that '.
                    Utility::formatName($data->fname, $data->oname, $data->lname).
                    ' has successfully completed his application</p><p>The Applicant would like to be admitted into '.
                    School::getLevelName($data->sch_abbr, $data->level).' of '.School::getFullName($data->sch_abbr).
                    '</p><p>You can view the application <a href="application.php?adm_id='.$data->adm_id.'">here</a></p>';
                    $title = 'Application for Admission';
                    if($admRank == 11){
                        $alert->sendToRank(2, $title, $message);
                    }else{
                        if($admRank == 12){
                             $alert->sendToRank(4, $title, $message);
                        }
                    }
                   
                    
                    //send email
                    $message = '<p>We recieved your application seeking admission into <em>'.School::getLevelName($sch_abbr, $level).', '.School::getFullName($sch_abbr).'</em></p><p>We are going through it. Expect to hear from us soon</p>'; //email body
         
                $mail = new Email();
                $mail->send($email, 'Application Recieved', $message);
                     Session::set_flash('admissionApplication','<div class="success"><p>You have Successfully submitted your application</p>
                     <p>The Admission committe would look into it, you would be notified of their decision via email</p><p><a href="application.php?adm_id='.$data->adm_id.'">View My Application</a></p></div>');
                    Redirect::to('success.php?appliedAdmission=true');
                }else{
                    $msg.='<div class="failure">Something went wrong while submitting your application</div>'; //output message
                }   

            }else{
                    foreach($val->errors() as $val){
                            $msg .= $val . '<br>';
                    }
            }

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
	<title>Admission Application</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/apply.css',5))?>" />
</head>
<body>
    <main>
        <?php
        require_once './nav.inc.php';
            if(!$show){
                echo $msg;
                exit();
            }
        ?>
        <form method="post" action="<?php echo Utility::myself()?>" onsubmit="return sumbitData()" enctype="multipart/form-data">
            <div class="formhead">Complete Your Application</div>
            <div>
                <label for = "address">Residential Address</label>
                <div>
                    <textarea id="address" name="address"><?php echo (Utility::noScript(Input::get('address')))?Input::get('address'):''; ?></textarea>
                </div>
            </div>
            <div>
                <label for = "picture" id="uploadTrigger" style="cursor: pointer; color:green;">Upload Picture</label>
                <div>
                    <input type="file" name="picture" id="picture" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png"/>
                    <img id="image" width="100" height="100"/>
                    <input type="hidden" name="hiddenPic" value="" id="hiddenPic"/>
                    <div id="picMsg" class="errMsg"></div>
                </div>
            </div>
            <div>
                <label for = "fatherName">Father's Name</label>
                <div>
                    <input type="text" name="fatherName" id="fatherName" value="<?php echo Utility::escape(Input::get('fatherName'))?>"/>
                    <div id="fatherNameMsg" class="errMsg"></div>
                </div>
            </div>
           <div>
                <label for = "motherName">Mother's Name</label>
                <div>
                    <input type="text" name="motherName" id="motherName" value="<?php echo Utility::escape(Input::get('motherName'))?>"/>
                    <div id="motherNameMsg" class="errMsg"></div>
                </div>
            </div>
            <div>
                <label for = "phone">Phone No(Parent)</label>
                <div>
                    <input type = "text" maxlength="11" name = "phone" value = "<?php echo Utility::escape(Utility::altValue(Input::get('phone'), $data->phone))?>" id = "phone"> <span class="smallNotification"> for receiving messages</span>
                    <div id="phoneMsg" class="errMsg"></div>
                </div>
            </div>

            <div>
                <label for = "email">Email(Parent)</label>
                <div>
                    <div><input type = "email" maxlength="50" name = "email" value = "<?php echo Utility::escape(Utility::altValue(Input::get('email'), $data->email))?>" id = "email"> <span class="smallNotification"> for receiving emails</span>
                    <div id="emailMsg" class="errMsg"></div>
                </div>
            </div>
            <div id="genMsg"><?php echo '<div class="failure">'.$msg.'</div>'; ?></div>
            <div>
                <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
                <button id="applyBtn">Apply</button>
           </div>
        </form>
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