<?php
    class Apm extends Management{
        private $_table;
        public function __construct() {
            parent::__construct();
            $this->_table = Config::get('users/table_name3');
        }
        
        function selectAdmissionApplicants($sch_abbr=null,$level=null){
            $sql = 'select id,adm_id,fname,lname,oname,level,score,sch_abbr from '.$this->_table.' where applied = ? and status =?';
            $val = [true,0];
            if(isset($sch_abbr) && isset($level)){
                if($sch_abbr !== 'ALL'){
                    $sql.=' and sch_abbr = ?';
                    $val[]=$sch_abbr;
                }
                if($level !== 0){
                    $sql.=' and level = ?';
                    $val[]=$level;
                }
            }
            $sql.=' order by sch_abbr, level,fname';
            if($this->_db->query($sql,$val)){
                return $this->_db->get_result();
            }
            
        }
        
         function selectAdmissionAttatchments($sch_abbr=null,$level=null){
            $sql = 'select * from attachment';
            $val = [];
            if(!empty($sch_abbr) && !empty($level)){
                $sql.=' where sch_abbr = ? and level = ?';
                $val[] = $sch_abbr;
                $val[]=$level;
            }
            $sql.=' order by sch_abbr, level';
            if($this->_db->query($sql,$val)){
                return $this->_db->get_result();
            }
            
        }
        
        function insertAttachment($attachment,$name,$sch_abbr,$level){
            $this->_db->query('insert into attachment(attachment,name,sch_abbr,level) values(?,?,?,?)',[$attachment,$name,$sch_abbr,$level]);
        }
        
        function delAttachment($idToDel){
            $this->_db->query('delete from attachment where id = ?',[$idToDel]);
        }
        
        function getApplicantDetails($id){
            $this->_db->query('select * from admission where adm_id=?',[$id]);
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
        
        private function sessionExists() :bool{
            $this->_db->query('select count(id) as counter from session');
            return $this->_db->one_result()->counter;
        }
        
        public function getSchedules($sch_abbr){
            $this->_db->query('select * from school where sch_abbr = ?',[$sch_abbr]);
            return $this->_db->one_result();
        }
        
        
        //this method updates the settings(schedules) for the selected school
        public function updateSchedule($sch_abbr,$term,$formFee,$regFee,$ftsf,$stsf,$ttsf,$logoName= null) :bool{
            if($logoName !== null){
                 return $this->_db->query('update school set current_term=?, form_fee=?, reg_fee=?, ft_fee=?, st_fee=?, tt_fee=?, logo=? where sch_abbr=?',[$term,$formFee,$regFee,$ftsf,$stsf,$ttsf,$logoName,$sch_abbr]);
            }else{
                 return $this->_db->query('update school set current_term=?, form_fee=?, reg_fee=?, ft_fee=?, st_fee=?, tt_fee=? where sch_abbr=?',[$term,$formFee,$regFee,$ftsf,$stsf,$ttsf,$sch_abbr]);
            }
          
        }
        
        
    }