<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
      require_once '../../../libraries/vendor/autoload.php';
      use PhpOffice\PhpSpreadsheet\IOFactory as IOObj;  
      require_once '../nav1.inc.php';
      require_once '../sub_teacher.inc.php';

      header("Content-Type: application/json; charset=UTF-8");
    if(Input::submitted() && Token::check(Input::get('token'))){
        $msg = '';
        $utils = new Utils();
        $table = $utils->getFormatedSession($sch_abbr).'_score';
        $scoreSettings = $staff->getScoreSettings($sch_abbr);
        $subject = new Subject($subId,$table,$scoreSettings);

        $val = new Validation();

        $fileUploaded = false;
        
         if(!$val->checkFile([

            'uploadedFile'=>[

                'name'=>'Uploaded File',

                'required'=>true,

                'maxSize'=>15000,

                'extension'=>['xlsx','xls']

            ]

        ])){

             foreach($val->errors() as $error){

                 $msg.=$error.'<br/>';

             }

        }else{
            $fileHandler = new File('uploadedFile');
            $title = $subName.' '. School::getLevName($sch_abbr, $subLevel).$subClass.' Scoresheet_upload'; //this refers to something like 'mathematics 2a scoresheet
            $uploadPath = '../uploads/scores/'.$title;
            $destination = $uploadPath.'.'.$fileHandler->extension();
            $fileHandler->move($destination);

            $fileUploaded = true;



            $ext = IOObj::identify($destination); //identify the extension of the file

            $reader = IOObj::createReader($ext); //creates a reader for the identified extension

            $reader->getReadDataOnly(); //set the reader to read data only ignoring the data formatting

            $spreadsheet = $reader->load($destination); //loads the uploaded file
            
            $totalNoOfStudents = $subject->getStudentCount();   
            $alphaColumns = ['C','D','E','F','G','H']; //this represents the name of the excel columns
            /*getting the required column*/
            $columns = $subject->getNeededColumns($sch_abbr); //this returns an array  of the needed columns
            /*
            * replace exam index with ex and store in another variable, this is done for compatibility for the column names in the different tables
            */
            $pos = array_search('exam', $columns);
            $scoreColumns = $columns;
            if($pos !== false){ //false is used for comparison because 0 is a valid value in this scenario
                $scoreColumns[$pos] = 'ex';
            }


            $endCellAlphabet = $alphaColumns[count($scoreColumns)-1]; //gets the end cell alphabet horizontally
            $endRow = $totalNoOfStudents + 2; //2 is added because the first 2 rows are headers
            $endCell = $endCellAlphabet.$endRow;
            $valArray = $spreadsheet->getActiveSheet()->rangeToArray('A3:'.$endCell,null,false,false,true); //returns the activesheet of the spreadsheet
            
            $usedAlphaColumns = array_slice($alphaColumns,0,count($scoreColumns),true);
            

            //validate the data
            $errors = [];
            $row = 3; //this is the row score enterings starts from
            foreach($valArray as $val){
                if(!$subject->isRegistered($val['A'])){
                    $errors[] = 'Row '.$row.': ID not registered';
                }
                $x=0;
                foreach($usedAlphaColumns as $usedAlphaColumn){
                    $score = $val[$usedAlphaColumn];
                    if($score != ''){
                        if(!preg_match('/^[1-9]{1,2}0{0,2}(\.[0-9]{1,2})?$/',$score)){
                            $errors[] ='Row '.$row.': Invalid '.strtoupper($scoreColumns[$x]);
                        }
                    
                        if($score > $scoreSettings[$columns[$x]]){
                            $errors[] = 'Row '.$row.': '.strtoupper($columns[$x]).' Exceeds Maximum value';
                        }
                        $x++;
                    }  
                }
                $row++;
            }

            
            if(empty($errors)){
                //update score table
                $lenght = count($usedAlphaColumns);
                foreach($valArray as $val){
                    $scoreArr = array_slice($val,2,$lenght,true);
                    $subject->update2($val['A'],$scoreArr,$currTerm);
                }
                Session::set_flash('import','success');
                echo json_encode(['statuscode'=>1,'token'=>Token::generate()]);
            }else{
                 echo json_encode(['statuscode'=>0,'token'=>Token::generate(),'errors'=>$errors]);
            }
             
        }


    }
    
  
