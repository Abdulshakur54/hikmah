<?php
class Staff extends User
{

    public function __construct()
    {
        parent::__construct($cat = 2);
    }

    //this method gets an id for management member after increement by 1
    public static function genId()
    {
        $agg = new Aggregate();
        $id = $agg->lookUp('staff_count', 'sing_val', 'id,=,1') + 1; //gets the current no and increement by 1
        $agg->edit($id, 'staff_count', 'sing_val', 'id,=,1'); //updates the admission count
        $preZeros = '';
        $preZerosCount = 3 - strlen((string)$id);
        for ($i = 1; $i <= $preZerosCount; $i++) {
            $preZeros .= '0';
        }
        return $preZeros . $id;
    }

    public function getClassWithId($staffId)
    {
        $this->_db->query('select class,id,level from class where teacher_id=?', [$staffId]);
        return ($this->_db->row_count()) ? $this->_db->one_result() : '';
    }


    //this method returns all the subject a teacher is taking
    public function getSubjectsWithIds($staffId)
    {
        $this->_db->query('select subject.subject,subject2.id,subject2.class_id,subject.level,subject2.teacher_id,class.class from subject inner join subject2 on subject.id = subject2.subject_id inner join class '
            . 'on subject2.class_id = class.id where subject2.teacher_id = ? order by subject.subject', [$staffId]);
        return ($this->_db->row_count()) ? $this->_db->get_result() : '';
    }

    function getSchedule($classId)
    {
        $this->_db->query('select * from class_psy where class_id = ?', [$classId]);
        return $this->_db->one_result();
    }

    function updateSchedule($ft_passmark, $st_passmark, $tt_passmark, $classId, $punc, $hon, $dhw, $rap, $sot, $rwp, $ls, $atw, $ho, $car, $con, $wi, $ob, $hea, $vs, $pig, $pis, $ac, $pama, $ms, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $height_beg, $height_end, $weight_beg, $weight_end, $signature = null): bool
    {
        if ($signature !== null) {
            return $this->_db->query('update class_psy set ft_passmark=?, st_passmark=?, tt_passmark=?, psy1=?,psy2=?,psy3=?,psy4=?,psy5=?,psy6=?,psy7=?,psy8=?,psy9=?,psy10=?,psy11=?,psy12=?,psy13=?,psy14=?,psy15=?,psy16=?,psy17=?,psy18=?,psy19=?,psy20=?,a1=?,b2=?,b3=?,c4=?,c5=?,c6=?,d7=?,e8=?,f9=?,height_beg=?,height_end=?,weight_beg=?,weight_end=?,signature=? where class_id=?', [$ft_passmark, $st_passmark, $tt_passmark, $punc, $hon, $dhw, $rap, $sot, $rwp, $ls, $atw, $ho, $car, $con, $wi, $ob, $hea, $vs, $pig, $pis, $ac, $pama, $ms, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $height_beg, $height_end, $weight_beg, $weight_end, $signature, $classId]);
        }
        return $this->_db->query('update class_psy set ft_passmark=?, st_passmark=?, tt_passmark=?, psy1=?,psy2=?,psy3=?,psy4=?,psy5=?,psy6=?,psy7=?,psy8=?,psy9=?,psy10=?,psy11=?,psy12=?,psy13=?,psy14=?,psy15=?,psy16=?,psy17=?,psy18=?,psy19=?,psy20=?,a1=?,b2=?,b3=?,c4=?,c5=?,c6=?,d7=?,e8=?,f9=?,height_beg=?,height_end=?,weight_beg=?,weight_end=? where class_id=?', [$ft_passmark, $st_passmark, $tt_passmark, $punc, $hon, $dhw, $rap, $sot, $rwp, $ls, $atw, $ho, $car, $con, $wi, $ob, $hea, $vs, $pig, $pis, $ac, $pama, $ms, $a1, $b2, $b3, $c4, $c5, $c6, $d7, $e8, $f9, $height_beg, $height_end, $weight_beg, $weight_end, $classId]);
    }

    function isASubjectTeacher($username): bool
    {
        $this->_db->query('select count(id) as counter from subject2 where teacher_id=?', [$username]);
        return ($this->_db->one_result()->counter > 0) ? true : false;
    }
    function isAClassTeacher($username): bool
    {
        $this->_db->query('select count(id) as counter from class where teacher_id=?', [$username]);
        return ($this->_db->one_result()->counter > 0) ? true : false;
    }


    //this function checks if all students of a class have completed course registration
    function isStdsSubRegComplete($classId)
    {
        $this->_db->query('select count(id) as counter from student where class_id = ? and sub_reg_comp = false', [$classId]);
        return ($this->_db->one_result()->counter) ? false : true;
    }

    //this function gets all students of a class that have not completed course registration
    function getStdsNotCompSubReg($classId)
    {
        $this->_db->query('select fname,lname,oname,std_id from student where class_id=? and sub_reg_comp = false', [$classId]);
        return $this->_db->get_result();
    }

    //this function updates student psycometry
    function updateStdPsy($std_id, $values)
    {
        $this->_db->update('student_psy', $values, "std_id='$std_id'");
    }

    function getStdPsyData(string $std_id, $term)
    {
        $sql = 'select student.fname,student.oname,student.lname,student.std_id';
        for ($i = 1; $i <= 20; $i++) {
            $sql .= ',student_psy.' . $term . '_psy' . $i;
        }
        $sql .= ',student_psy.' . $term . '_height_beg';
        $sql .= ',student_psy.' . $term . '_height_end';
        $sql .= ',student_psy.' . $term . '_weight_beg';
        $sql .= ',student_psy.' . $term . '_weight_end';
        $sql .= ',student_psy.' . $term . '_com';
        $sql.=' from student inner join student_psy on student.std_id = student_psy.std_id where student.std_id = ?';
        $this->_db->query($sql,[$std_id]);
        return $this->_db->one_result();
    }


    function getStudents($classId)
    {
        $this->_db->query('select std_id,fname,lname,oname from student where class_id=? order by fname,lname,oname asc', [$classId]);
        return $this->_db->get_result();
    }
   

    function getStudentsIds($classId)
    {
        $this->_db->query('select std_id from student where class_id=?  order by fname,lname,oname asc', [$classId]);
        $ids = [];
        if ($this->_db->row_count() > 0) {
            $res = $this->_db->get_result();
            foreach ($res as $r) {
                $ids[] = $r->std_id;
            }
            return $ids;
        }
    }

    //this function returns true if a staff is a subject teacher of the provided subject
    function isSubjectTeacher($staffId, $subId): bool
    {
        $this->_db->query('select count(id) as counter from subject2 where teacher_id=? and id=?', [$staffId, $subId]);
        return ($this->_db->one_result()->counter > 0) ? true : false;
    }
}
