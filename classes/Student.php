<?php
class Student extends User
{

    private $_agg;
    private $_util;

    public function __construct($sch = null)
    {
        parent::__construct($cat = 3);
        $this->_agg = new Aggregate();
    }

    //this method gets an id for a student  after increement by 1
    public static function genId($sch_abbr, $level)
    {
        $db = DB::get_instance(); //get an instance of the database connection
        //get student count and increement by 1
        $db->query('select std_count from school where sch_abbr=?', [$sch_abbr]);
        $id = ($db->one_result()->std_count) + 1;
        //update the student count
        $db->query('update school set std_count=? where sch_abbr=?', [$id, $sch_abbr]);
        $preZeros = '';
        $preZerosCount = 3 - strlen((string)$id);
        for ($i = 1; $i <= $preZerosCount; $i++) {
            $preZeros .= '0';
        }
        //get current session year
        $db->query('select current_session from school where sch_abbr=?', [$sch_abbr]);
        $currSessionYear = explode('/', $db->one_result()->current_session)[0];
        return $sch_abbr . '/' . substr($currSessionYear, -2) . '/' . $level . '/' . $preZeros . $id;
    }

    public function getCurrentSession($sch)
    {
        return $this->_agg->lookup('current_session', 'school', 'sch_abbr,=,' . $sch);
    }

    public function getCurrentSessionFirstYear($sch)
    {
        return explode('/', $this->getCurrentSession($sch))[0];
    }

    public function getCurrentSessionSecondYear($sch)
    {
        return explode('/', $this->getCurrentSession($sch))[1];
    }

    public static function getDefaultPermission()
    {
        return '';
    }

    //this function returns the registered subject ids for a particular student as an associative array
    public function getRegisteredSubjectsId($stdId): array
    {
        $subIds = $this->_agg->lookup('subject_ids', 'student3', 'std_id,=,' . $stdId);
        if (!empty($subIds)) {
            $subIds =  json_decode($subIds, true);
            $subIdString = "'" . implode("','", $subIds) . "'";
            $this->_db->query('select subject,id from subject where id in(' . $subIdString . ')');
            $res = $this->_db->get_result();
            $subjects = [];
            foreach ($res as $r) {
                $subjects[$r->id] = $r->subject;
            }
            return $subjects;
        }
        return [];
    }

    //this functtion returns the subject list available for registration for a class which a student belongs to as an associative array

    function getRegistrationList($classId): array
    {
        $this->_db->query('select id,subject from subject where class_id=?', [$classId]);
        $res = $this->_db->get_result();
        if (!empty($res)) {
            $subs = [];
            foreach ($res as $r) {
                $subs[$r->id] = $r->subject;
            }
            return $subs;
        }
        return [];
    }

    //returns the minimun number of subject for a class
    function getMinNoSub($classID): int
    {
        $this->_db->query('select nos from class where id = ?', [$classID]);
        return $this->_db->one_result()->nos;
    }

    //this function register subjects for a particular student
    function registerSubjects($stdId, $subIds, $classId, $sch_abbr)
    {
        $subIdsString = "'" . implode("','", $subIds) . "'";
        $this->_db->query('select student_ids,id from subject where id in(' . $subIdsString . ')');
        $studentIds = $this->_db->get_result();
        $start = false; //this will help determine when a prepared query is available for use
        //get the subject_ids column from student3
        $this->_db->query('select subject_ids from student3 where std_id = ?', [$stdId]);
        $subjectIdsArr = json_decode($this->_db->one_result()->subject_ids);
        $subToAddArr = []; //to hold the subjet Ids to be registered in an arrray
        foreach ($studentIds as $studentId) { //$studentId is an object consisting of id and student_ids column from subject table
            //get the student_ids for each selected subject from the subject table
            $arrStdId = json_decode($studentId->student_ids);
            $arrStdId[] = $stdId; //append studentid;

            //update subject table
            if ($start) {
                $this->_db->requery([json_encode($arrStdId), $studentId->id]);
            } else {
                $this->_db->query('update subject set student_ids = ? where id=?', [json_encode($arrStdId), $studentId->id]);
                $start = false;
            }
            //append subject_id to student3 table
            $subjectIdsArr[] = $studentId->id;
            //append to the subjectIds that is to be registered
            $subToAddArr[] = $studentId->id;
        }
        //update student 3 table
        $this->_db->query('update student3 set subject_ids = ? where std_id=?', [json_encode($subjectIdsArr), $stdId]);
        //insert rows into score table
        $this->insertScore($stdId, $subToAddArr, $classId, $sch_abbr);
    }

