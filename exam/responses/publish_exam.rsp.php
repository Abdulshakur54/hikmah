<?php

    //initializations

	spl_autoload_register(

		function($class){

			require_once'../../classes/'.$class.'.php';

		}

	);

	session_start(Config::get('session/options'));

	//end of initializatons



    $mgt = new Management();

    $staff = new Staff();

    $std = new Student();

    $user = null;
    
    $url = new Url();

    $message='';

    if($mgt->isRemembered()){

        $user = $mgt;

    }

    if($staff->isRemembered()){

        $user = $staff;

    }

	if(!isset($user)){ //ensure user is legally logged in

        Redirect::to('index.php'); //redirect to exam home page

	} 



    header("Content-Type: application/json; charset=UTF-8");

    if(Input::submitted() && Token::check(Input::get('token'))){

        $ids = json_decode(Input::get('idarr'));

        $idArr = $ids->id;

        $arrLen = count($idArr);

        $examId = Utility::escape(Input::get('examid'));

        $type = Utility::escape(Input::get('type'));

        $instruction = Input::get('instruction');
        $transfer = Input::get('transfer');
        $exam = new Exam();

        $examDetails = $exam->getDetails($examId);

        if(!$exam->exists($examId) || !Utility::noScript($instruction)){ //ensures that the exam exists and instruction is valid

            exit();

        }

        //code below confirms that each id exist

        $db = DB::get_instance();

        

         switch($type){

            case 11:

                $table = Config::get('users/table_name3');

                $id = Config::get('users/username_column3');

            break;

            case 12:

                $table = Config::get('users/table_name3');

                $id = Config::get('users/username_column3');

            break;

            case 7:

                $table = Config::get('users/table_name1');

                $id = Config::get('users/username_column1');

            break;

            case 15:

                $table = Config::get('users/table_name1');

                $id = Config::get('users/username_column1');

            break;

            case 9:

                $table = Config::get('users/table_name2');

                $id = Config::get('users/username_column2');

            break;

            case 10:

                $table = Config::get('users/table_name2');

                $id = Config::get('users/username_column2');

            break;

        }



        function valIds(){

            global $db, $id, $table, $idArr, $arrLen;

            $db->query('select count('.$id.') as counter from '.$table.' where '.$id.' = ?',[$idArr[0]]);

            if($db->one_result()->counter > 0){

                for($i=2;$i<$arrLen;$i++){

                    $db->requery([$idArr[$i]]);

                    if(!$db->one_result()->counter > 0){

                        return false;

                    }    

                }

                return true;

            }

            return false;

                

        }



        if(!valIds()){

            exit(); //ensure that all the ids are valid ids(are ids from the database)

        }        

        

        //end of code that confirms id



        $sql1 = 'update ex_exam set published = ?, instruction = ? where exam_id =?';

        $idAssArr =[];
        
        foreach($idArr as $val){ //populate $idAssarr with the id as key and 1 as the value

            $idAssArr[$val] = 1;

        } 

        $sql2 = 'insert into ex_available_exam(exam_id,examinees) values(?,?)';

        $idString = json_encode($idAssArr);

       

        if($db->trans_query([[$sql1,[true,$instruction,$examId]],[$sql2,[$examId,$idString]]])){ //query the database using a transaction

            $date = strtotime($examDetails->expiry);

            $title = strtoupper($examDetails->title);

            $format = Utility::numSuffix(date("d", $date)); 

            

            $start = false;
            
            if($transfer === 'true'){
                $alert = new Alert(true);
                 foreach($idArr as $val){ //sends alert notification to each of the examinee
                    $link = $url->to('ongoing_exam.php?examid='.$examId,4);
                    $alert->send($val,'Exam Alert','You are expected to take your exam, '.$examId.'('.$title.') before '.date("D. d", $date).$format.date(" F, Y (h:i:sa)", $date).'<br>You can click <a href="'.$link.'">here</a> to take exam',$start); //formats the message and time contained in the message
                    $start = true; //this will enable it to be requeried in the Alert class

                }
            }else{
                 $alert = new ExamAlert(true);
                 foreach($idArr as $val){ //sends alert notification to each of the examinee

                    $alert->send($val,'Exam Alert','You are expected to take your exam, '.$examId.'('.$title.') before '.date("D. d", $date).$format.date(" F, Y (h:i:sa)", $date).'<br>You can click <a href="ongoing_exam.php?examid='.$examId.'">here</a> to take exam',$start); //formats the message and time contained in the message

                    $start = true; //this will enable it to be requeried in the Alert class

                }
            }
            
           

            //message indicating that the exam have been published

            echo json_encode(['statuscode'=>1,'token'=>Token::generate()]);

        }else{

            //message indicating that the exam was not published

            echo json_encode(['statuscode'=>2,'token'=>Token::generate()]);

        } 

       

    }