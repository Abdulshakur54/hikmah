<?php

    class Alert {
        
        private $_db,$_db2,$_db3;
        private $_queryDeletePrepared = false; // indicate if the delete query has been prepared by $_db2
        private $_queryInsertPrepared = false; // indicate if the insert query has been prepared by $_db3
        private $_querySelectedPrepared = false; // indicate if select query has bee prepared by $_db1
        private $_maxAllowed = 15; //this is the maximum no of alerts allowed for each reciever
        
        public function __construct($openThreeDb = false) {
            $this->_db = DB::get_instance();
            if($openThreeDb){
                $this->_db2 = DB::get_instance2();
                $this->_db3 = DB::get_instance3();
            }
        }
           
        
        //this method insert into the alert table, it makes use of 3 database connections and prepared statements if necessary so as to facilitate quick response 
        public function send($receiver_id, $title, $message, $requery = false){
            //check if max alert has been reached
            if($this->maxReached($receiver_id,$requery)){
                if($requery){
                    if($this->_queryDeletePrepared){
                        $this->_db2->requery([$receiver_id]);
                    }else{
                         $this->_db2->query('delete from alert where receiver_id =? order by id asc limit 1',[$receiver_id]); //delete one alert row from table
                         $this->_queryDeletePrepared = true;
                    }
                   
                }else{
                     $this->_db->query('delete from alert where receiver_id =? order by id asc limit 1',[$receiver_id]); //delete one alert row from table
                }
               
            }
            if($requery){
                if($this->_queryInsertPrepared){
                    $this->_db3->requery([$receiver_id, $title, $message]);
                }else{
                    $this->_db3->query('insert into alert(receiver_id,title,message) values(?,?,?)',[$receiver_id, $title, $message]);
                    $this->_queryInsertPrepared = true;
                }
                
            }else{
                $this->_db->query('insert into alert(receiver_id,title,message) values(?,?,?)',[$receiver_id, $title, $message]);
            }
            
        }
        
        //this method sends notification to all the users having thesame rank with no exceptions
        // you must instantiate this class with $openThreeDb = true to use this function
        public function sendToRank(int $receiver_rank, string $title, string $message,$condition=null,$format = true){ //when false value is passed in for format, it means it is an already formated condition, so it should just be used in the query without formatting
            switch ($receiver_rank){
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                case 17:
                    $table = Config::get('users/table_name0');
                    $column = Config::get('users/username_column0');
                    break;
                case 7:
                case 15:
                case 8:
                case 16:
                    $table = Config::get('users/table_name1');
                     $column = Config::get('users/username_column1');
                    break;
                case 9:
                case 10:
                    $table = Config::get('users/table_name2');
                    $column = Config::get('users/username_column2');
                    break;
                case 11:
                case 12:
                    $table = Config::get('users/table_name3'); 
                    $column = Config::get('users/username_column3');
            }
            //query the database to get all the ids needed
            if(!empty($condition)){
                if($format){
                    $con = explode(',', $condition);
                    $this->_db->query('select '.$column.' from '.$table.' where rank = ? and '.$con[0].$con[1].'?',[$receiver_rank,$con[2]]);
                }else{
                     $this->_db->query('select '.$column.' from '.$table.' where '.$condition);
                }
                
            }else{
                $this->_db->query('select '.$column.' from '.$table.' where rank = ?',[$receiver_rank]);
            }
            
            $ids = $this->_db->get_result();
            //send notification for each id
            foreach ($ids as $val){
                $this->send($val->$column, $title, $message, true);
            }
            
        }
        
       
        
        private function maxReached($receiver_id, $requery = false){
            return ($this->getCount($receiver_id,$requery) >= $this->_maxAllowed) ? true: false;
        }
       
        //this method return all the alerts for a particular reciever
        public function getMyAlerts($receiver_id){
            $this->_db->query('select * from alert where receiver_id = ? order by id desc',[$receiver_id]);
            return $this->_db->get_result();
        }
        
        
        //this method checks if a receiver has alert(s)
        public function hasAlerts($receiver_id) :bool{
            return ($this->getUnseenCount($receiver_id) > 0) ? true: false;
        }
        
        //this function deletes the alert row with the provided id
        public function delete($id){
            $this->_db->query('delete from alert where id=?',[$id]);
        }
        
        //this function counts the no of alert for a particular receiver
        public function getCount($receiver_id,$requery) :int{
            if($requery){
                 if($this->_querySelectedPrepared){
                    $this->_db->requery([$receiver_id]);
                }else{
                    $this->_db->query('select count(id) as counter from alert where receiver_id = ?',[$receiver_id]);
                    $this->_querySelectedPrepared = true;
                }
            }else{
                $this->_db->query('select count(id) as counter from alert where receiver_id = ?',[$receiver_id]);
            }
           
               
            return $this->_db->one_result()->counter;
        }
        
        
        //this function is used to help reset all the prepared queries
        public function reset(){
            $this->_queryDeletePrepared = false;
            $this->_queryInsertPrepared = false;
            $this->_querySelectedPrepared = false;
        }
        
        public function getUnseenCount($receiver_id) :int{
            $this->_db->query('select count(id) as counter from alert where receiver_id = ? and seen = ?',[$receiver_id, false]);
            return $this->_db->one_result()->counter;
        }
        
        public function seen($receiver_id){
            $this->_db->query('update alert set seen = ? where receiver_id=?',[true,$receiver_id]);
        }
        
        public function __destruct() {
            $this->_db2 = null;
            $this->_db3 = null;
        }
        
    }
