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
    if(Input::submitted() && Token::check(Input::get('token'))){
        $decision = Utility::escape(Input::get('decision'));
        if($decision === 'accept'){
            $std = new Student();
            $newId = $std->genId($sch_abbr,$data->level);
            $newPwd = Token::create(5);
            switch ($data->rank){
                case 11:
                    $newRank = 9;
                case 12:
                    $newRank = 10;
            }
            $hashedNewPwd = password_hash($newPwd, PASSWORD_DEFAULT);
            $newPermission = Student::getDefaultPermission();
            if(!empty($data->picture)){
                $picExt = explode('.', $data->picture)[1];
                $newPicture = str_replace('/', '', $newId).'.'.$picExt;
            }else{
                $newPicture = '';
            }
            
            
            $sql1 = 'insert into student(std_id,password,fname,lname,oname,rank,permission,active,sch_abbr,level,picture)'.
                    'values(?,?,?,?,?,?,?,?,?,?,?)';
            $val1 = [$newId,$hashedNewPwd, Utility::escape($data->fname), Utility::escape($data->lname), Utility::escape($data->oname),$newRank,$newPermission, true,$sch_abbr,$data->level,$newPicture];
            $email =  Utility::escape($data->email);
            $sql2 = 'insert into student2(std_id,fathername,mothername,dob,address,phone,email) values(?,?,?,?,?,?,?)';
            $val2 = [$newId, Utility::escape($data->fathername), Utility::escape($data->mothername),$data->dob, Utility::escape($data->address), Utility::escape($data->phone),$email];
            $sql3 = 'insert into student3(std_id) values(?)';
            $sql4 = 'insert into student_psy(std_id) values(?)';
            //delete data from admission table
            $sql5 = 'delete from admission where id = ?';
            //transfer details to student tables
            $db = DB::get_instance();

            if($db->trans_query([[$sql1,$val1],[$sql2,$val2],[$sql3,[$newId]],[$sql4,[$newId]],[$sql5,[$id]]])){
                
                if(!empty($newPicture)){
                    $oldPicDtn = './uploads/passports/'.$data->picture;
                    $newPicDtn = '../student/uploads/passports/'.$newPicture;
                    //transfer applicants passport
                    rename($oldPicDtn, $newPicDtn); 
                }  
                
                //send email to the applicant with his studentid and password in the mail
                $message = '<p>You have accepted our offer!. You are officially a student at '.School::getFullName($sch_abbr).'</p><p>Your Student Login details are show below<p>Username: <span>'.$newId.'</span></p><p>'
                        . 'Password: <span>'.$newPwd.'</span></p> <p>It is recommended to change your password after login. <a href="'.$url->to('changepassword.php',3).'">Login and change password</a></p><p>Your data has also been transfered to the student portal.<br>This can be accessed at <a href="'.$url->to('index.php',3).'">'.$url->to('index.php',3).'</a></p>'; //email 
                $mail = new Email();
                $mail->send($email, 'Your Login Details', $message);
                Session::set_flash('acceptAdmission', 'You have successfully accepted our offer. Your Details have now been transfered to another portal. An email containing your login details to that portal has been sent to you');
                Redirect::home('success.php?acceptAdmission=true',5);
                
            }else{
                $msg = 'An Error prevents you from accepting our admission';
            }
            
        }
        if($decision === 'decline'){
            $db = DB::get_instance();
            $db->query('update admission set status = ? where id = ?',[3,$id]); //declines admission
            Redirect::to('admission_decision.php'); //redirect back to same page, a form of refreshing
        }
         if($decision === 'download'){
            $body = '
                <main>
                   <h2>OFFER OF ADMISSION</h2>
                   <p>Congratulations! '.Utility::formatName($data->fname, $data->oname, $data->lname).' with Applicant ID: '.$data->adm_id.'</p><p>You have been offered admission into '.School::getLevelName($sch_abbr, $level).', '.School::getFullName($sch_abbr).'</p>
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
    }   
	
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admission Decision</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/admission_decision.css',5))?>" />
</head>
<body>
    <main>
        <?php
        require'./nav.inc.php';
         //check if user has been given admission
        $admStatus = $data->status;
        switch ($admStatus){
            case 0:
                echo '<h2>ADMISSION DECISION</h2>';
                echo '<p>A decision is yet to be made on your Application</p>';
                echo '<p>Please, check back later</p>';
                break;
            case 1:
                echo '<form method="post" onsubmit="return false">';
                echo '<h2>OFFER OF ADMISSION</h2>';
                echo '<p>Congratulations! '.Utility::formatName($data->fname, $data->oname, $data->lname).' with Applicant ID: '.$data->adm_id.'</p><p>You have been offered admission into '.School::getLevelName($sch_abbr, $level).', '.School::getFullName($sch_abbr).'</p>';
                echo '<input type="hidden" id="token" value="'.Token::generate().'"  name="token"/>';
                echo '<input type="hidden" id="decision" name="decision" />';
                echo '<button onclick ="acceptAdmission()" name="acceptBtn">Accept</button> <button onclick ="declineAdmission()" name="declineBtn">Decline</button> <button onclick ="downloadAdmission()" name="downloadBtn">Download</button>';
                echo '</form>';
                break;
            case 2:
                echo '<h2>ADMISSION DECISION</h2>';
                echo '<p>We are sad to inform you that your application was not successful</p>';
                echo '<p>Please, try again next session</p>';
                break;
             case 3:
                echo '<h2>ADMISSION DECISION</h2>';
                echo '<p>You have decline our offer of admission</p>';
                echo '<p>You can only try again next session</p>';
                break;
        }
        
        ?>
    </main>
    <script>
        window.onload = function(){
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
            appendScript('scripts/admission_decision.js'); 
        };

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>

