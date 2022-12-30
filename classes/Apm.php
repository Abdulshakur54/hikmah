<?php
class Apm extends Management
{
    private $_table;
    public function __construct()
    {
        parent::__construct();
        $this->_table = Config::get('users/table_name3');
    }

    function selectAdmissionApplicants($sch_abbr = null, $level = null)
    {
        $sql = 'select id,adm_id,fname,lname,oname,level,score,sch_abbr,status from ' . $this->_table . ' where applied = ?';
        $val = [true];
        if (isset($sch_abbr) && isset($level)) {
            if ($sch_abbr !== 'ALL') {
                $sql .= ' and sch_abbr = ?';
                $val[] = $sch_abbr;
            }
            if ($level !== 0) {
                $sql .= ' and level = ?';
                $val[] = $level;
            }
        }
        $sql .= ' order by sch_abbr, level,fname';
        if ($this->_db->query($sql, $val)) {
            return $this->_db->get_result();
        }
    }

    function selectAdmissionAttatchments($sch_abbr = null, $level = null)
    {

        $sql = 'select * from attachment';
        $val = [];
        if (!empty($sch_abbr) && $sch_abbr !== 'ALL') {
            $sql .= ' where sch_abbr = ?';
            $val[] = $sch_abbr;
        }
        if (!empty($level) && $level !== 'ALL') {
            if (substr($sql, -1) == '?') {
                $sql .= 'and level = ?';
            } else {
                $sql .= ' where level = ?';
            }
            $val[] = $level;
        }
        $sql .= ' order by sch_abbr, level';
        if ($this->_db->query($sql, $val)) {
            return $this->_db->get_result();
        }
    }

    function insertAttachment($attachment, $name, $sch_abbr, $level)
    {
        $this->_db->query('insert into attachment(attachment,name,sch_abbr,level) values(?,?,?,?)', [$attachment, $name, $sch_abbr, $level]);
    }

    function delAttachment($idToDel)
    {
        $this->_db->query('delete from attachment where id = ?', [$idToDel]);
    }

    function getApplicantDetails($id)
    {
        $this->_db->query('select * from admission where adm_id=?', [$id]);
        return $this->_db->one_result();
    }

    //        function newSession($sch_abbr){
    //            //create new table if it dosent'exist
    //            if(!$this->sessionExists()){
    //                $sch = [$sch_abbr];
    //                $sql = 
    //            }else{
    //                
    //            }
    //        }

    private function sessionExists(): bool
    {
        $this->_db->query('select count(id) as counter from session');
        return $this->_db->one_result()->counter;
    }

    public function getSchedules($sch_abbr)
    {
        $utils = new Utils();
        $current_session = $utils->getSession($sch_abbr);
        $this->_db->query('select school.*, fee.reg_fee,fee.form_fee,fee.ft_fee,fee.st_fee,fee.tt_fee from school inner join fee on school.sch_abbr = fee.sch_abbr  where school.sch_abbr = ? and fee.session = ?', [$sch_abbr,$current_session]);
        return $this->_db->one_result();
    }


    //this method updates the settings(schedules) for the selected school
    public function updateSchedule($sch_abbr, $term, $formFee, $regFee, $ftsf, $stsf, $ttsf, $logoName = null): bool
    {
        $utils = new Utils();
        $current_session = $utils->getSession($sch_abbr);
        $this->_db->update('fee', ['form_fee' => $formFee, 'reg_fee' => $regFee, 'ft_fee' => $ftsf, 'st_fee' => $stsf, 'tt_fee' => $ttsf], "sch_abbr = '$sch_abbr' and session='$current_session'");
        if ($logoName !== null) {
            $response =  $this->_db->query('update school set current_term=?, logo=? where sch_abbr=?', [$term, $logoName, $sch_abbr]);
        } else {

            $response =  $this->_db->query('update school set current_term=? where sch_abbr=?', [$term, $sch_abbr]);
        }
        return $response;
    }
}
