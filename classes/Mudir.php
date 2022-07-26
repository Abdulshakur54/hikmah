<?php
    class Mudir extends Management{
        
        
        public function getNotClassTeachers($sch_abbr){
            if($this->_db->query('select staff_id,fname,lname,oname from staff where class is not null and active = ? and rank=? and sch_abbr=?',[true,15,$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
        public function classNoTeacher($sch_abbr){
            if($this->_db->query('select level,class,id from class where teacher is null and sch_abbr=?',[$sch_abbr])){
                return $this->_db->get_result();
            }
            
        }
        
    }