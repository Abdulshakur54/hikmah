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
    function get_ses_scores($student_id)
    {
        $db = $this->_db;
        $db->query('select ' .  $this->_score_table . '.ft_tot,' .  $this->_score_table . '.st_tot,' .  $this->_score_table . '.tt_tot,' . $this->_subject_table . '.subject from ' . $this->_subject_table . ' left join ' . $this->_subject_table2 . ' on ' . $this->_subject_table . '.id = ' . $this->_subject_table2 . '.subject_id inner join ' . $this->_score_table . ' on ' . $this->_subject_table2 . '.id = ' . $this->_score_table . '.subject_id where ' . $this->_score_table . '.std_id=?', [$student_id]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }


    function get_aggregate_data($student_id)
    {
        $db = $this->_db2;
        if (!$this->_has_agg_prep_stmt) {
            $sql = 'select student.fname,student.oname,student.lname,student.level,student.sch_abbr,student.picture, school.address,school.phone_contacts,school.email,school.logo,' . $this->_class_psy_table . '.a1,'
                . $this->_class_psy_table . '.b2,' . $this->_class_psy_table . '.b3,' . $this->_class_psy_table . '.c4,' . $this->_class_psy_table . '.c5,' . $this->_class_psy_table . '.c6,' . $this->_class_psy_table . '.d7,' . $this->_class_psy_table . '.e8,' . $this->_class_psy_table .
                '.f9,' . $this->_school2_table . '.a1 as hos_a1,'
                . $this->_school2_table . '.b2 as hos_b2,' . $this->_school2_table . '.b3 as hos_b3,' . $this->_school2_table . '.c4 as hos_c4,' . $this->_school2_table . '.c5 as hos_c5,' . $this->_school2_table . '.c6 as hos_c6,' . $this->_school2_table . '.d7 as hos_d7,' . $this->_school2_table . '.e8 as hos_e8,' . $this->_school2_table .
                '.f9 as hos_f9,' . $this->_class_psy_table .
                '.psy1,' . $this->_class_psy_table .
                '.psy2,' . $this->_class_psy_table .
                '.psy3,' . $this->_class_psy_table .
                '.psy4,' . $this->_class_psy_table .
                '.psy5,' . $this->_class_psy_table .
                '.psy6,' . $this->_class_psy_table .
                '.psy7,' . $this->_class_psy_table .
                '.psy8,' . $this->_class_psy_table .
                '.psy9,' . $this->_class_psy_table .
                '.psy10,' . $this->_class_psy_table .
                '.psy11,' . $this->_class_psy_table .
                '.psy12,' . $this->_class_psy_table .
                '.psy13,' . $this->_class_psy_table .
                '.psy14,' . $this->_class_psy_table .
                '.psy15,' . $this->_class_psy_table .
                '.psy16,' . $this->_class_psy_table .
                '.psy17,' . $this->_class_psy_table .
                '.psy18,' . $this->_class_psy_table .
                '.psy19,' . $this->_class_psy_table .
                '.psy20,' . $this->_class_psy_table . '.height_beg,' . $this->_class_psy_table . '.height_end,' . $this->_class_psy_table . '.weight_beg,' . $this->_class_psy_table . '.weight_end,' .
                $this->_std_psy_table . '.' . $this->_term . '_height_beg,' . $this->_std_psy_table . '.' . $this->_term . '_height_end,' . $this->_std_psy_table . '.' . $this->_term . '_weight_beg,' . $this->_std_psy_table . '.' . $this->_term . '_weight_end,' . $this->_std_psy_table . '.' . $this->_term . '_times_present,' . $this->_std_psy_table . '.' . $this->_term . '_com,' . $this->_std_psy_table . '.' . $this->_term . '_p_com,' . $this->_std_psy_table . '.' . $this->_term . '_psy1,' . $this->_std_psy_table . '.' . $this->_term . '_psy2,' . $this->_std_psy_table . '.' . $this->_term . '_psy3,' . $this->_std_psy_table . '.' . $this->_term . '_psy4,' . $this->_std_psy_table . '.' . $this->_term . '_psy5,' . $this->_std_psy_table . '.' . $this->_term . '_psy6,' . $this->_std_psy_table . '.' . $this->_term . '_psy7,' . $this->_std_psy_table . '.' . $this->_term . '_psy8,' . $this->_std_psy_table . '.' . $this->_term . '_psy9,' . $this->_std_psy_table . '.' . $this->_term . '_psy10,' . $this->_std_psy_table . '.' . $this->_term . '_psy11,' . $this->_std_psy_table . '.' . $this->_term . '_psy12,' . $this->_std_psy_table . '.' . $this->_term . '_psy13,' . $this->_std_psy_table . '.' . $this->_term . '_psy14,' . $this->_std_psy_table . '.' . $this->_term . '_psy15,' . $this->_std_psy_table . '.' . $this->_term . '_psy16,' . $this->_std_psy_table . '.' . $this->_term . '_psy17,' . $this->_std_psy_table . '.' . $this->_term . '_psy18,' . $this->_std_psy_table . '.' . $this->_term . '_psy19,' . $this->_std_psy_table . '.' . $this->_term . '_psy20,' . $this->_class_table . '.class,' . $this->_class_psy_table . '.' . $this->_term . '_passmark,' . $this->_class_table . '.id as class_id,' . $this->_class_table . '.petname, avg(' . $this->_score_table . '.' . $this->_term . '_tot) as average, sum(' . $this->_score_table . '.' . $this->_term . '_tot) as total,' . $this->_school2_table . '.hijra_session,' . $this->_school2_table . '.' . $this->_term . '_res_date,' . $this->_school2_table . '.' . $this->_term . '_times_opened,' . $this->_school2_table . '.signature as hos_signature,' . $this->_class_psy_table . '.signature as tea_signature from ' . $this->_class_psy_table . ' left join ' . $this->_score_table . ' on ' . $this->_class_psy_table . '.class_id = ' . $this->_score_table . '.class_id inner join ' . $this->_school2_table . ' on ' . $this->_class_psy_table . '.sch_abbr = ' . $this->_school2_table . '.sch_abbr inner join student on student.std_id = ' . $this->_score_table . '.std_id inner join ' . $this->_std_psy_table . ' on ' . $this->_std_psy_table . '.std_id = student.std_id inner join ' . $this->_class_table . ' on ' . $this->_class_table . '.id = ' . $this->_class_psy_table . '.class_id inner join school on ' . $this->_school2_table . '.sch_abbr = school.sch_abbr where ' . $this->_score_table . '.std_id=?';

            $db->query($sql, [$student_id]);

            $this->_has_agg_prep_stmt = true;
        } else {
            $db->requery([$student_id]);
        }
        return $db->one_result();
    }
    function get_ses_aggregate_data($student_id)
    {
        $db = $this->_db2;
        if (!$this->_has_agg_prep_stmt) {
            $sql = 'select student.fname,student.oname,student.lname,student.level,student.picture,' . $this->_std_psy_table . '.ft_times_present,' . $this->_std_psy_table . '.st_times_present,' . $this->_std_psy_table . '.tt_times_present,' . $this->_school2_table . '.ft_times_opened,' . $this->_school2_table . '.st_times_opened,' . $this->_school2_table . '.tt_times_opened,' . $this->_school2_table . '.hijra_session,' . $this->_school2_table . '.signature as hos_signature,' . $this->_class_psy_table . '.signature as tea_signature,' . $this->_class_table . '.class,' . $this->_class_psy_table . '.ft_passmark,' . $this->_class_psy_table . '.st_passmark,' . $this->_class_psy_table . '.tt_passmark from student inner join ' . $this->_std_psy_table . ' on student.std_id = ' . $this->_std_psy_table . '.std_id inner join ' . $this->_school2_table . ' on student.sch_abbr = ' . $this->_school2_table . '.sch_abbr inner join ' . $this->_class_psy_table . ' on student.class_id = ' . $this->_class_psy_table . '.class_id inner join ' . $this->_class_table . ' on student.class_id = ' . $this->_class_table . '.id where student.std_id = ?';
            $db->query($sql, [$student_id]);
            $this->_has_agg_prep_stmt = true;
        } else {
            $db->requery([$student_id]);
        }
        return $db->one_result();
    }

    function get_student_count($class_id)
    {
        $db = $this->_db;
        $db->query('select count(class_id) as counter from student where class_id=?', [$class_id]);
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
        } elseif ($score < 39.5 && $score >= 0 && !is_null($score)) {
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

    public static function get_ids($class_id, $sch_abbr)
    {
        $db = DB::get_instance();
        $util = new Utils();
        $formatted_session = $util->getFormatedSession($sch_abbr);
        $db->query('select distinct std_id from ' . $formatted_session . '_score where class_id=?', [$class_id]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }

    public static function get_grades_summary(array $scores): array
    {
        $summary = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0];
        foreach ($scores as $score) {
            $grade = self::get_grade($score);
            switch ($grade) {
                case 'A1':
                    $summary['A'] += 1;
                    break;
                case 'B2':
                case 'B3':
                    $summary['B'] += 1;
                    break;
                case 'C4':
                case 'C5':
                case 'C6':
                    $summary['C'] += 1;
                    break;
                case 'D7':
                    $summary['D'] += 1;
                    break;
                case 'E8':
                    $summary['E'] += 1;
                    break;
                case 'F9':
                    $summary['F'] += 1;
                    break;
                default:
            }
        }
        return $summary;
    }

    public static function format_grade_summary(array $summary, bool $html_output = false): string
    {
        $formatted_summary = '';
        foreach ($summary as $alp => $count) {
            $space = ($html_output) ? '&nbsp; &nbsp; &nbsp;' : '   ';
            if ($count > 0) {
                if ($count === 1) {
                    $formatted_summary .= $space . $count . ' ' . $alp;
                } else {
                    $formatted_summary .= $space . $count . ' ' . $alp . 's';
                }
            }
        }
        return trim($formatted_summary);
    }
    public static function get_class_term_totals($session, $class_id, $term)
    {
        $db = DB::get_instance();
        $score_table = Utility::getFormatedSession($session) . '_score';
        $db->query('select ' . $score_table . '.' . $term . '_tot from ' . $score_table . ' inner join student on student.std_id = ' . $score_table . '.std_id where student.class_id = ?', [$class_id]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }

    public static function get_class_ses_totals($session, $class_id)
    {
        $db = DB::get_instance();
        $score_table = Utility::getFormatedSession($session) . '_score';
        $db->query('select ' . $score_table . '.ft_tot,' . $score_table . '.st_tot,' . $score_table . '.tt_tot from ' . $score_table . ' inner join student on student.std_id = ' . $score_table . '.std_id where student.class_id = ?', [$class_id]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }
    public static function get_student_term_totals($session, $std_id, $term, $requery = false): array
    {
        $db = DB::get_instance();
        if ($requery) {
             $db->requery([$std_id]);
        } else {

            $score_table = Utility::getFormatedSession($session) . '_score';
            $db->query('select ' . $term . '_tot from ' . $score_table . ' where std_id = ?', [$std_id]);
        }
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }
    public static function get_student_ses_totals($session, $std_id, $requery = false): array
    {
        $db = DB::get_instance();
        if($requery){
            $db->requery([$std_id]);
        }else{
            $score_table = Utility::getFormatedSession($session) . '_score';
            $db->query('select ft_tot,st_tot,tt_tot from ' . $score_table . ' where std_id = ?', [$std_id]);
        }
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
       
    }
}
