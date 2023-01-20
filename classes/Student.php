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
        $utils = new Utils();
        $session = $utils->getSession($sch_abbr);
        //get student count and increement by 1
        $count =  Ses::getStudentsCount($sch_abbr,$session) + 1;
        
        $preZeros = '';
        $preZerosCount = 3 - strlen((string)$count);
        for ($i = 1; $i <= $preZerosCount; $i++) {
            $preZeros .= '0';
        }
        //get current session year
        $utils = new Utils();
        $currSession = $utils->getSession($sch_abbr);
        $currSessionYear = explode('/', $currSession)[0];
        return $sch_abbr . '/' . substr($currSessionYear, -2) . '/' . $level . '/' . $preZeros . $count;
    }

    public function getCurrentSession($sch)
    {
        $utils = new Utils();
        return $utils->getSession($sch);
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
            $db->query('select student.std_id,student.fname,student.lname,student.oname, student_psy.' . $term . '_com, student_psy.' . $term . '_p_com from student inner join student_psy on student_psy.std_id = student.std_id where student.class_id=? and student.active = 1', [$classId]);
        } else {

            $db->query('select student.std_id,student.fname,student.lname,student.oname, student_psy.' . $term . '_com, student_psy.' . $term . '_p_com from student inner join student_psy on student_psy.std_id = student.std_id where student.class_id=? and student.std_id=? and student.active = 1', [$classId, $std_id]);
        }
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }


    public static function getStudents(string|int $classId): array
    {
        $db = DB::get_instance();
        return  $db->select('student', 'fname,oname,lname,std_id,class_id', "class_id = '$classId' and active=1");
    }
}