    //this function inserts a record into the score table, instantUtil method has to be called to use this method, this is to help instantiate util Class
    private function insertScore($stdId, $subIds, $classId, $sch_abbr)
    {
        $currScoreTable = $this->_util->getFormatedSession($sch_abbr) . '_score';
        if (count($subIds) > 1) { //prepare and requery if entries will be more than one
            $start = false;
            foreach ($subIds as $subId) {
                if ($start) {
                    $this->_db->requery([$classId, $stdId, $subId]);
                } else {
                    $this->_db->query('insert into ' . $currScoreTable . '(class_id,std_id,subject_id) values(?,?,?)', [$classId, $stdId, $subId]);
                    $start = true;
                }
            }
        } else { //entry is expected to be one
            $this->_db->query('insert into ' . $currScoreTable . '(class_id,std_id,subject_id) values(?,?,?)', [$classId, $stdId, $subIds[0]]);
        }
    }

    //this function helps instantiate Util class
    function instantUtil()
    {
        $this->_util = new Utils();
    }

    //this function deregister subjects for a particular student
    function deregisterSubjects($stdId, $subIds, $classId, $sch_abbr)
    {
        $subIdsString = "'" . implode("','", $subIds) . "'"; //$subIdsString is to be used in the query below
        $this->_db->query('select student_ids,id from subject where id in(' . $subIdsString . ')');
        $studentIds = $this->_db->get_result();
        $start = false; //this will help determine when a prepared query is available for use 
        foreach ($studentIds as $studentId) { //$studentId is an object consisting of id and student_ids column from subject table
            //get the student_ids for each selected subject from the subject table
            $arrStdId = json_decode($studentId->student_ids, true);
            $newArrStdId = []; //the new one is to be refilled with values from $arrStdId except $stdId;
            foreach ($arrStdId as $res) { //remove $stdId from the $arrStdId
                if ($res != $stdId) {
                    $newArrStdId[] = $res;
                }
            }
            //update subject table
            if ($start) {
                $this->_db->requery([json_encode($newArrStdId), $studentId->id]);
            } else {
                $this->_db->query('update subject set student_ids = ? where id=?', [json_encode($newArrStdId), $studentId->id]);
                $start = true;
            }
        }
        //get the subject_ids column from student3
        $this->_db->query('select subject_ids from student3 where std_id = ?', [$stdId]);
        $subjectIdsArr = json_decode($this->_db->one_result()->subject_ids, true);
        $newSubjectIdsArr = [];
        foreach ($subjectIdsArr as $sId) { //remove subjectid from $subjectIdsArr
            if (!in_array($sId, $subIds)) {
                $newSubjectIdsArr[] = $sId;
            }
        }
        //update student 3 table
        $this->_db->query('update student3 set subject_ids = ? where std_id=?', [json_encode($newSubjectIdsArr), $stdId]);

        //delete rows from score table
        $this->deleteScore($stdId, $subIds, $classId, $sch_abbr);
    }


    //this function deletes a record from the score table, instantUtil method has to be called to use this method, this is to help instantiate util Class
    private function deleteScore($stdId, $subIds, $classId, $sch_abbr)
    {
        $currScoreTable = $this->_util->getFormatedSession($sch_abbr) . '_score';
        if (count($subIds) > 1) { //prepare and requery if entries will be more than one
            $start = false;
            foreach ($subIds as $subId) {
                if ($start) {
                    $this->_db->requery([$classId, $stdId, $subId]);
                } else {
                    $this->_db->query('delete from ' . $currScoreTable . ' where class_id=? and std_id=? and subject_id = ?', [$classId, $stdId, $subId]);
                    $start = true;
                }
            }
        } else { //entry is expected to be one
            $this->_db->query('delete from ' . $currScoreTable . ' where class_id=? and std_id=? and subject_id = ?', [$classId, $stdId, $subIds[0]]);
        }
    }



    function updateCompSubReg($stdId, $complete = true)
    {
        $this->_db->query('update student set sub_reg_comp=? where std_id = ?', [$complete, $stdId]);
    }

    //this function returns the class teacher id
    function getClassTeacherId($classId)
    {
        $this->_db->query('select teacher_id from class where id = ?', [$classId]);
        return $this->_db->one_result()->teacher_id;
    }

    //this function returns an associative array of subjects with the id being the key and the name being the value
    function getSubjectNames(array $subIds): array
    {
        $this->_db->query('select id,subject from subject where id in(' . "'" . implode("','", $subIds) . "')");
        $subjects = [];
        $res = $this->_db->get_result();
        foreach ($res as $r) {
            $subjects[$r->id] = $r->subject;
        }
        return $subjects;
    }


    function classHasSubject($classId): bool
    {
        $this->_db->query('select count(id) as counter from subject where class_id = ?', [$classId]);
        return ($this->_db->one_result()->counter > 0) ? true : false;
    }
}
