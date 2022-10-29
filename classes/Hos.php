<?php
class Hos extends Management
{

    private $_addSubjectStmt = false; //used to know the status of a prepared statement for addSubject method

    public function getNotClassTeachers($sch_abbr)
    {
        if ($this->_db->query('select staff_id,fname,lname,oname from staff where class_id is null and active = ? and rank=? and sch_abbr=?', [true, 7, $sch_abbr])) {
            return $this->_db->get_result();
        }
    }


    public function getClassTeachers($sch_abbr)
    {
        if ($this->_db->query('select staff_id,fname,lname,oname from staff where class_id is not null and active = ? and rank=? and sch_abbr=?', [true, 7, $sch_abbr])) {
            return $this->_db->get_result();
        }
    }

    public function getTeachers($sch_abbr)
    {
        if ($this->_db->query('select staff_id,fname,lname,oname,title from staff where active = ? and rank=? and sch_abbr=?', [true, 7, $sch_abbr])) {
            return $this->_db->get_result();
        }
    }


    public function classNoTeacher($sch_abbr)
    {
        if ($this->_db->query('select level,class,id from class where teacher_id is null and sch_abbr=? order by level, class', [$sch_abbr])) {
            return $this->_db->get_result();
        }
    }

    public function classWithTeacher($sch_abbr)
    {
        if ($this->_db->query('select level,class,id from class where teacher_id is not null and sch_abbr=? order by level, class', [$sch_abbr])) {
            return $this->_db->get_result();
        }
    }

    public function getClasses($sch_abbr)
    {
        if ($this->_db->query('select level,class,id,petname from class where sch_abbr=? order by level, class', [$sch_abbr])) {
            return $this->_db->get_result();
        }
    }


    //Ensures the teacher is not already the class teacher to the class, returns a boolean
    public function isClassTeacher($classId, $teacherId)
    {
        $this->_db->query('select teacher_id from class where id=?', [$classId]);
        $res = $this->_db->one_result()->teacher_id;
        return ($res === $teacherId) ? true : false;
    }

    //checks if the teacher is already a class teacher, returns a boolean
    public function isAClassTeacher($teacherId)
    {
        $this->_db->query('select count(teacher_id) as counter from class where teacher_id=?', [$teacherId]);
        if ($this->_db->one_result()->counter > 0) {
            return true;
        } else {
            return false;
        }
    }

    //this method returns all the students in a class
    public function getAllClassStudents($classId)
    {
        $this->_db->query('select std_id from student where class_id = ?', [$classId]);
        return $this->_db->get_result();
    }

    //this method returns all the students(id) for a subject, an array is returned
    public function getSubjectStudents($subjectId)
    {
        $this->_db->query('select student_ids from subject where id=?', [$subjectId]);
        if ($this->_db->row_count() > 0) {
            $sIds = $this->_db->one_result()->student_ids;
            if ($sIds !== null) {
                return json_decode($sIds, true);
            }
        }
        return [];
    }

    public function updateSubjectTeacher($subjectId, $teacherId, $setNull = false)
    {
        $sql1 = 'update subject2 set teacher_id=? where id=?';
        if ($setNull) {
            $val1 = [null, $subjectId];
        } else {
            $val1 = [$teacherId, $subjectId];
        }
        $this->_db->query($sql1, $val1);
    }

    public function addClass($sch_abbr, $level, $class, $nos, $petname)
    {

        if ($this->_db->query('insert into class(sch_abbr,level,class,nos,petname) values(?,?,?,?,?)', [$sch_abbr, $level, $class, $nos, $petname])) {
            //gets the id of the just inserted class
            $this->_db->query('select id from class where sch_abbr = ? and level = ? and class = ?', [$sch_abbr, $level, $class]);
            $id = $this->_db->one_result()->id;
            //insert class id into the class_psy table
            $this->_db->query('insert into class_psy(class_id,sch_abbr) values(?,?)', [$id, $sch_abbr]);
            //add any available subject to the added class
            $subjects = $this->getLevelSubjects($level, $sch_abbr);
            if (!empty($subjects)) {
                foreach ($subjects as $subject) {
                    $start = false;
                    if ($start) {
                        $this->_db->requery([$subject->id, $id]);
                    } else {
                        $this->_db->query('insert into subject2 (subject_id,class_id) values (?,?)', [$subject->id, $id]);
                    }
                }
            }
        }
    }

