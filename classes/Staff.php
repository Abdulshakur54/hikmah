<?php
	class Staff extends User{

            public function __construct(){
                    parent::__construct($cat = 2);
            }

            //this method gets an id for management member after increement by 1
            public static function genId(){
               $agg = new Aggregate();
               $id = $agg->lookUp('staff_count', 'sing_val', 'id,=,1') + 1; //gets the current no and increement by 1
               $agg->edit($id, 'staff_count', 'sing_val', 'id,=,1'); //updates the admission count
               $preZeros = '';
               $preZerosCount = 3 - strlen((string)$id);
               for($i=1;$i<=$preZerosCount;$i++){
                   $preZeros.='0';
               }
               return $preZeros.$id;
            }

            public function getClassWithId($staffId){
                $this->_db->query('select class,id,level from class where teacher_id=?',[$staffId]);
                return ($this->_db->row_count()) ? $this->_db->one_result():'';
            }

            
            //this method returns all the subject a teacher is taking
            public function getSubjectsWithIds($staffId){
                $this->_db->query('select subject.subject,subject2.id,subject2.class_id,subject.level,subject2.teacher_id,class.class from subject inner join subject2 on subject.id = subject2.subject_id inner join class '
                        . 'on subject2.class_id = class.id where subject2.teacher_id = ? order by subject.subject',[$staffId]);
                return ($this->_db->row_count()) ? $this->_db->get_result():'';
            }
            
            function getSchedule($classId){
                $this->_db->query('select * from class_psy where class_id = ?',[$classId]);
                return $this->_db->one_result();
            }
            
            function updateSchedule($classId,$punc,$hon,$dhw,$rap,$sot,$rwp,$ls,$atw,$ho,$car,$con,$wi,$ob,$hea,$vs,$pig,$pis,$ac,$pama,$ms,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$height_beg,$height_end,$weight_beg,$weight_end,$signature = null):bool{
                if($signature !== null){
                    return $this->_db->query('update class_psy set psy1=?,psy2=?,psy3=?,psy4=?,psy5=?,psy6=?,psy7=?,psy8=?,psy9=?,psy10=?,psy11=?,psy12=?,psy13=?,psy14=?,psy15=?,psy16=?,psy17=?,psy18=?,psy19=?,psy20=?,a1=?,b2=?,b3=?,c4=?,c5=?,c6=?,d7=?,e8=?,f9=?,height_beg=?,height_end=?,weight_beg=?,weight_end=?,signature=? where class_id=?',[$punc,$hon,$dhw,$rap,$sot,$rwp,$ls,$atw,$ho,$car,$con,$wi,$ob,$hea,$vs,$pig,$pis,$ac,$pama,$ms,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$height_beg,$height_end,$weight_beg,$weight_end,$signature,$classId]);
                }
                return $this->_db->query('update class_psy set psy1=?,psy2=?,psy3=?,psy4=?,psy5=?,psy6=?,psy7=?,psy8=?,psy9=?,psy10=?,psy11=?,psy12=?,psy13=?,psy14=?,psy15=?,psy16=?,psy17=?,psy18=?,psy19=?,psy20=?,a1=?,b2=?,b3=?,c4=?,c5=?,c6=?,d7=?,e8=?,f9=?,height_beg=?,height_end=?,weight_beg=?,weight_end=? where class_id=?',[$punc,$hon,$dhw,$rap,$sot,$rwp,$ls,$atw,$ho,$car,$con,$wi,$ob,$hea,$vs,$pig,$pis,$ac,$pama,$ms,$a1,$b2,$b3,$c4,$c5,$c6,$d7,$e8,$f9,$height_beg,$height_end,$weight_beg,$weight_end,$classId]);
            }

            function isASubjectTeacher($username) :bool{
                $this->_db->query('select count(id) as counter from subject2 where teacher_id=?',[$username]);
                return ($this->_db->one_result()->counter > 0)?true:false;
            }
            function isAClassTeacher($username) :bool{
                $this->_db->query('select count(id) as counter from class where teacher_id=?',[$username]);
                return ($this->_db->one_result()->counter > 0)?true:false;
            }
            
            
            //this function checks if all students of a class have completed course registration
            function isStdsSubRegComplete($classId){
                $this->_db->query('select count(id) as counter from student where class_id = ? and sub_reg_comp = false',[$classId]);
                return ($this->_db->one_result()->counter) ?false:true;
            }
 
            //this function gets all students of a class that have not completed course registration
            function getStdsNotCompSubReg($classId){
                $this->_db->query('select fname,lname,oname,std_id from student where class_id=? and sub_reg_comp = false',[$classId]);
                return $this->_db->get_result();
            }
            
            //this function updates student psycometry
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
            
            
            function getStudents($classId){
                $this->_db->query('select std_id,fname,lname,oname from student where class_id=?',[$classId]);
                return $this->_db->get_result();
            }
            
            function getStudentsIds($classId){
                $this->_db->query('select std_id from student where class_id=?',[$classId]);
                $ids = [];
                if($this->_db->row_count() > 0){
                    $res = $this->_db->get_result();
                    foreach($res as $r){
                        $ids[]=$r->std_id;
                    }
                    return $ids;
                }
            }
            
            //this function returns true if a staff is a subject teacher of the provided subject
            function isSubjectTeacher($staffId, $subId) :bool{
                $this->_db->query('select count(id) as counter from subject2 where teacher_id=? and id=?',[$staffId,$subId]);
                return ($this->_db->one_result()->counter > 0)?true:false;
            }
            
            //this function returns the score settings for a school
            function getScoreSettings($sch_abbr){
                $this->_db->query('select fa,sa,ft,st,pro,exam from school2 where sch_abbr=?',[$sch_abbr]);
                $res = $this->_db->one_result();
                $scoreSetting = [];
                $scoreSetting['fa']=$res->fa;
                $scoreSetting['sa']=$res->sa;
                $scoreSetting['ft']=$res->ft;
                $scoreSetting['st']=$res->st;
                $scoreSetting['pro']=$res->pro;
                $scoreSetting['exam']=$res->exam;
                return $scoreSetting;
            }
	}