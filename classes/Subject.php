<?php

/**
 * Description of Subject
 *
 * @author Abdulshakur
 */
class Subject
{
    private $_db, $_id, $_table, $_scoreSettings;
    private $_updScrQry = false;
    private $_updScrQrySql = ''; //this is to store the sql statement for update query

    private $_isRegisteredPreparedStatement = false;

    public function __construct($id = null, $table = null, $scoreSettings = null)
    {
        if (!empty($id)) {
            $this->_id = $id;
        }
        if (!empty($table)) {
            $this->_table = $table;
        }
        if (!empty($scoreSettings)) {
            $this->_scoreSettings = $scoreSettings;
        }
        $this->_db = DB::get_instance();
    }


    //this method returns the IDs of the student taking a particular subject as an indexed array
    function getStudentIds(): array
    {
        $this->_db->query('select std_id from ' . $this->_table . ' where subject_id=?', [$this->_id]);
        $res = $this->_db->get_result();
        $ids = [];
        if ($this->_db->row_count() > 0) {
            foreach ($res as $val) {
                $ids[] = $val->std_id;
            }
            return $ids;
        }
        return [];
    }

    //returns the minimun number of subject for a class
    function getMinNoSub($classID): int
    {
        $this->_db->query('select nos from class where id = ?', [$classID]);
        return $this->_db->one_result()->nos;
    }


    //this functtion returns the subject list available for registration for a class which a student belongs to as an associative array