    public function addSubject($sch_abbr, $subject, $level)
    {
        if ($this->_db->query('insert into subject(sch_abbr,subject,level) values (?,?,?)', [$sch_abbr, $subject, $level])) {
            $subject_id = $this->_db->get('subject', 'id', '', 'id', 'desc')->id;
            $classIds = $this->getLevelClasses($level, $sch_abbr);
            $start = false;
            foreach ($classIds as $id) {
                if ($start) {
                    $this->_db->requery([$subject_id, $id->id]);
                } else {
                    $this->_db->query('insert into subject2(subject_id,class_id) values(?,?)', [$subject_id, $id->id]);
                    $start = true;
                }
            }
            return true;
        }
        return false;
    }

    public function getSubjects($sch_abbr): array
    {
        $this->_db->query('select subject.*,staff.title,staff.fname,staff.lname,staff.oname, class.class from subject inner join subject2 on subject.id = subject2.subject_id left join staff on subject2.teacher_id = staff.staff_id left join class on subject2.class_id = class.id where subject.sch_abbr=?', [$sch_abbr]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }
    public function getDiscreteSubjects($sch_abbr): array
    {
        $this->_db->query('select subject.*,subject2.id as subid,staff.title,staff.fname,staff.lname,staff.oname, class.class from subject inner join subject2 on subject.id = subject2.subject_id left join staff on subject2.teacher_id = staff.staff_id left join class on subject2.class_id = class.id where subject.sch_abbr=?', [$sch_abbr]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }

    //this method returns all subject name for the given level
    public function getLevelSubjects($level, $sch_abbr): array
    {
        $this->_db->query('select id,subject from subject where level = ? and sch_abbr=?', [$level, $sch_abbr]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }

    public function classExists($sch_abbr, $level, $class): bool
    {
        $this->_db->query('select count(id) as counter from class where sch_abbr=? and level=? and class=?', [$sch_abbr, $level, $class]);
        if ($this->_db->one_result()->counter > 0) {
            return true;
        }
        return false;
    }


    public function editSubject($subject_id, $subject)
    {
        $this->_db->update('subject', ['subject' => $subject], "id=$subject_id");
    }


    public function editClass($classId, $newClass, $petname)
    {
        // echo $classId.'<br>'.$newClass.'<br>'.$petname;
        if (!empty($newClass) && empty($petname)) {
            $sql = 'update class set class = ? where id=?';
            $val = [$newClass, $classId];
        }

        if (empty($newClass) && !empty($petname)) {
            $sql = 'update class set petname = ? where id=?';
            $val = [$petname, $classId];
        }

        if (!empty($newClass) && !empty($petname)) {
            $sql = 'update class set class = ?, petname = ? where id=?';
            $val = [$newClass, $petname, $classId];
        }
        $this->_db->query($sql, $val);
    }


    public function subjectExists($sch_abbr, $level, $subject): bool
    {
        $this->_db->query('select count(id) as counter from subject where sch_abbr=? and level=? and subject=?', [$sch_abbr, $level, $subject]);
        if ($this->_db->one_result()->counter > 0) {
            return true;
        }
        return false;
    }

    //method return all the class_ids for a level
    public function getLevelClasses($level, $sch_abbr = ''): array
    {
        $sql = 'select id from class where level=?';
        $placeholders = [$level];
        if (!empty($sch_abbr)) {
            $sql .= ' and sch_abbr = ?';
            $placeholders[] = $sch_abbr;
        }
        $this->_db->query($sql, $placeholders);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        } else {
            return [];
        }
    }




    //this method helps to assign a class to a teacher. 
    public function assignClass($classId, $staffId)
    {
        $this->_db->query('update class set teacher_id=? where id=?', [$staffId, $classId]);
    }

    //this method helps to unassign a class to a teacher. 
    public function unAssignClass($staffId)
    {
        $this->_db->query('update class set teacher_id=? where teacher_id=?', [null, $staffId]);
    }


    public function getClassTeacherId($classId)
    {
        $this->_db->query('select staff_id from staff where class_id =?', [$classId]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->one_result()->staff_id;
        }
    }

    public function classHasATeacher($classId): bool
    {
        $this->_db->query('select teacher_id from class where id = ?', [$classId]);
        $res = $this->_db->one_result()->teacher_id;
        return (!empty($res)) ? true : false;
    }



    public function getStaffNames($staffId)
    {
        $this->_db->query('select fname,lname,oname, title from staff where staff_id=?', [$staffId]);
        return $this->_db->one_result();
    }

    //this method deletes a class, it also deletes all the relationship it has in other tables
    public function deleteClass($classId, $sch_abbr): bool
    {
        //delete from class table
        $sql1 = 'delete from class where id = ?';
        $val1 = [$classId];
        //delete from the other tables
        $sql2 = 'delete from subject2 where class_id=?';
        $val2 = [$classId];
        //delete from score table
        $utils = new Utils();
        $formSession = $utils->getFormatedSession($sch_abbr);
        $sql3 = 'delete from ' . $formSession . '_score where class_id = ?';
        $val3 = [$classId];
        //delete from the other tables(class_psy)
        $sql4 = 'delete from class_psy where class_id=?';
        $val4 = [$classId];
        return $this->_db->trans_query([[$sql1, $val1], [$sql2, $val2], [$sql3, $val3], [$sql4, $val4]]);
    }

    //this method deletes a subject, it also deletes all the relationship it has in other tables
    public function deleteSubject($subjectId, $sch_abbr): bool
    {
        //delete from the subject tables
        $sql1 = 'delete from subject2 where id=?';
        $val1 = [$subjectId];
        //delete from score table
        $utils = new Utils();
        $formSession = $utils->getFormatedSession($sch_abbr);
        $sql2 = 'delete from ' . $formSession . '_score where subject_id = ?';
        $val2 = [$subjectId];
        return $this->_db->trans_query([[$sql1, $val1], [$sql2, $val2]]);
    }


    public function getClassAndLevel($classId)
    {
        $this->_db->query('select level,class from class where id = ?', [$classId]);
        return $this->_db->one_result();
    }

    public function isSubjectTeacher($teacherId, $subjectId=''): bool
    {
        if($subjectId = ''){
            $this->_db->query('select count(id) as counter from subject2 where teacher_id=?', [$teacherId]);
            return ($this->_db->one_result()->counter > 0) ? true : false;
        }
        $this->_db->query('select count(id) as counter from subject2 where teacher_id=? and id=?', [$teacherId, $subjectId]);
        return ($this->_db->one_result()->counter > 0) ? true : false;
    }

    public function subjectHasTeacher($subjectId): bool
    {
        $this->_db->query('select teacher_id from subject2 where id=?', [$subjectId]);
        return (!empty($this->_db->one_result()->teacher_id)) ? true : false;
    }

    function getNoStudentsNeedsClass($sch_abbr): int
    {
        $this->_db->query('select count(id) as counter from student where class_id IS NULL and sch_abbr = ?', [$sch_abbr]);
        return $this->_db->one_result()->counter;
    }

    function getStudentsNeedsClass($sch_abbr, $level = null)
    {
        $this->_db->query('select id,std_id, fname,oname,lname from student where class_id IS NULL and sch_abbr = ? and level = ?', [$sch_abbr, $level]);
        return $this->_db->get_result();
    }

    function getSchedule($sch_abbr)
    {
        $this->_db->query('select * from school2 where sch_abbr = ?', [$sch_abbr]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->one_result();
        }
        return [];
    }

    function updateSchedule($sch_abbr, $fa, $sa, $ft, $st, $pro, $exam, $ftto, $stto, $ttto, $ftrd, $strd, $ttrd, $ftcd, $stcd, $ttcd, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $signature = null): bool
    {
        if ($signature !== null) {
            return $this->_db->query('update school2 set fa=?,sa=?,ft=?,st=?,pro=?,exam=?,ft_times_opened=?,st_times_opened=?,tt_times_opened=?,ft_res_date=?,st_res_date=?,tt_res_date=?,'
                . 'ft_close_date=?,st_close_date=?,tt_close_date=?,a1=?,b2=?,b3=?,c4=?,c5=?,c6=?,d7=?,e8=?,f9=?,signature=? where sch_abbr=?', [$fa, $sa, $ft, $st, $pro, $exam, $ftto, $stto, $ttto, $ftrd, $strd, $ttrd, $ftcd, $stcd, $ttcd, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $signature, $sch_abbr]);
        }
        return $this->_db->query('update school2 set fa=?,sa=?,ft=?,st=?,exam=?,pro=?,ft_times_opened=?,st_times_opened=?,tt_times_opened=?,ft_res_date=?,st_res_date=?,tt_res_date=?,'
            . 'ft_close_date=?,st_close_date=?,tt_close_date=?,a1=?,b2=?,b3=?,c4=?,c5=?,c6=?,d7=?,e8=?,f9=? where sch_abbr=?', [$fa, $sa, $ft, $st, $pro, $exam, $ftto, $stto, $ttto, $ftrd, $strd, $ttrd, $ftcd, $stcd, $ttcd, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $sch_abbr]);
    }

    //this function returns some basic details for a class
    function getClassDetail($classId)
    {
        $this->_db->query('select class.*, staff.title, staff.fname, staff.lname, staff.oname from class left join staff on class.teacher_id = staff.staff_id where class.id=?', [$classId]);
        return $this->_db->one_result();
    }


    //this function get some teacher details
    function getTeacherDetail($teacher_id)
    {
        $this->_db->query('select title,fname,lname,oname from staff where staff_id =?', [$teacher_id]);
        return $this->_db->one_result();
    }

    //this function checks if all students of a school have completed course registration
    function isStdsSubRegComplete($sch_abbr)
    {
        $this->_db->query('select count(id) as counter from student where class_id is NOT NULL and sub_reg_comp is false and active = true and sch_abbr=?', [$sch_abbr]);
        return ($this->_db->one_result()->counter) ? false : true;
    }

    //this function gets all students of a school that have not completed course registration
    function getStdsNotCompSubReg($sch_abbr)
    {
        $this->_db->query('select student.fname,student.lname,student.oname,student.std_id,student.level,class.class from student inner join class on student.class_id = class.id where student.class_id is NOT NULL and student.sub_reg_comp = false and student.sch_abbr=?', [$sch_abbr]);
        return $this->_db->get_result();
    }

    function getClassSameLevel($ClassId, $sch_abbr)
    {
        $this->_db->query('select level from class where id=?', [$ClassId]);
        $level = (int) $this->_db->one_result()->level;
        $this->_db->query('select id,sch_abbr,level,class,teacher_id from class where not id=? and level=? and sch_abbr=?', [$ClassId, $level, $sch_abbr]);
        return $this->_db->get_result();
    }

    //this function helps reset the score table for a particular school after the score settings have been changed
    function updateScore($scoreTable, $currTerm)
    {
        switch ($currTerm) {
            case 'ft':
                $this->_db->query("update $scoreTable inner join subject2 on $scoreTable.subject_id = subject2.id set $scoreTable.ft_fa=?,$scoreTable.ft_sa=?,$scoreTable.ft_ft=?,$scoreTable.ft_st=?,$scoreTable.ft_pro=?,$scoreTable.ft_ex=?,$scoreTable.ft_tot=?", [null, null, null, null, null, null, null]);
                break;
            case 'st':
                $this->_db->query("update $scoreTable inner join subject2 on $scoreTable.subject_id = subject2.id set $scoreTable.st_fa=?,$scoreTable.st_sa=?,$scoreTable.st_ft=?,$scoreTable.st_st=?,$scoreTable.st_pro=?,$scoreTable.st_ex=?,$scoreTable.st_tot=?", [null, null, null, null, null, null, null]);
                break;
            case 'tt':
                $this->_db->query('update ' . $scoreTable . ' set tt_fa=?,tt_sa=?,tt_ft=?,tt_st=?,tt_pro=?,tt_ex=?,tt_tot=? where sch_abbr=?', [null, null, null, null, null, null, null]);
                $this->_db->query("update $scoreTable inner join subject2 on $scoreTable.subject_id = subject2.id set $scoreTable.ft_fa=?,$scoreTable.ft_sa=?,$scoreTable.ft_ft=?,$scoreTable.tt_st=?,$scoreTable.tt_pro=?,$scoreTable.tt_ex=?,$scoreTable.tt_tot=?", [null, null, null, null, null, null, null]);
        }
    }
}
