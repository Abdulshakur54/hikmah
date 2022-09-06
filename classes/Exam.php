<?php

    class Exam{
        private $_db;

        public function __construct(){
            $this->_db = DB::get_instance();
        }
        
        //this method adds an exam to the ex_exam table
        public function add($examId, $title, $username, $noOfQtnsAdd, $noOfQtns, $passMark, $duration, $count, $expiryDate, $expiryTime,$transfer) :bool{
            return $this->_db->query('insert into ex_exam (exam_id, title, examiner_id, no_qtn_added, no_qtn_req, pass_mark, duration, count, expiry,transfer) values(?,?,?,?,?,?,?,?,?,?)',
            [$examId, $title, $username, $noOfQtnsAdd, $noOfQtns, $passMark, $duration, $count, $expiryDate.' '.$expiryTime,$transfer]);
        }

        //this method edits the ex_exam table
        public function edit($examId, $noOfQtnsAdd, $noOfQtns, $passMark, $duration, $count, $expiryDate, $expiryTime) :bool{
            return $this->_db->query('update ex_exam set no_qtn_added=?, no_qtn_req=?, pass_mark=?, duration=?, count=?, expiry=? where exam_id = ?',
            [$noOfQtnsAdd, $noOfQtns, $passMark, $duration, $count, $expiryDate.' '.$expiryTime, $examId]);
        }


         //this method edits the the passMark for a particular exam
         public function editPassMark($examId, $expiry, $passMark) :bool{
            return $this->_db->query('update ex_exam set pass_mark = ?, expiry=? where exam_id = ?',
            [$passMark, $expiry, $examId]);
        }


        //this method checks if an examId exists in the ex_exam table
        public function idExists($examId) :bool{
            $agg = new Aggregate();
            return $agg->exists($examId,'exam_id','ex_exam');
        }

        public function updatePassedStatus($examId, $passMark){
            $this->_db->query('update ex_completed set passed =? where exam_id =? and ptg_score >= ?',[true,$examId,$passMark]);
            $this->_db->query('update ex_completed set passed =? where exam_id =? and ptg_score < ?',[false,$examId,$passMark]);
        }

        //this method returns all the data associated with an exam from the ex_exam table
        public function getDetails($examId){
            $this->_db->query('select * from ex_exam where exam_id = ?',[$examId]);
            return $this->_db->one_result();
        }

       public function isPublished($examId) :bool{
            $this->_db->query('select published from ex_exam where exam_id = ?',[$examId]);
            return $this->_db->one_result()->published;
       }

       public function isExaminer($examId,$examinerId) :bool{
            $this->_db->query('select count(id) as counter from ex_exam where exam_id=? and examiner_id=?',[$examId,$examinerId]);
            return (($this->_db->one_result()->counter) > 0)?true:false;
       }



       //this method returns the total no of examinees for an exam
       //$part represent those that have participated while $notPart represents otherwise
       public function getExamineeCount($examId) :int{
            $this->_db->query('select examinees from ex_available_exam where exam_id=?',[$examId]);
            $resArr = json_decode($this->_db->one_result()->examinees,true);
            $notPart = count($resArr);
            $this->_db->query('select count(id) as counter from ex_completed where exam_id=?',[$examId]);
            $part = $this->_db->one_result()->counter;
            return ($notPart + $part);
       }
       
       

       //this method returns the total no of examiness that have completed their exams
       public function getCompletedExamCount($examId) :int{
            $this->_db->query('select count(id) as counter from ex_completed where exam_id=?',[$examId]);
            return $this->_db->one_result()->counter;
       }


       //this method returns the total no of examiness that have passed a particular exam
       public function getExamineePassCount($examId) :int{
            $this->_db->query('select count(id) as counter from ex_completed where exam_id=? and passed = ?',[$examId, true]);
            return $this->_db->one_result()->counter;
       }


       //this method returns the total no of examiness that have failed a particular exam
       public function getExamineeFailCount($examId) :int{
            $this->_db->query('select count(id) as counter from ex_completed where exam_id=? and passed = ?',[$examId, false]);
            return $this->_db->one_result()->counter;
       }


       //this method checks if an exam has expired
       public function isExpired($examId) :bool{
           $this->_db->query('select expiry from ex_exam where exam_id=?',[$examId]);
            if(strtotime($this->_db->one_result()->expiry) <= time()){
                return true;
            }
            return false;
       }

       //this function returns examinee data
       function chooseExamineeData($examineeRank, $examineeTable, $usernameCol,$condition = null){
            if(!empty($condition)){
                $con = explode(',', $condition);
                if($this->_db->query('select '.$usernameCol.',fname,lname,oname from '.$examineeTable.' where rank = ? and '.$con[0].$con[1].'? order by fname',[$examineeRank, $con[2]])){
                    return $this->_db->get_result();
                }
            }else{
                if($this->_db->query('select '.$usernameCol.',fname,lname,oname from '.$examineeTable.' where rank = ? order by fname',[$examineeRank])){
                    return $this->_db->get_result();
                }
            }
            
            return;  
       }
       
        //this function is same as chooseExamineeData except that it is customized for APM when he sets exam for admission screening
       function chooseAdmissionExamineeData($examineeRank, $examineeTable, $usernameCol, $condition = null, $anotherCondition = null){
           $sql = 'select '.$usernameCol.',fname,lname,oname,sch_abbr,level from '.$examineeTable.' where rank = ? and applied =? and status = ?';
           $val = [$examineeRank,true,0];  //this will be the query parameter
           
            if(!empty($condition)){
                $con = explode(',', $condition);
                $anotherCon = explode(',', $anotherCondition); 
                if($con[2] !== 'ALL'){
                    $sql.=' and '.$con[0].$con[1].'?';
                    $val[] = $con[2]; //add to the query parameter
                }  
            }
            
            if(!empty($anotherCondition)){
                if($anotherCon[2] !== 'ALL'){
                    $sql.=' and '.$anotherCon[0].$anotherCon[1].'?';
                    $val[] = $anotherCon[2]; //add to the query parameter
                }
            }
            
            $sql.=' order by fname'; 
            if($this->_db->query($sql,$val)){
                return $this->_db->get_result();
            }
       }
       
       
       //this function is used to get data of students that are eligible for a teacher exam initiated via the school portal not the exam portal
       public function chooseTeacherExamineeData($scoreTable, $usernameCol,$subId){
           $this->_db->query('select student.fname,student.oname,student.lname,student.'.$usernameCol.' from student inner join '.$scoreTable.' on student.'.$usernameCol.'='.$scoreTable.'.'.$usernameCol.' where '.$scoreTable.'.subject_id=?',[$subId]);
           if($this->_db->row_count() > 0){
            return $this->_db->get_result();
           }
           return [];
          
       }


       public function publish($examId){
           $this->_db->query('update ex_exam set published = ? where exam_id = ?',[true,$examId]);
       }

       //checks if an exam exists
       public function exists($examId){
            $this->_db->query('select count(id) as counter from ex_exam where exam_id = ?',[$examId]);
            return (($this->_db->one_result()->counter) > 0)?true:false;
        }

        //returns unpublished Exams for a particular examiner
        public function getUnpublished($examinerId){
            $this->_db->query('select * from ex_exam where examiner_id = ? and published = ?',[$examinerId, false]);
            return $this->_db->get_result();
        }


         //returns published Exams for a particular examiner
         public function getPublished($examinerId){
            $this->_db->query('select * from ex_exam where examiner_id = ? and published = ?',[$examinerId, true]);
            return $this->_db->get_result();
        }



        public function delete($examId) :bool{
            $sql1 = 'delete from ex_exam where exam_id=?';
            $sql2 = 'delete from ex_question where exam_id=?';
            return $this->_db->trans_query([[$sql1,[$examId]],[$sql2,[$examId]]]);
        }

        public function deletePubExam($examId) :bool{
            $sql1 = 'delete from ex_exam where exam_id=?';
            $sql2 = 'delete from ex_question where exam_id=?';
            $sql3 = 'delete from ex_available_exam where exam_id=?';
            $sql4 = 'delete from ex_completed where exam_id=?';
            $sql5 = 'delete from ex_theory where exam_id=?';
            return $this->_db->trans_query([[$sql1,[$examId]], [$sql2,[$examId]], [$sql3,[$examId]], [$sql4,[$examId]], [$sql5,[$examId]]]);
        }


        //this method helps to return the available exam for a particular examinee as an array of objects
        public function getAvailableExams($examineeId){
            $examIds = []; //this is to hold all the available exam Ids
            $this->_db->query('select * from ex_available_exam');
            if($this->_db->row_count() > 0){
                $resArr = $this->_db->get_result();
                foreach($resArr as $resObj){
                    $examineesId = array_keys(json_decode($resObj->examinees,true)); //returns an array of the keys
                    if(in_array(strtoupper($examineeId), $examineesId)){
                        $examIds[] = $resObj->exam_id;
                    }
                }
                $examCount = count($examIds); //this is to help know the no of '?' to be generated in the sql statement
                $sql = 'select * from ex_exam where exam_id IN(';
                for($i=1;$i<=$examCount;$i++){
                    $sql.='?,';  
                }
                if($examCount > 0){
                    $sql = substr($sql,0,strlen($sql)-1).')'; //sql dynamically generated
                    $this->_db->query($sql,$examIds);
                    return $this->_db->get_result();
                }
               
            }
        }


        //this function gets the exam instruction for a particular exam and returns it as a string
        public function getInstruction($examId) :string{
            $this->_db->query('select instruction from ex_exam where exam_id =?',[$examId]);
            return $this->_db->one_result()->instruction;
        }


        //this methods handles the no of times an examinee can take an exam and returns the count
        public function handleCount($examId, $username, $maxcount) :int{
            $this->_db->query('select examinees from ex_available_exam where exam_id =?',[$examId]);
            $resArr = json_decode($this->_db->one_result()->examinees,true);
            $count = $resArr[$username];
            $resArr[$username]+=1; //increase the count
            if($resArr[$username] > $maxcount){ 
                //remove the examiner from taking the exam
                unset($resArr[$username]);
            }
            //update the count
            $resJSON = json_encode($resArr);
            $this->_db->query('update ex_available_exam set examinees = ? where exam_id = ?',[$resJSON, $examId]);
            return $count; 
        }


        //this method check if a completed exam exist
        public function isCompletedInserted($examId, $examineeId){
            $this->_db->query('select count(id) as counter from ex_completed where exam_id =? and examinee_id =?',[$examId, $examineeId]);
            return (($this->_db->one_result()->counter) > 0) ? true:false;
        }
        
     
        //this method insert data into the ex_completed table
        function insertCompleted($examId,$examinerId,$examineeId,$noOfQtns,$noOfCorAns,$noOfWrongAns,$ptgScore,$answers,$passed,$marks=null) :bool{
            if(!$this->isCompletedInserted($examId, $examineeId)){
                return $this->_db->query('insert into ex_completed(exam_id,examiner_id,examinee_id,total_no_of_qtn,no_cor_ans,no_wrong_ans,ptg_score,answers,passed,marks) values(?,?,?,?,?,?,?,?,?,?)',
                [$examId,$examinerId,$examineeId,$noOfQtns,$noOfCorAns,$noOfWrongAns,$ptgScore,$answers,$passed,$marks]);
            }else{
                return $this->updCompletedExam($examId,$examineeId,$noOfQtns,$noOfCorAns,$noOfWrongAns,$ptgScore,$answers,$passed,$marks);
            }  
        }
        
        public function updCompletedExam($examId,$examineeId,$noOfQtns,$noOfCorAns,$noOfWrongAns,$ptgScore,$answers,$passed,$marks){
             if(isset($marks)){ //update the marks column if given
                 return $this->_db->query('update ex_completed set total_no_of_qtn=?, no_cor_ans=?, no_wrong_ans=?, ptg_score=?, answers=?, passed=?, marks =? where exam_id=? and examinee_id=?',
                 [$noOfQtns,$noOfCorAns,$noOfWrongAns,$ptgScore,$answers,$passed,$marks,$examId,$examineeId]);
             }else{
                 return $this->_db->query('update ex_completed set total_no_of_qtn=?, no_cor_ans=?, no_wrong_ans=?, ptg_score=?, answers=?, passed=? where exam_id=? and examinee_id=?',
                 [$noOfQtns,$noOfCorAns,$noOfWrongAns,$ptgScore,$answers,$passed,$examId,$examineeId]);
             }
             
        }
        

        public function isTheoryQtnInserted($examId,$examinerId,$examineeId,$qId) :bool{
            $this->_db->query('select id from ex_theory where exam_id = ? and examiner_id = ? and examinee_id = ? and qtn_id = ?',[$examId,$examinerId,$examineeId,$qId]);
            return ($this->_db->row_count() > 0) ? true: false;
        }
                
        function insertTheory($examId,$examinerId,$examineeId,$qId,$answer,$mark,$comment){
            if(!$this->isTheoryQtnInserted($examId, $examinerId, $examineeId, $qId)){
                 $this->_db->query('insert into ex_theory(exam_id,examiner_id,examinee_id,qtn_id,answer,mark,comment) values(?,?,?,?,?,?,?)',
                [$examId,$examinerId,$examineeId,$qId,$answer,$mark,$comment]);
            }else{
                $this->_db->query('update ex_theory set answer=?, mark=?, comment=? where exam_id=? and examiner_id=? and examinee_id=? and qtn_id=?',[$answer,$mark,$comment,$examId,$examinerId,$examineeId,$qId]);
            }
                        
        }
        
        
        
         function updateTheoryQtn($examId,$examineeId,$qId,$mark,$comment,$submitted=false){
            $this->_db->query('update ex_theory set mark=?, comment=?, submitted=? where exam_id=?  and examinee_id=? and qtn_id=?',[$mark,$comment,$submitted,$examId,$examineeId,$qId]);              
        }
        

        function removeFromAvailableExam($examId, $examineeId){
            $this->_db->query('select examinees from ex_available_exam where exam_id = ?',[$examId]);
            if($this->_db->row_count() > 0){
                $resArr = json_decode($this->_db->one_result()->examinees, true);
                unset($resArr[$examineeId]);
                $res = json_encode($resArr);
                $this->_db->query('update ex_available_exam set examinees = ? where exam_id =?',[$res,$examId]);
            }
        }


        function getCompletedExam($table, $column, $examId, $examineeId){
            $this->_db->query('select ex_completed.exam_id, ex_completed.examinee_id,ex_completed.total_no_of_qtn,ex_completed.no_cor_ans,ex_completed.no_wrong_ans,ex_completed.ptg_score,ex_completed.passed,ex_completed.marks,'.$table.'.fname,'.$table.'.oname,'.$table.'.lname from ex_completed inner join '.$table.' on ex_completed.examinee_id = '.$table.'.'.$column.' where ex_completed.exam_id=? and ex_completed.examinee_id=?',[$examId, $examineeId]);
            if($this->_db->row_count() > 0){
                return $this->_db->one_result();
            }
        }


        function getCompletedExams($table, $column,$examineeId){
            
            $this->_db->query('select ex_completed.exam_id, ex_completed.examinee_id,ex_completed.total_no_of_qtn,ex_completed.no_cor_ans,ex_completed.no_wrong_ans,ex_completed.ptg_score,ex_completed.passed,'.$table.'.fname,'.$table.'.oname,'.$table.'.lname from ex_completed inner join '.$table.' on ex_completed.examinee_id = '.$table.'.'.$column.' where ex_completed.examinee_id=?',[$examineeId]);
            if($this->_db->row_count() > 0){
                return $this->_db->get_result();
            }
        }

        function getDetCompletedExam($table, $column, $examId, $examineeId){
            $this->_db->query('select ex_completed.*,'.$table.'.fname,'.$table.'.oname,'.$table.'.lname from ex_completed inner join '.$table.' on ex_completed.examinee_id = '.$table.'.'.$column.' where ex_completed.exam_id=? and ex_completed.examinee_id=?',[$examId, $examineeId]);
            if($this->_db->row_count() > 0){
                return $this->_db->one_result();
            }
        }



        function getDetCompletedExams($table, $column,$examineeId){
            $this->_db->query('select ex_completed.*,'.$table.'.fname,'.$table.'.oname,'.$table.'.lname from ex_completed inner join '.$table.' on ex_completed.examinee_id = '.$table.'.'.$column.' where ex_completed.examinee_id=?',[$examineeId]);
            if($this->_db->row_count() > 0){
                return $this->_db->get_result();
            }
        }


        //this method is to be used by the examiner to get the detailsof thos
        function getCompletedExamExaminees($table,$column,$examId){
            $this->_db->query('select ex_completed.*,'.$table.'.fname,'.$table.'.oname,'.$table.'.lname from ex_completed inner join '.$table.' on ex_completed.examinee_id = '.$table.'.'.$column.' where exam_id=?',[$examId]);
            if($this->_db->row_count() > 0){
                return $this->_db->get_result();
            } 
        }
        
        //this  function checks if an exam contains a theory question that have been submitted for marking
        function hasUnmarkedTheory($examId){
           $this->_db->query('select id from ex_theory where exam_id=? and submitted = ?',[$examId, false]);
            if($this->_db->row_count() > 0){
                return true;
            }
           return false;
        }
        
        //this functions returns  the theory question ready for marking for a particular exam and qtn_id
        function getTheoryQtns($examId,$qtnId,$includeSubmitted){
            if($includeSubmitted){
                $this->_db->query('select * from ex_theory where exam_id =? and qtn_id=? order by examinee_id',[$examId, $qtnId]);
                return $this->_db->get_result(); 
            }
            $this->_db->query('select * from ex_theory where exam_id =? and qtn_id=? and submitted=? order by examinee_id',[$examId, $qtnId, false]);
            return $this->_db->get_result(); 
        }
        
        

         //this functions returns the theory questions distinct(without repeating any question), it is normally needed for grouping
        function getDistinctTheoryQtns($examId){
            $this->_db->query('select distinct ex_question.qtn, ex_question.id,ex_question.answers, ex_question.mark from ex_question inner join ex_theory on ex_question.id = ex_theory.qtn_id where ex_question.exam_id = ? and ex_theory.submitted=?',[$examId,false]);
            return $this->_db->get_result(); 
        }
        
         //this functions returns the answer of an examinee for a particular exam as an array
        function getExamineeAnswers($examId, $examineeId){
            $this->_db->query('select answers from ex_completed where exam_id = ? and examinee_id = ?',[$examId,$examineeId]);
            return json_decode($this->_db->one_result()->answers, true); 
        }
        
        
        //this function checks that there is no more theory question for an examinee in a particular exam
        
        function noMoreTheoryQtns($examId,$examineeId) :bool{
            $this->_db->query('select count(id) as counter from ex_theory where exam_id = ? and examinee_id =? and submitted=?',[$examId, $examineeId, false]);
            return ($this->_db->one_result()->counter > 0) ? false: true;
        }
        
        //this function updates the theory answer in the ex_completed table for an examinee for a particular exam
        
        
        function updateCompletedExam($examId,$examineeId,$passMark){
            //variables declared for computing results
            $totalNoOfQtn = 0; //this is same as the total no of answers provided by the examiner, a german and multiple selection may have more than 1 answers
            $totalNoOfCorrAns = 0;  //this is the total no of correct answers provided by the examinee, one taking the exam
            $totalNoOfMarks = 0; //this is the total mark for the whole exam
            $totalMarkOfCorrAns = 0;  //this is the sum of the marks for the correct answer an examinee gives
            $addWrongAns = 0; //to be used by case 4
           
            $db2 = DB::get_instance2();
            //end of variables declared for computing results
            //get the answers from ex_completed table
            $this->_db->query('select answers, marks from ex_completed where exam_id=? and examinee_id=?',[$examId,$examineeId]);
            $ans = json_decode($this->_db->one_result()->answers,true);
            $marks = json_decode($this->_db->one_result()->marks,true);
            //loop through the answers to compute the result
            $first = true; //this is set so the query just below can run only once and be requeried other times
            $first2 = true; //this is set so the query for second db connection, $db2, should run only once, and be requeried other times
            foreach($ans as $qtnId=>$answer){
                if($first){
                    $this->_db->query('select * from ex_question where exam_id =? and id =?',[$examId,$qtnId]);   
                    $first = false;
                }else{
                    $this->_db->requery([$examId, $qtnId]);
                }
                $qtnDetails = $this->_db->one_result();
                
                //computing result
           
                  $totalNoOfMarks += $qtnDetails->mark;
                  switch($qtnDetails->type){
                        case 1:
                            $totalNoOfQtn++;
                            if($qtnDetails->answers === $answer){
                                $totalNoOfCorrAns++;
                                $totalMarkOfCorrAns += $qtnDetails->mark;
                            }
                        break;
                        case 2:
                            $totalNoOfQtn++;
                            if(Utility::equals($qtnDetails->answers,$answer)){
                                $totalNoOfCorrAns++;
                                $totalMarkOfCorrAns += $qtnDetails->mark;
                            }
                        break;
                        case 3:
                            $dbAnswer = json_decode($qtnDetails->answers,true);
                            $counter = count($dbAnswer);
                            $eachMark = ($qtnDetails->mark)/$counter;
                            $usrAnswer = json_decode($answer,true);
                            $totalNoOfQtn+=$counter;
                            if($qtnDetails->answer_order){
                                for($i=1;$i<=$counter;$i++){
                                    if(Utility::equals($dbAnswer['ans'.$counter],$usrAnswer['ans'.$counter])){
                                        $totalNoOfCorrAns++;
                                        $totalMarkOfCorrAns+=$eachMark;
                                    }
                                }

                            }else{
                                foreach($usrAnswer as $anss){
                                    foreach($dbAnswer as $dbAns){
                                        if(Utility::equals($anss, $dbAns)){
                                            $totalNoOfCorrAns++;
                                            $totalMarkOfCorrAns+=$eachMark;
                                        }
                                    }
                                }
                            }
                        break;
                        case 4:
                            $dbAnswer = json_decode($qtnDetails->answers,true);
                            $counter = count($dbAnswer);
                            $eachMark = ($qtnDetails->mark)/$counter;
                            $usrAnswer = json_decode($answer,true);
                            $usrAnsCount = count($usrAnswer);
                            $totalNoOfQtn+=$counter;
                            $mk = 0; //initialize to 0 so it can be increemented by each mark gotten
                             foreach($usrAnswer as $usrAnskey=>$anss){
                                $sameKey = false;
                                foreach($dbAnswer as $dbAnsKey=>$dbAns){  
                                    if(Utility::equals($usrAnskey, $dbAnsKey)){ //this means they have the same keys
                                        $sameKey = true;
                                        if(Utility::equals($anss, $dbAns)){
                                            $totalNoOfCorrAns++;
                                            $totalMarkOfCorrAns+=$eachMark;
                                            $mk+=$eachMark;
                                        }else{
                                            $addWrongAns++;
                                            $mk-=$eachMark;
                                        }
                                    }
                                }
                                if(!$sameKey){
                                    $addWrongAns++;
                                    $mk-=$eachMark;
                                }
                                if($mk<0){
                                    $mk = 0; //this is to ensure examinees mark is not deducted for wrong answers
                                }
                                $totalMarkOfCorrAns += $mk;
                            }
                        break;
                        case 5:
                             $totalNoOfQtn++;
                            if($first2){
                                $db2->query('select mark from ex_theory where exam_id =? and examinee_id=? and qtn_id =?',[$examId,$examineeId,$qtnId]);
                                $first2 = false; 
                            }else{
                                $db2->requery([$examId,$examineeId,$qtnId]);
                            }
                            $examineeMark = (int) $db2->one_result()->mark;
                            ///continue from here
                            if($examineeMark >= ($qtnDetails->mark)/2){
                                $totalNoOfCorrAns++;
                            }
                            $totalMarkOfCorrAns += $examineeMark;
                            $marks[$qtnId]=$examineeMark; //update the examinee mark 
                        break;
                    }
              
                //end of computing result
            }
            $totWrongAns = ($totalNoOfQtn - $totalNoOfCorrAns) + $addWrongAns;
            $totalScore = $totalMarkOfCorrAns;
            $ptgScore = round(($totalScore /$totalNoOfMarks) * 100,2);
            
            
             //this is added to calculate the equivalent score of the examinee that would be transfered to a score column in the main portal
            $examDetails = $this->getDetails($examId);
            if(!empty($examDetails->transfer)){ //this should only be true if it is an external exam (exam from the main portal)
                 $transferDetails = json_decode($examDetails->transfer);
                 $maxMark = (int) $transferDetails->maxScore;
                 $realScore = round($totalScore * ($maxMark/$totalNoOfMarks),2);
                 $agg = new Aggregate();
                 $agg->edit($realScore, $transferDetails->tableColumn, $transferDetails->tableName,$transferDetails->idColumn.',=,'.$examineeId);
             }
            //end of added to calculate
             
            $passed = ($ptgScore >= $passMark)?true:false;
            $jsonConvMarks = json_encode($marks);
            $this->updCompletedExam($examId, $examineeId, $totalNoOfQtn, $totalNoOfCorrAns, $totWrongAns, $ptgScore, json_encode($ans), $passed,$jsonConvMarks);
        }
        
        public function getPassMark($examId){
            $this->_db->query('select pass_mark from ex_exam where exam_id=?',[$examId]);
            return $this->_db->one_result()->pass_mark;
        }
        
        
        //this function gets the qtnId and Mark for an examinee in a particular exam, it returns an associative array
        public function getExamMarks($examId){
            $this->_db->query('select mark, id from ex_question where exam_id = ?',[$examId]);
            $res = $this->_db->get_result();
            $resArray = [];
            foreach($res as $singRes){
                $resArray[$singRes->id] = $singRes->mark;
            }
            return $resArray;
        }
        
        //counts the no of questions for an exam for a particular examinee
        public function getExamineeQtnCount($examId) :int{
           $this->_db->query('select no_qtn_req from ex_exam where exam_id = ?', [$examId]);
           return $this->_db->one_result()->no_qtn_req;
        }
        
        
        //this function returns the comment for a particular entry question in the ex_theory table
        function getTheoryComment($examId,$examineeId,$qtnId,$first){
            $db2 = DB::get_instance2();
            if($first){
                $db2->query('select comment from ex_theory where exam_id=? and examinee_id = ? and qtn_id=?',[$examId,$examineeId,$qtnId]);
            }else{
                $db2->requery([$examId,$examineeId,$qtnId]);
            }
            return $db2->one_result()->comment;
        }
        
        
    }