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

    //this function helps instantiate Util class
    function instantUtil()
    {
        $this->_util = new Utils();
    }

    

    //this function returns the class teacher id
    function getClassTeacherId($classId)
    {
        $this->_db->query('select teacher_id from class where id = ?', [$classId]);
        return $this->_db->one_result()->teacher_id;
    }

   


    function classHasSubject($classId): bool
    {
        $this->_db->query('select count(id) as counter from subject where class_id = ?', [$classId]);
        return ($this->_db->one_result()->counter > 0) ? true : false;
    }

    public static function getStudentsWithComments($classId, $term, $std_id = '')
    {
        $db = DB::get_instance();
        if ($std_id === '') {
           $db->query('select student.std_id,student.fname,student.lname,student.oname, student_psy.' . $term . '_com, student_psy.' . $term . '_p_com from student inner join student_psy on student_psy.std_id = student.std_id where student.class_id=?', [$classId]);
        } else {

           $db->query('select student.std_id,student.fname,student.lname,student.oname, student_psy.' . $term . '_com, student_psy.' . $term . '_p_com from student inner join student_psy on student_psy.std_id = student.std_id where student.class_id=? and student.std_id=?', [$classId, $std_id]);
        }
        if ($db->row_count() > 0) {
            return$db->get_result();
        }
        return [];
    }

}
