<?php

/*
Request Categories
 case 1: Salary



  */

//objects of this class send requests to individuals
    class Request2 {
  
        private $_db,$_db2;
        private $_sendQryPrepared = false;
        
        
        public function __construct($openTwoDb = false) {
            $this->_db = DB::get_instance();
            if($openTwoDb){
                $this->_db2 = DB::get_instance2();
            }    
        }
        
        
        public function requstConfirm($id,$requester_id,$category,$other){
            switch ($category){
                case 1: // allow deregistration of some subject
                    $sub = new Subject();
                    //get the school and classid of the student
                    $this->_db->query('select student.sch_abbr, student.class_id, student.sub_reg_comp, class.nos from student inner join class on student.class_id = class.id where student.std_id =?',[$requester_id]);
                    $res = $this->_db->one_result();
                    $sub->deregisterSubjects($requester_id, array_keys($other),$res->sch_abbr);
                    $minNoSub = $res->nos;
                    $util = new Utils();
                    $scoreTable = $util->getFormatedSession($res->sch_abbr).'_score';
                    $regSubArr = $sub->getRegisteredSubjectsId($scoreTable,$requester_id);
                    $subRegComp = $res->sub_reg_comp;
                    if(($minNoSub <= count($regSubArr)) && !$subRegComp){
                        //update table indicating that the minimum no of subjects has been registered
                        $sub->updateCompSubReg($requester_id);
                    }else{
                        //update indicating otherwise, i.e turn sub_reg_com to false
                        $sub->updateCompSubReg($requester_id,false);
                    }       
                break;
                    
            }
            //delete the request from the request table
            $this->deleteRequset($id);
            //send notification to the requester
            $this->requestResponseNotification($requester_id,$category,true,$other);
        }
        
        //this method is used to send a notification to a requester after his request have been accepted or rejected
        private function requestResponseNotification($requester_id,$category, $accepted,$other=[]){
            $alert = new Alert();
            switch ($category){
                case 1:
                    if($accepted){
                        $message = 'Your request to deregister subjects: '.implode(',', array_values($other)).' has been approved and the mentioned subject have been deregistered';
                        $alert->send($requester_id, 'Subject Deregistration Approval', $message);
                    }else{
                        $message = 'Your request to deregister subjects: '.implode(',', array_values($other)).' has been declined. You may want to physically meet your Form Teacher and then request again';
                        $alert->send($requester_id, 'Subject Deregistration Disapproval', $message);
                    }
                    
                break;
            }
        }
        
        public function requstDecline($id,$requester_id,$category,$other){
            $this->deleteRequset($id);
            //send notification to the requester
            $this->requestResponseNotification($requester_id,$category, false,$other);
        }
        
        
        //this method sends request to the request table, it sends multiple request using prepared statements when requery is true
        public function send($requester_id, $confirmerId, $request,$category, $other=null, $requery = false){
            
            //title the request relative to the category
            switch($category){
                case 1: 
                    $title = 'Subject Deregisteration';;
                    break;
            }
                    
            if($requery){
                if($this->_sendQryPrepared){
                    $this->_db->requery([$requester_id, $category]); //check if request exists
                    if(!($this->_db->row_count() > 0)){ //ensures that request are only sent when it does not exist
                        $this->_db2->requery([$requester_id,$confirmerId,$title,$request,$category]);
                    }
                }else{
                    //instansiate second connection and prepare query 
                    $this->_db->query('select id from request2 where requester_id=? and category=?',[$requester_id, $category,$other]); //check if request exists
                    if(!($this->_db->row_count() > 0)){ //ensures that request are only sent when it does not exist
                        $this->_db2->query('insert into request2(requester_id,confirmer_id,title,request,category,other) values(?,?,?,?,?,?)',[$requester_id,$confirmerId,$title,$request,$category,$other]);
                        $this->_sendQryPrepared = true;
                    }
                }
            }else{
                if($this->requestExists($requester_id,$category)){
                    //return 1;
                }else{
                     return $this->_db->query('insert into request2(requester_id,confirmer_id,title,request,category,other) values(?,?,?,?,?,?)',[$requester_id,$confirmerId,$title,$request,$category,$other]);
                } 
            }
                
        }
        
        public function getMyRequests($confirmerId){
            $this->_db->query('select * from request2 where confirmer_id =? order by id desc',[$confirmerId]);
            return $this->_db->get_result();
        }
        
        public function getCount($confirmerId) :int{
            $arrVal = (array)$this->getMyRequests($confirmerId);
            return count($arrVal);
        }
        
        //this delete a request from the request table
        public function deleteRequset($id) :bool{
            return $this->_db->query('delete from request2 where id =?',[$id]);
        }
        
        //this is another version of deleteRequest with different parameters
        public function delRequest($requester_id,$category){
            
            if(!is_array($requester_id)){
                $this->_db->query('delete from request2 where requester_id = ? and category =?',[$requester_id, $category]);
            }else{
                $x = 1;
                foreach ($requester_id as $req => $cat){
                    if($x === 1){
                        $this->_db->query('delete from request2 where requester_id = ? and category =?',[$req, $category]);
                        $x++;
                    }else{
                        //using requery
                        $this->_db->requery([$req, $category]);
                    }
                }
            }
        }
        
        
        public function requestExists($requester_id,$category) :bool{
            $this->_db->query('select id from request2 where requester_id=? and category=?',[$requester_id, $category]);
            if($this->_db->row_count() > 0){
                return true;
            }
            return false;
        }
        
         //this method checks if a receiver has request(s)
        public function hasRequests($confirmerId) {
            $request = $this->getMyRequests($confirmerId);
            return (!empty($request)) ? $request:false;
        }
        
        function reset(){
            $this->_sendQryPrepared = false;
        }
        
        public function __destruct() {
            $this->_db2 = null;
        }
    }
