<?php
    //initializations
      spl_autoload_register(
              function($class){
                      require_once'../../../../classes/'.$class.'.php';
              }
      );
      session_start(Config::get('session/options'));
      require_once "../../../../libraries/vendor/autoload.php"; //loading php libraries from composer
      //end of initializatons
     header("Content-Type: application/json; charset=UTF-8");
     $url = new Url();
     if(Input::submitted() && Token::check(Input::get('token'))){
        $idObj = json_decode(Input::get('idarr'));
        $idArr = $idObj->id;
        $token = Input::get('token');
        $type = Utility::escape(Input::get('type'));
        $db = DB::get_instance();
        $db2 = DB::get_instance2();
        $mail = new Email();
        if($type === 'accept'){
            $start = false;
            if(count($idArr)){
                    foreach ($idArr as $id){
                    if(!$start){
                        $db->query('update admission set status = 1 where adm_id=?',[$id]); //prepare the query so it can be requeried
                        /*get Applicant Email */
                        $db2->query('select email,fname,oname,lname,sch_abbr,level from admission where adm_id=?',[$id]);
                        $appData = $db2->one_result();
                        $appEmail = Utility::escape($appData->email);
                        $appFname = Utility::escape($appData->fname);
                        $appLname = Utility::escape($appData->lname);
                        $appOname = Utility::escape($appData->oname);
                        $appSchool = Utility::escape($appData->sch_abbr);
                        $appLevel = $appData->level;

                        $body = '<p>Congratulations! You have been offered admission to <em>'.School::getLevelName($appSchool, $appLevel).', '.School::getFullName($appSchool).'</em></p>'.
                                '<p>Kindly click on this link <a href="'.$url->to('admission_decision.php',5).'"> My Response to your offer</a> to respond to our offer.</p>';
                        $mail->send($appEmail, 'Offer of Admission', $body);
                    }else{
                        $db->requery([$id]);
                        sendEmail($id);
                        $start = true;
                    }
                
                }
                echo json_encode(['success'=>true,'token'=>Token::generate()]);
                exit();
            }
            
        }else{
            if($type==='decline'){
                if(count($idArr)){
                    $start = false;
                    foreach ($idArr as $id){
                        if(!$start){
                            $db->query('update admission set status = 2 where adm_id=?',[$id]); //prepare the query so it can be requeried
                            /*get Applicant Email */
                            $db2->query('select email,fname,oname,lname,sch_abbr,level from admission where adm_id=?',[$id]);
                            $appData = $db2->one_result();
                            $appEmail = Utility::escape($appData->email);
                            $appFname = Utility::escape($appData->fname);
                            $appLname = Utility::escape($appData->lname);
                            $appOname = Utility::escape($appData->oname);
                            $appSchool = Utility::escape($appData->sch_abbr);
                            $appLevel = $appData->level;
                            $body = '<p>We Regret to inform you that your application to gain entry into  <em>'.School::getLevelName($appSchool, $appLevel).', '.School::getFullName($appSchool).'</em> is unsuccessful. Please try again next session</p>';
                            $mail->send($appEmail, 'Admission Decision', $body);
                        }else{
                            $db->requery([$id]);
                            sendEmail($id);
                            $start = true;
                        }        
                    }
                    echo json_encode(['success'=>true,'token'=>Token::generate()]);
                    exit();
                }
            }
        }
        echo json_encode(['success'=>false,'token'=>Token::generate()]);
        
     }
    