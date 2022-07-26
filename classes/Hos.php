<?php
    class Hos extends Management{
        
        private $_addSubjectStmt = false; //used to know the status of a prepared statement for addSubject method
        
        public function getNotClassTeachers($sch_abbr){
            if($this->_db->query('select staff_id,fname,lname,oname from staff where class_id is null and active = ? and rank=? and sch_abbr=?',[true,7,$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
        
        public function getClassTeachers($sch_abbr){
            if($this->_db->query('select staff_id,fname,lname,oname from staff where class_id is not null and active = ? and rank=? and sch_abbr=?',[true,7,$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
        public function getTeachers($sch_abbr){
            if($this->_db->query('select staff_id,fname,lname,oname,title from staff where active = ? and rank=? and sch_abbr=?',[true,7,$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
        
        public function classNoTeacher($sch_abbr){
            if($this->_db->query('select level,class,id from class where teacher_id is null and sch_abbr=? order by level, class',[$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
        public function classWithTeacher($sch_abbr){
            if($this->_db->query('select level,class,id from class where teacher_id is not null and sch_abbr=? order by level, class',[$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
        public function getClasses($sch_abbr){
            if($this->_db->query('select level,class,id,petname from class where sch_abbr=? order by level, class',[$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
        public function getSubjects($sch_abbr){
            if($this->_db->query('select subject.level,subject.subject,subject.id,class.class from subject inner join class on subject.class_id = class.id where subject.sch_abbr=? order by subject.level, class.class, subject.subject',[$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
        //Ensures the teacher is not already the class teacher to the class, returns a boolean
        public function isClassTeacher($classId,$teacherId){
            $this->_db->query('select teacher_id from class where id=?',[$classId]);
            $res = $this->_db->one_result()->teacher_id;
            return ($res === $teacherId)?true:false;
        }
        
        //checks if the teacher is already a class teacher, returns a boolean
        public function isAClassTeacher($teacherId){
            $this->_db->query('select count(teacher_id) as counter from class where teacher_id=?',[$teacherId]);
            if($this->_db->one_result()->counter > 0){
                return true;
            }else{
                return false;
            }
        }
        
        
        //this method returns all the subject(id) for a student, an array is returned
        public function getStudentSubjects($studentId){
            $this->_db->query('select subject_ids from student3 where student_id=?',[$studentId]);
            if($this->_db->row_count() > 0){
                $sIds = $this->_db->one_result()->subject_ids;
                if($sIds !== null){
                   return json_decode($sIds, true);
                }
            }
            return [];
        }
        
        //this method returns all the students in a class
        public function getAllClassStudents($classId){
            $this->_db->query('select std_id from student where class_id = ?',[$classId]);
            return $this->_db->get_result();
        }
        
         //this method returns all the students(id) for a subject, an array is returned
        public function getSubjectStudents($subjectId){
            $this->_db->query('select student_ids from subject where id=?',[$subjectId]);
            if($this->_db->row_count() > 0){
                $sIds = $this->_db->one_result()->student_ids;
                if($sIds !== null){
                     return json_decode($sIds, true);
                } 
            }
            return [];
        }
        
        public function updateSubjectTeachers($subjectId,$teacherId,$setNull = false){
            $sql1 = 'update subject set teacher_id=? where id=?';
            if($setNull){
                $val1 = [null,$subjectId];
            }else{
                $val1 = [$teacherId,$subjectId];
            }
            $this->_db->query($sql1,$val1);
        }
        
        public function addClass($sch_abbr,$level,$class,$nos,$petname){
            
            if($this->_db->query('insert into class(sch_abbr,level,class,nos,petname) values(?,?,?,?,?)',[$sch_abbr,$level,$class,$nos,$petname])){
                //gets the id of the just inserted class
                $this->_db->query('select id from class where sch_abbr = ? and level = ? and class = ?',[$sch_abbr,$level,$class]);
                $id = $this->_db->one_result()->id;
                //insert class id into the class_psy table
                $this->_db->query('insert into class_psy(class_id,sch_abbr) values(?,?)',[$id,$sch_abbr]);
                //add any available subject to the added class
                $subjects = $this->getLevelSubjects($level);
                if(!empty($subjects)){
                    foreach ($subjects as $subject){
                        $this->addSubject($sch_abbr, $subject->subject, $id, $level);
                    }
                }
                
            }
        }
        
        public function addSubject($sch_abbr,$subject,$class_id,$level){
           if($this->_addSubjectStmt){
               return $this->_db->requery([$sch_abbr,$subject,$class_id,$level]);
           }else{
                $this->_addSubjectStmt = true;
               return $this->_db->query('insert into subject(sch_abbr,subject,class_id,level) values(?,?,?,?)',[$sch_abbr,$subject,$class_id,$level]); 
           }
           
        }
        
        //this method returns all subject name for the given level
        public function getLevelSubjects($level){
            $this->_db->query('select distinct subject from subject where level = ?',[$level]);
            if($this->_db->row_count() > 0){
                return $this->_db->get_result();
            }
        }
        
        public function classExists($sch_abbr,$level,$class) :bool{
            $this->_db->query('select count(id) as counter from class where sch_abbr=? and level=? and class=?',[$sch_abbr,$level,$class]);
            if($this->_db->one_result()->counter > 0){
                return true;
            }
            return false;
        }
        
        
        public function editClass($classId,$newClass,$petname){
           // echo $classId.'<br>'.$newClass.'<br>'.$petname;
            if(!empty($newClass) && empty($petname)){
                $sql = 'update class set class = ? where id=?';
                $val = [$newClass,$classId];
            }
            
            if(empty($newClass) && !empty($petname)){
                $sql = 'update class set petname = ? where id=?';
                $val = [$petname,$classId];
            }
            
            if(!empty($newClass) && !empty($petname)){
                $sql = 'update class set class = ?, petname = ? where id=?';
                $val = [$newClass,$petname,$classId];
            }
            $this->_db->query($sql,$val);
        }
        
        
        public function subjectExists($sch_abbr,$level,$subject) :bool{
            $this->_db->query('select count(id) as counter from subject where sch_abbr=? and level=? and subject=?',[$sch_abbr,$level,$subject]);
            if($this->_db->one_result()->counter > 0){
                return true;
            }
            return false;
        }
        
        //method return all the class_ids for a level
        public function getLevelClasses($level){
            $this->_db->query('select id from class where level=?',[$level]);
            if($this->_db->row_count() > 0){
                return $this->_db->get_result();
            }
        }
        
        
         //this method helps to assign a class to a teacher. 
        public function assignClass($classId,$staffId){
           $this->_db->query('update class set teacher_id=? where id=?',[$staffId,$classId]);
        }
        
        //this method helps to unassign a class to a teacher. 
        public function unAssignClass($staffId){
           $this->_db->query('update class set teacher_id=? where teacher_id=?',[null,$staffId]);
        }
        
        
        public function getClassTeacherId($classId){
            $this->_db->query('select staff_id from staff where class_id =?',[$classId]);
            if($this->_db->row_count() > 0){
                return $this->_db->one_result()->staff_id;
            }
         
        }
        
        public function classHasATeacher($classId) :bool{
            $this->_db->query('select teacher_id from class where id = ?',[$classId]);
            $res = $this->_db->one_result()->teacher_id;
            return (!empty($res)) ? true:false;
        }
        
        
        
        public function getStaffNames($staffId){
            $this->_db->query('select fname,lname,oname, title from staff where staff_id=?',[$staffId]);
            return $this->_db->one_result();
        }
        
        //this method deletes a class, it also deletes all the relationship it has in other tables
        public function deleteClass($classId,$sch_abbr) :bool{
            //delete from class table
            $sql1 = 'delete from class where id = ?';
            $val1 = [$classId];
            //delete from the other tables
            $sql2 = 'delete from subject where class_id=?';
            $val2 = [$classId];
            $sql3 = 'update student3 set class_id = ? where class_id = ?';
            $val3 = [null,$classId];
            //delete from score table
            $utils = new Utils();
            $formSession = $utils->getFormatedSession($sch_abbr);
            $sql4 = 'delete from '.$formSession.'_score where class_id = ?';
            $val4 = [$classId]; 
            //delete from the other tables(class_psy)
            $sql5 = 'delete from class_psy where class_id=?';
            $val5 = [$classId];
            return $this->_db->trans_query([[$sql1,$val1],[$sql2,$val2],[$sql3,$val3],[$sql4,$val4],[$sql5,$val5]]);
        }
        
         //this method deletes a subject, it also deletes all the relationship it has in other tables
        public function deleteSubject($subjectId,$sch_abbr) :bool{
            //query to get the students and teacher related to the subject
            $this->_db->query('select student_ids from subject where id=?',[$subjectId]);
            $studentIds = json_decode($this->_db->one_result()->student_ids);
            $db2 = DB::get_instance2();
            if(!empty($studentIds)){ //this means some students offer this subject
                $start = false;
                foreach($studentIds as $studentId){
                    if($start){ //requery because query have already been prepared
                        $this->_db->requery([$studentId]);
                        $subjectIds = json_decode($this->_db->one_result()->subject_ids);
                        $newSubjectIds = [];
                        foreach($subjectIds as $subId){
                            if($subId != $subjectId){
                               $newSubjectIds[] = $subId; 
                            }
                        }
                         //update student3 table with the modified sujectIds(newSubjectIds)
                        $db2->requery([json_encode($newSubjectIds)]);
                        
                        
                    }else{
                        $this->_db->query('select subject_ids from student3 where std_id = ?',[$studentId]);//query the db
                        $subjectIds = json_decode($this->_db->one_result()->subject_ids);
                        $newSubjectIds = [];
                        foreach($subjectIds as $subId){
                            if($subId != $subjectId){
                               $newSubjectIds[] = $subId; 
                            }
                        }
                        //update student3 table with the modified sujectIds(newSubjectIds)
                        $db2->query('update student3 set subject_ids = ?',[json_encode($newSubjectIds)]);
                    }
                }
            }
            //delete from the subject tables
            $sql1 = 'delete from subject where id=?';
            $val1 = [$subjectId];
            //delete from score table
            $utils = new Utils();
            $formSession = $utils->getFormatedSession($sch_abbr);
            $sql2 = 'delete from '.$formSession.'_score where subject_id = ?';
            $val2 = [$subjectId];  
            return $this->_db->trans_query([[$sql1,$val1],[$sql2,$val2]]);
        }
        
        
        public function getClassAndLevel($classId){
            $this->_db->query('select level,class from class where id = ?',[$classId]);
            return $this->_db->one_result();
        }
        
        public function isSubjectTeacher($teacherId,$subjectId):bool{
            $this->_db->query('select count(id) as counter from subject where teacher_id=? and id=?',[$teacherId,$subjectId]);
            return ($this->_db->one_result()->counter > 0) ? true: false;
        }
        
        public function subjectHasTeacher($subjectId) :bool{
            $this->_db->query('select teacher_id from subject where id=?',[$subjectId]);
            return(!empty($this->_db->one_result()->teacher_id)) ? true: false;
            
        }
        
        function getNoStudentsNeedsClass($sch_abbr) :int{
            $this->_db->query('select count(student.id) as counter from student3 inner join student on student3.std_id = student.std_id where student3.class_id IS NULL and student.sch_abbr = ?',[$sch_abbr]);
            return $this->_db->one_result()->counter;
        }
        
        function getStudentsNeedsClass($sch_abbr, $level=null){
            $this->_db->query('select student.id,student.std_id, student.fname,student.oname,student.lname from student inner join student3 on student.std_id = student3.std_id where student3.class_id IS NULL and student.sch_abbr = ? and student.level = ?',[$sch_abbr,$level]);
            return $this->_db->get_result();
        }
        
        function getSchedule($sch_abbr){
            $this->_db->query('select * from school2 where sch_abbr = ?',[$sch_abbr]);
            return $this->_db->one_result();
        }
        
       function updateSchedule($sch_abbr,$fa,$sa,$ft,$st,$pro,$exam,$ftto,$stto,$ttto,$ftrd,$strd,$ttrd,$ftcd,$stcd,$ttcd,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$signature=null) :bool{
           if($signature !== null){
               return $this->_db->query('update school2 set fa=?,sa=?,ft=?,st=?,pro=?,exam=?,ft_times_opened=?,st_times_opened=?,tt_times_opened=?,ft_res_date=?,st_res_date=?,tt_res_date=?,'
                   . 'ft_close_date=?,st_close_date=?,tt_close_date=?,a1=?,b2=?,b3=?,c4=?,c5=?,c6=?,d7=?,e8=?,f9=?,signature=? where sch_abbr=?',[$fa,$sa,$ft,$st,$pro,$exam,$ftto,$stto,$ttto,$ftrd,$strd,$ttrd,$ftcd,$stcd,$ttcd,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$signature,$sch_abbr]);
           }
           return $this->_db->query('update school2 set fa=?,sa=?,ft=?,st=?,exam=?,pro=?,ft_times_opened=?,st_times_opened=?,tt_times_opened=?,ft_res_date=?,st_res_date=?,tt_res_date=?,'
                   . 'ft_close_date=?,st_close_date=?,tt_close_date=?,a1=?,b2=?,b3=?,c4=?,c5=?,c6=?,d7=?,e8=?,f9=? where sch_abbr=?',[$fa,$sa,$ft,$st,$pro,$exam,$ftto,$stto,$ttto,$ftrd,$strd,$ttrd,$ftcd,$stcd,$ttcd,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$sch_abbr]);
       }
       
       function populateStdPsy($classId, $stdIdsString) :bool{
           //get default class psycometry
           $this->_db->query('select * from class_psy where class_id=?',[$classId]);
           $cp = $this->_db->one_result();  //cp stand for class psycometry, the short form is used to reduce the lenght of code written
            $sql = 'UPDATE `student_psy` SET `ft_height_beg`=?,`ft_height_end`=?,`ft_weight_beg`=?,`ft_weight_end`=?,`st_height_beg`=?,'
                    . '`st_height_end`=?,`st_weight_beg`=?,`st_weight_end`=?,`tt_height_beg`=?,`tt_height_end`=?,`tt_weight_beg`=?,'
                    . '`tt_weight_end`=?,`ft_psy1`=?,`ft_psy2`=?,`ft_psy3`=?,`ft_psy4`=?,`ft_psy5`=?,`ft_psy6`=?,`ft_psy7`=?,`ft_psy8`=?,'
                    . '`ft_psy9`=?,`ft_psy10`=?,`ft_psy11`=?,`ft_psy12`=?,`ft_psy13`=?,`ft_psy14`=?,`ft_psy15`=?,`ft_psy16`=?,`ft_psy17`=?,'
                    . '`ft_psy18`=?,`ft_psy19`=?,`ft_psy20`=?,`st_psy1`=?,`st_psy2`=?,`st_psy3`=?,`st_psy4`=?,`st_psy5`=?,`st_psy6`=?,'
                    . '`st_psy7`=?,`st_psy8`=?,`st_psy9`=?,`st_psy10`=?,`st_psy11`=?,`st_psy12`=?,`st_psy13`=?,`st_psy14`=?,`st_psy15`=?,'
                    . '`st_psy16`=?,`st_psy17`=?,`st_psy18`=?,`st_psy19`=?,`st_psy20`=?,`tt_psy1`=?,`tt_psy2`=?,`tt_psy3`=?,`tt_psy4`=?,`'
                    . 'tt_psy5`=?,`tt_psy6`=?,`tt_psy7`=?,`tt_psy8`=?,`tt_psy9`=?,`tt_psy10`=?,`tt_psy11`=?,`tt_psy12`=?,`tt_psy13`=?,'
                    . '`tt_psy14`=?,`tt_psy15`=?,`tt_psy16`=?,`tt_psy17`=?,`tt_psy18`=?,`tt_psy19`=?,`tt_psy20`=? WHERE std_id in('.$stdIdsString.')';
            $val = [$cp->height_beg,$cp->height_end,$cp->weight_beg,$cp->weight_end,$cp->height_beg,$cp->height_end,$cp->weight_beg,
                $cp->weight_end,$cp->height_beg,$cp->height_end,$cp->weight_beg,$cp->weight_end, $cp->psy1,$cp->psy2,$cp->psy3,$cp->psy4,
                $cp->psy5,$cp->psy6,$cp->psy7,$cp->psy8,$cp->psy9,$cp->psy10,$cp->psy11,$cp->psy12,$cp->psy13,$cp->psy14,$cp->psy15,
                $cp->psy16,$cp->psy17,$cp->psy18,$cp->psy19,$cp->psy20, $cp->psy1,$cp->psy2,$cp->psy3,$cp->psy4,$cp->psy5,$cp->psy6,
                $cp->psy7,$cp->psy8,$cp->psy9,$cp->psy10,$cp->psy11,$cp->psy12,$cp->psy13,$cp->psy14,$cp->psy15,$cp->psy16,$cp->psy17,
                $cp->psy18,$cp->psy19,$cp->psy20, $cp->psy1,$cp->psy2,$cp->psy3,$cp->psy4,$cp->psy5,$cp->psy6,$cp->psy7,$cp->psy8,
                $cp->psy9,$cp->psy10,$cp->psy11,$cp->psy12,$cp->psy13,$cp->psy14,$cp->psy15,$cp->psy16,$cp->psy17,$cp->psy18,$cp->psy19,
                $cp->psy20];
                return $this->_db->query($sql,$val);
       }
       
       //this function returns some basic details for a class
       function getClassDetail($classId){
           $this->_db->query('select sch_abbr,level,class,teacher_id,nos from class where id=?',[$classId]);
           return $this->_db->one_result();
       }
       
       
       //this function get some teacher details
       function getTeacherDetail($teacher_id){
           $this->_db->query('select title,fname,lname,oname from staff where staff_id =?',[$teacher_id]);
           return $this->_db->one_result();
       }
       
       //this function checks if all students of a school have completed course registration
       function isStdsSubRegComplete($sch_abbr){
           $this->_db->query('select count(id) as counter from student where class_id is NOT NULL and sub_reg_comp is false and active = true and sch_abbr=?',[$sch_abbr]);
           return ($this->_db->one_result()->counter) ?false:true;
       }
 
        //this function gets all students of a school that have not completed course registration
       function getStdsNotCompSubReg($sch_abbr){
           $this->_db->query('select student.fname,student.lname,student.oname,student.std_id,student.level,class.class from student inner join class on student.class_id = class.id where student.class_id is NOT NULL and student.sub_reg_comp = false and student.sch_abbr=?',[$sch_abbr]);
           return $this->_db->get_result();
       }
       
       function getClassSameLevel($ClassId){
           $this->_db->query('select level from class where id=?',[$ClassId]);
           $level = (int) $this->_db->one_result()->level;
           $this->_db->query('select id,sch_abbr,level,class,teacher_id from class where not id=? and level=?',[$ClassId,$level]);
           return $this->_db->get_result();
       }
       
       //this function helps reset the score table for a particular school after the score settings have been changed
       function updateScore($scoreTable,$currTerm,$sch_abbr){
           switch($currTerm){
               case 'ft':
                   $this->_db->query('update '.$scoreTable.' set ft_fa=?,ft_sa=?,ft_ft=?,ft_st=?,ft_pro=?,ft_ex=?,ft_tot=? where sch_abbr=?',[null,null,null,null,null,null,null,$sch_abbr]);
                   break;
               case 'st':
                   $this->_db->query('update '.$scoreTable.' set st_fa=?,st_sa=?,st_ft=?,st_st=?,st_pro=?,st_ex=?,st_tot=? where sch_abbr=?',[null,null,null,null,null,null,null,$sch_abbr]);
                   break;
               case 'tt':
                   $this->_db->query('update '.$scoreTable.' set tt_fa=?,tt_sa=?,tt_ft=?,tt_st=?,tt_pro=?,tt_ex=?,tt_tot=? where sch_abbr=?',[null,null,null,null,null,null,null,$sch_abbr]);
           }
       }

               
       
       
       
       
    }