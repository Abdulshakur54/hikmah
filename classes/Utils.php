<?php

class Utils {
    private $_db;
    public function __construct() {
        $this->_db = DB::get_instance();
    }
    
    public function getSession($sch_abbr){
        $this->_db->query('select current_session from school where sch_abbr=?',[$sch_abbr]);
         return $this->_db->one_result()->current_session;
    }
    
    public function getFormatedSession($sch_abbr){
        return Utility::getFormatedSession($this->getSession($sch_abbr));
    }
    
    public function getPreviousSession($sch_abbr){
        $this->_db->query('select previous_session from school where sch_abbr=?',[$sch_abbr]);
        if($this->_db->row_count() > 0){
            return $this->_db->one_result()->previous_session;
        }
        return false;
    }
    
    public function getSessionStartYear($sch_abbr){
        return explode('/', $this->getSession($sch_abbr))[0];
    }
    
    public function getSessionEndYear($sch_abbr){
        return explode('/', $this->getSession($sch_abbr))[1];
    }
    
    public function getSessionYearAbbreviation($sch_abbr){
        return substr($this->getSessionStartYear($sch_abbr), -2);
    }
    
    //this function returns the current term in a school
    public function getCurrentTerm($sch_abbr){
        $this->_db->query('select current_term from school where sch_abbr = ?',[$sch_abbr]);
        return $this->_db->one_result()->current_term;
    }
    
    public function getSubDetails($subId){
        $this->_db->query('select subject.subject, subject.level, class.class from subject inner join subject2 on subject.id = subject2.subject_id inner join class on subject2.class_id = class.id where subject2.id=?',[$subId]);
        return $this->_db->one_result();
    }
    
}