    function getRegistrationList($classId): array
    {
        $this->_db->query('select subject.subject, subject2.id from subject inner join subject2 on subject.id = subject2.subject_id where subject2.class_id=?', [$classId]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }


    //this function register subjects for a particular student
    function registerSubjects($stdId, $subIds, $classId, $sch_abbr)
    {
        //insert rows into score table
        $this->insertScore($stdId, $subIds, $classId, $sch_abbr);
    }

    //this function inserts a record into the score table, instantUtil method has to be called to use this method, this is to help instantiate util Class
    private function insertScore($stdId, $subIds, $classId, $sch_abbr)
    {
        $util = new Utils();
        $currScoreTable = $util->getFormatedSession($sch_abbr) . '_score';
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


    //this function returns the registered subject ids for a particular student as an associative array
    public function getRegisteredSubjectsId($table, $stdId): array
    {
        $this->_db->query("select subject.subject, subject2.id from subject inner join subject2 on subject.id = subject2.subject_id inner join $table on subject2.id = $table.subject_id where $table.std_id=?", [$stdId]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }


    //this function returns an associative array of subjects with the id being the key and the name being the value
    function getSubjectNames(array $subIds): array
    {
        $this->_db->query('select subject.subject, subject2.id from subject inner join subject2 on subject.id = subject2.subject_id where subject2.id in(' . "'" . implode("','", $subIds) . "')");
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }

    function getScores($table, $term)
    {
        switch ($term) {
            case 'ft':
                $this->_db->query('select ' . $table . '.id,' . $table . '.std_id,' . $table . '.ft_fa,' . $table . '.ft_sa,' . $table . '.ft_ft,' . $table . '.ft_st,' . $table . '.ft_pro,' . $table . '.ft_ex, student.fname,student.oname,student.lname from ' . $table . ' inner join student on ' . $table . '.std_id = student.std_id where ' . $table . '.subject_id=?', [$this->_id]);
                break;
            case 'st':
                $this->_db->query('select ' . $table . '.id,' . $table . '.std_id,' . $table . '.st_fa,' . $table . '.st_sa,' . $table . '.st_ft,' . $table . '.st_st,' . $table . '.st_pro,' . $table . '.st_ex, student.fname,student.oname,student.lname from ' . $table . ' inner join student on ' . $table . '.std_id = student.std_id where subject_id=?'[$this->_id]);
                break;
            case 'tt':
                $this->_db->query('select ' . $table . '.id,' . $table . '.std_id,' . $table . '.tt_fa,' . $table . '.tt_sa,' . $table . '.tt_ft,' . $table . '.tt_st,' . $table . '.tt_pro,' . $table . '.tt_ex, student.fname,student.oname,student.lname from ' . $table . ' inner join student on ' . $table . '.std_id = student.std_id where subject_id=?'[$this->_id]);
                break;
        }
        return $this->_db->get_result();
    }


    //this function returns the score settings for a school
    function getScoreSettings($sch_abbr, $session = '')
    {
        if ($session != '') {
            $school_table = Utility::getFormatedSession($session) . '_school2';
        } else {
            $school_table = 'school2';
        }

        $this->_db->query('select fa,sa,ft,st,pro,exam from ' . $school_table . ' where sch_abbr=?', [$sch_abbr]);
        $res = $this->_db->one_result();
        $scoreSetting = [];
        $scoreSetting['fa'] = $res->fa;
        $scoreSetting['sa'] = $res->sa;
        $scoreSetting['ft'] = $res->ft;
        $scoreSetting['st'] = $res->st;
        $scoreSetting['pro'] = $res->pro;
        $scoreSetting['exam'] = $res->exam;
        return $scoreSetting;
    }

    /*
       this function return the needed column from scores table
      */
    function getNeededColumns($sch_abbr, $session = '')
    {
        $scoreSettings = $this->getScoreSettings($sch_abbr, $session);
        $columns = [];
        if ($scoreSettings['fa'] > 0) {
            $columns[] = 'fa';
        }
        if ($scoreSettings['sa'] > 0) {
            $columns[] = 'sa';
        }
        if ($scoreSettings['ft'] > 0) {
            $columns[] = 'ft';
        }
        if ($scoreSettings['st'] > 0) {
            $columns[] = 'st';
        }
        if ($scoreSettings['pro'] > 0) {
            $columns[] = 'pro';
        }
        if ($scoreSettings['exam'] > 0) {
            $columns[] = 'exam';
        }
        return $columns;
    }


    //this method is used to update scores table 
    //to use this function you must provide the third parameter when instantiating an object of this class
    function update($id, $scoreArr, $term)
    {
        if ($this->_updScrQry) {
            $param = $scoreArr;
            $param[] = Utility::sum($scoreArr); //append the sum of $scoreArr to $param
            $param[] = $id; //append score ID to $param
            $this->_db->requery($param);
        } else {
            if ($this->_updScrQrySql == '') {
                switch ($term) {
                    case 'ft':
                        $sql = 'update ' . $this->_table . ' set ';
                        if ($this->_scoreSettings['fa'] > 0) {
                            $sql .= 'ft_fa=?,';
                        }
                        if ($this->_scoreSettings['sa'] > 0) {
                            $sql .= 'ft_sa=?,';
                        }
                        if ($this->_scoreSettings['ft'] > 0) {
                            $sql .= 'ft_ft=?,';
                        }
                        if ($this->_scoreSettings['st'] > 0) {
                            $sql .= 'ft_st=?,';
                        }
                        if ($this->_scoreSettings['pro'] > 0) {
                            $sql .= 'ft_pro=?,';
                        }
                        if ($this->_scoreSettings['exam'] > 0) {
                            $sql .= 'ft_ex=?,';
                        }
                        $sql .= 'ft_tot=?';
                        break;
                    case 'st':
                        $sql = 'update ' . $this->_table . ' set ';
                        if ($this->_scoreSettings['fa'] > 0) {
                            $sql .= 'st_fa=?,';
                        }
                        if ($this->_scoreSettings['sa'] > 0) {
                            $sql .= 'st_sa=?,';
                        }
                        if ($this->_scoreSettings['ft'] > 0) {
                            $sql .= 'st_ft=?,';
                        }
                        if ($this->_scoreSettings['st'] > 0) {
                            $sql .= 'st_st=?,';
                        }
                        if ($this->_scoreSettings['pro'] > 0) {
                            $sql .= 'st_pro=?,';
                        }
                        if ($this->_scoreSettings['exam'] > 0) {
                            $sql .= 'st_ex=?,';
                        }
                        $sql .= 'st_tot=?';
                        break;
                    case 'tt':
                        $sql = 'update ' . $this->_table . ' set ';
                        if ($this->_scoreSettings['fa'] > 0) {
                            $sql .= 'tt_fa=?,';
                        }
                        if ($this->_scoreSettings['sa'] > 0) {
                            $sql .= 'tt_sa=?,';
                        }
                        if ($this->_scoreSettings['ft'] > 0) {
                            $sql .= 'tt_ft=?,';
                        }
                        if ($this->_scoreSettings['st'] > 0) {
                            $sql .= 'tt_st=?,';
                        }
                        if ($this->_scoreSettings['pro'] > 0) {
                            $sql .= 'tt_pro=?,';
                        }
                        if ($this->_scoreSettings['exam'] > 0) {
                            $sql .= 'tt_ex=?,';
                        }
                        $sql .= 'tt_tot=?';
                        break;
                }
                //$sql = substr($sql,0, strlen($sql)-1); //this will remove the comma at the end
                $sql .= ' where id=?';
                $this->_updScrQrySql = $sql;
            }

            $param = $scoreArr;
            $param[] = Utility::sum($scoreArr); //append the sum of $scoreArr to $param
            $param[] = $id; //append score ID to $param
            $this->_db->query($this->_updScrQrySql, $param);
            $this->_updScrQry = true;
        }
    }


    //this method is a second approach to update score table 
    //to use this function you must provide the first and third parameter when instantiating an object of this class
    function update2($stdId, $scoreArr, $term)
    {
        if ($this->_updScrQry) {
            $param = $scoreArr;
            $param[] = Utility::sum($scoreArr); //append the sum of $scoreArr to $param
            $param[] = $this->_id; //append subject ID to $param
            $param[] = $stdId; //append student ID to $param
            $this->_db->requery($param);
        } else {
            switch ($term) {
                case 'ft':
                    $sql = 'update ' . $this->_table . ' set ';
                    if ($this->_scoreSettings['fa'] > 0) {
                        $sql .= 'ft_fa=?,';
                    }
                    if ($this->_scoreSettings['sa'] > 0) {
                        $sql .= 'ft_sa=?,';
                    }
                    if ($this->_scoreSettings['ft'] > 0) {
                        $sql .= 'ft_ft=?,';
                    }
                    if ($this->_scoreSettings['st'] > 0) {
                        $sql .= 'ft_st=?,';
                    }
                    if ($this->_scoreSettings['pro'] > 0) {
                        $sql .= 'ft_pro=?,';
                    }
                    if ($this->_scoreSettings['exam'] > 0) {
                        $sql .= 'ft_ex=?,';
                    }
                    $sql .= 'ft_tot=?';
                    break;
                case 'st':
                    $sql = 'update ' . $this->_table . ' set ';
                    if ($this->_scoreSettings['fa'] > 0) {
                        $sql .= 'st_fa=?,';
                    }
                    if ($this->_scoreSettings['sa'] > 0) {
                        $sql .= 'st_sa=?,';
                    }
                    if ($this->_scoreSettings['ft'] > 0) {
                        $sql .= 'st_ft=?,';
                    }
                    if ($this->_scoreSettings['st'] > 0) {
                        $sql .= 'st_st=?,';
                    }
                    if ($this->_scoreSettings['pro'] > 0) {
                        $sql .= 'st_pro=?,';
                    }
                    if ($this->_scoreSettings['exam'] > 0) {
                        $sql .= 'st_ex=?,';
                    }
                    $sql .= 'st_tot=?';
                    break;
                case 'tt':
                    $sql = 'update ' . $this->_table . ' set ';
                    if ($this->_scoreSettings['fa'] > 0) {
                        $sql .= 'tt_fa=?,';
                    }
                    if ($this->_scoreSettings['sa'] > 0) {
                        $sql .= 'tt_sa=?,';
                    }
                    if ($this->_scoreSettings['ft'] > 0) {
                        $sql .= 'tt_ft=?,';
                    }
                    if ($this->_scoreSettings['st'] > 0) {
                        $sql .= 'tt_st=?,';
                    }
                    if ($this->_scoreSettings['pro'] > 0) {
                        $sql .= 'tt_pro=?,';
                    }
                    if ($this->_scoreSettings['exam'] > 0) {
                        $sql .= 'tt_ex=?,';
                    }
                    $sql .= 'tt_tot=?';
                    break;
            }
            //$sql = substr($sql,0, strlen($sql)-1); //this will remove the comma at the end
            $sql .= ' where subject_id=? and std_id=?';
            $param = $scoreArr;
            $param[] = Utility::sum($scoreArr); //append the sum of $scoreArr to $param
            $param[] = $this->_id; //append subject ID to $param
            $param[] = $stdId; //append student ID to $param
            $this->_db->query($sql, $param);
            $this->_updScrQry = true;
        }
    }



    //function helps to ignore prepared query
    function reset()
    {
        $this->_updScrQry = false;
    }

    //function requires you instantiate an object of this class passing the first parameter($subId)
    function getStudentCount(): int
    {
        $this->_db->query('select count(id) as counter from ' . $this->_table . ' where subject_id=?', [$this->_id]);
        return $this->_db->one_result()->counter;
    }


    //this method checks if a given std_id is registered for a particular subject
    function isRegistered($std_id): bool
    {
        if ($this->_isRegisteredPreparedStatement) {
            $this->_db->requery([$this->_id, $std_id]);
            return ($this->_db->one_result()->counter > 0) ? true : false;
        }
        $this->_db->query('select count(id) as counter from ' . $this->_table . ' where subject_id=? and std_id=?', [$this->_id, $std_id]);
        $this->_isRegisteredPreparedStatement = true;
        return ($this->_db->one_result()->counter > 0) ? true : false;
    }

    //this method resets the prepared statement used for isRegistered method();
    function resetIsRegistered()
    {
        $this->_isRegisteredPreparedStatement = false;
    }

    //this function deregister subjects for a particular student
    function deregisterSubjects($stdId, $subIds, $sch_abbr)
    {
        //delete rows from score table
        $this->deleteScore($stdId, $subIds, $sch_abbr);
    }


    //this function deletes a record from the score table, instantUtil method has to be called to use this method, this is to help instantiate util Class
    private function deleteScore($stdId, $subIds, $sch_abbr)
    {
        $util = new Utils();
        $currScoreTable = $util->getFormatedSession($sch_abbr) . '_score';
        if (count($subIds) > 1) { //prepare and requery if entries will be more than one
            $start = false;
            foreach ($subIds as $subId) {
                if ($start) {
                    $this->_db->requery([$stdId, $subId]);
                } else {
                    $this->_db->query('delete from ' . $currScoreTable . ' where std_id=? and subject_id = ?', [$stdId, $subId]);
                    $start = true;
                }
            }
        } else { //entry is expected to be one
            $this->_db->query('delete from ' . $currScoreTable . ' where std_id=? and subject_id = ?', [$stdId, $subIds[0]]);
        }
    }



    function updateCompSubReg($stdId, $complete = true)
    {
        $this->_db->query('update student set sub_reg_comp=? where std_id = ?', [$complete, $stdId]);
    }

    public static function add_scheme($sub_id, $title, $scheme, $term, $order)
    {
        $db = DB::get_instance();
        $db->insert('scheme_of_work', ['subject_id' => $sub_id, 'title' => $title, 'scheme' => $scheme, 'term' => $term, 'scheme_order' => $order]);
    }

    public static function edit_scheme($scheme_id, $title, $scheme, $order)
    {
        $db = DB::get_instance();
        $db->update('scheme_of_work', ['title' => $title, 'scheme' => $scheme, 'scheme_order' => $order], "id = $scheme_id");
    }

    public static function delete_scheme($scheme_id)
    {
        $db = DB::get_instance();
        $db->delete('scheme_of_work', "id=$scheme_id");
    }

    public static function get_scheme($scheme_id)
    {
        $db = DB::get_instance();
        return $db->get('scheme_of_work', '*', "id = $scheme_id", 'scheme_order');
    }
    public static function get_schemes($sub_id, $term)
    {
        $db = DB::get_instance();
        return $db->select('scheme_of_work', '*', "subject_id = $sub_id and term = '$term'", 'scheme_order');
    }
}
