<?php
class Result
{
    private $_term, $_session, $_db, $_db2;
    private $_subject_table, $_subject_table2, $_class_table, $_std_psy_table, $_school2_table, $_score_table, $_class_psy_table;
    private $_has_agg_prep_stmt = false;
    public function __construct($session, $term, $sch_abbr)
    {
        $this->_term = $term;
        $this->_session = $session;
        $this->_db = DB::get_instance();
        $this->_db2 = DB::get_instance2();
        $util = new Utils();
        $current_session = $util->getSession($sch_abbr);
        $formatted_session = Utility::getFormatedSession($session);
        $this->_score_table = $formatted_session . '_score';
        if ($current_session === $session) {
            $this->_subject_table = 'subject';
            $this->_subject_table2 = 'subject2';
            $this->_class_table = 'class';
            $this->_std_psy_table = 'student_psy';
            $this->_school2_table = 'school2';
            $this->_class_psy_table = 'class_psy';
        } else {
            $this->_subject_table = $formatted_session . '_subject';
            $this->_subject_table2 = $formatted_session . '_subject2';
            $this->_class_table = $formatted_session . '_class';
            $this->_std_psy_table = $formatted_session . '_student_psy';
            $this->_school2_table = $formatted_session . '_school2';
            $this->_class_psy_table = $formatted_session . '_class_psy';
        }
    }



    function get_scores($student_id)
    {
        $db = $this->_db;
        $db->query('select ' .  $this->_score_table . '.' . $this->_term . '_fa,' . $this->_score_table . '.' . $this->_term . '_sa,' . $this->_score_table . '.' . $this->_term . '_ft,' . $this->_score_table . '.' . $this->_term . '_st,' . $this->_score_table . '.' . $this->_term . '_pro,' . $this->_score_table . '.' . $this->_term . '_ex,' . $this->_score_table . '.' . $this->_term . '_tot,' . $this->_subject_table . '.subject from ' . $this->_subject_table . ' left join ' . $this->_subject_table2 . ' on ' . $this->_subject_table . '.id = ' . $this->_subject_table2 . '.subject_id inner join ' . $this->_score_table . ' on ' . $this->_subject_table2 . '.id = ' . $this->_score_table . '.subject_id where ' . $this->_score_table . '.std_id=?', [$student_id]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }


    function get_aggregate_data($student_id, $sch_abbr)
    {
        $db = $this->_db2;
        if (!$this->_has_agg_prep_stmt) {
            $db->query('select student.fname,student.oname,student.lname,student.level,student.sch_abbr,student.picture, school.address,school.phone_contacts,school.email,school.logo,' . $this->_std_psy_table . '.' . $this->_term . '_height_beg,' . $this->_std_psy_table . '.' . $this->_term . '_height_end,' . $this->_std_psy_table . '.' . $this->_term . '_weight_beg,' . $this->_std_psy_table . '.' . $this->_term . '_weight_end,' . $this->_std_psy_table . '.' . $this->_term . '_times_present,' . $this->_std_psy_table . '.' . $this->_term . '_com,' . $this->_std_psy_table . '.' . $this->_term . '_p_com,' . $this->_std_psy_table . '.' . $this->_term . '_psy1,' . $this->_std_psy_table . '.' . $this->_term . '_psy2,' . $this->_std_psy_table . '.' . $this->_term . '_psy3,' . $this->_std_psy_table . '.' . $this->_term . '_psy4,' . $this->_std_psy_table . '.' . $this->_term . '_psy5,' . $this->_std_psy_table . '.' . $this->_term . '_psy6,' . $this->_std_psy_table . '.' . $this->_term . '_psy7,' . $this->_std_psy_table . '.' . $this->_term . '_psy8,' . $this->_std_psy_table . '.' . $this->_term . '_psy9,' . $this->_std_psy_table . '.' . $this->_term . '_psy10,' . $this->_std_psy_table . '.' . $this->_term . '_psy11,' . $this->_std_psy_table . '.' . $this->_term . '_psy12,' . $this->_std_psy_table . '.' . $this->_term . '_psy13,' . $this->_std_psy_table . '.' . $this->_term . '_psy14,' . $this->_std_psy_table . '.' . $this->_term . '_psy15,' . $this->_std_psy_table . '.' . $this->_term . '_psy16,' . $this->_std_psy_table . '.' . $this->_term . '_psy17,' . $this->_std_psy_table . '.' . $this->_term . '_psy18,' . $this->_std_psy_table . '.' . $this->_term . '_psy19,' . $this->_std_psy_table . '.' . $this->_term . '_psy20,' . $this->_class_table . '.class,' . $this->_class_table . '.id as class_id,' . $this->_class_table . '.petname, avg(' . $this->_score_table . '.' . $this->_term . '_tot) as average, sum(' . $this->_score_table . '.' . $this->_term . '_tot) as total,' . $this->_school2_table . '.hijra_session,' . $this->_school2_table . '.' . $this->_term . '_res_date,' . $this->_school2_table . '.' . $this->_term . '_times_opened,' . $this->_school2_table . '.signature as hos_signature,' . $this->_class_psy_table . '.signature as tea_signature from ' . $this->_class_psy_table . ' left join ' . $this->_score_table . ' on ' . $this->_class_psy_table . '.class_id = ' . $this->_score_table . '.class_id inner join ' . $this->_school2_table . ' on ' . $this->_class_psy_table . '.sch_abbr = ' . $this->_school2_table . '.sch_abbr inner join student on student.std_id = ' . $this->_score_table . '.std_id inner join ' . $this->_std_psy_table . ' on ' . $this->_std_psy_table . '.std_id = student.std_id inner join ' . $this->_class_table . ' on ' . $this->_class_table . '.id = ' . $this->_class_psy_table . '.class_id inner join school on ' . $this->_school2_table . '.sch_abbr = school.sch_abbr where ' . $this->_score_table . '.std_id=? and school.sch_abbr = ?', [$student_id, $sch_abbr]);
            $this->_has_agg_prep_stmt = true;
        }else{
            $db->requery([$student_id, $sch_abbr]);
        }
        return $db->one_result();
    }

    function get_student_count($class_id)
    {
        $db = $this->_db;
        $db->query('select count(class_id) as counter from student where class_id=?',[$class_id]);
        return $db->one_result()->counter;
    }

    public static function get_grade($score): string
    {
        if ($score <= 100 && $score >= 74.5) {
            return 'A1';
        } elseif ($score < 74.5 && $score >= 69.5) {
            return 'B2';
        } elseif ($score < 69.5 && $score >= 64.5) {
            return 'B3';
        } elseif ($score < 64.5 && $score >= 59.5) {
            return 'C4';
        } elseif ($score < 59.5 && $score >= 54.5) {
            return 'C5';
        } elseif ($score < 54.5 && $score >= 49.5) {
            return 'C6';
        } elseif ($score < 49.5 && $score >= 44.5) {
            return 'D7';
        } elseif ($score < 44.5 && $score >= 39.5) {
            return 'E8';
        } elseif ($score < 39.5 && $score >= 0) {
            return 'F9';
        }
        return 'invalid score';
    }

    public static function get_remark(string $grade)
    {
        switch (strtoupper($grade)) {
            case 'A1':
                return 'Excellent';
            case 'B2':
                return 'Very Good';
            case 'B3':
                return 'Good';
            case 'C4':
                return 'Upper Credit';
            case 'C5':
                return 'Credit';
            case 'C6':
                return 'Lower Credit';
            case 'D7':
                return 'Pass';
            case 'E8':
                return 'Poor';
            case 'F9':
                return 'Fail';
        }
    }
}
