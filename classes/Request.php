<?php

/*
Request Categories
 case 1: Salary



  */

//this class send request to ranks
    class Request {
        
        
        private $_db,$_db2;
        private $_mgtIdFirstLetter = 'M';
        private $_directorRank = 1;
        private $_hRMRank = 6;
        private $_sendQryPrepared = false;
        
        
        public function __construct() {
            $this->_db = DB::get_instance();
        }
        
        
        public function requstConfirm($id,$requester_id,$category){
            switch ($category){
                case 1: //aproval of salary
                    $this->_db->query('update account set approved = ? where receiver = ?',[true, $requester_id]);
                    break;
            }
            //delete the request from the request table
            $this->deleteRequset($id);
            //send notification to the requester
            $this->requestResponseNotification($requester_id,$category, true);
        }
        
        //this method is used to send a notification to a requester after his request have been accepted or rejected
        private function requestResponseNotification($requester_id,$category, $accepted){
            switch ($category){
                case 1:
                    $acct = new Account();
                    $not = new Alert(true);
                    $details = $acct->getAccountDetails($requester_id);
                    $salary = $details->salary;
                    $name = Utility::formatName($details->fname, $details->oname, $details->lname);
                    $firstLetter = strtoupper(substr($requester_id,0,1));
                    if($accepted){
                        $not->send($requester_id, 'Salary Approval', 'Your request of  &#8358;'.$salary.' as salary has been approved');
                        //check if the requester is a member of management then send a notification to the director, else send a notification to the hrm
                        if($firstLetter === $this->_mgtIdFirstLetter){ //this is a management member
                             $not->sendToRank($this->_directorRank, 'Salary Approval', 'A salary of &#8358;'.$salary.' has been approved for '.$name);
                        }else{ //the requester is assumed to be a staff
                             $not->sendToRank($this->_hRMRank, 'Salary Approval', 'A salary of &#8358;'.$salary.' has been approved for '.$name);
                        }
                    }else{
                         $not->send($requester_id, 'Salary Declination', 'Your request of  &#8358;'.$salary.' as salary was rejected');
                         //check if the requester is a member of management then send a notification to the director, else send a notification to the hrm
                        if($firstLetter === $this->_mgtIdFirstLetter){ //this is a management member
                             $not->sendToRank($this->_directorRank, 'Salary Declination', 'The salary of  &#8358;'.$salary.' is rejected for '.$name);
                        }else{ //the requester is assumed to be a staff
                             $not->sendToRank($this->_hRMRank, 'Salary Declination', 'The salary of  &#8358;'.$salary.' is rejected for '.$name);
                        }
                         
                    }
                    break;
            }
        }
        
        public function requstDecline($id,$requester_id,$category){
            $this->deleteRequset($id);
            //send notification to the requester
            $this->requestResponseNotification($requester_id,$category, false);
        }
        
        
        //this method sends request to the request table, it sends multiple request using prepared statements when requery is true
        public function send($requester_id, $confirmer_rank, $request,$category, $requery = false){
            
            //title the request relative to the category
            switch($category){
                case 1: 
                    $title = 'Salary Confirmation';
            }
                    
            if($requery){
                if($this->_sendQryPrepared){
                    $this->_db->requery([$requester_id, $category]); //check if request exists
                    if(!($this->_db->row_count() > 0)){ //ensures that request are only sent when it does not exist
                        $this->_db2->requery([$requester_id,$confirmer_rank,$title,$request,$category]);
                    }
                }else{
                    //instansiate second connection and prepare query
                    $this->_db2 = DB::get_instance2();
                    $this->_db->query('select id from request where requester_id=? and category=?',[$requester_id, $category]); //check if request exists
                    if(!($this->_db->row_count() > 0)){ //ensures that request are only sent when it does not exist
                        $this->_db2->query('insert into request(requester_id,confirmer_rank,title,request,category) values(?,?,?,?,?)',[$requester_id,$confirmer_rank,$title,$request,$category]);
                        $this->_sendQryPrepared = true;
                    }
                }
            }else{
                if($this->requestExists($requester_id,$category)){
                //return 1;
                }else{
                     return $this->_db->query('insert into request(requester_id,confirmer_rank,title,request,category) values(?,?,?,?,?)',[$requester_id,$confirmer_rank,$title,$request,$category]);
                } 
            }
                
        }
        
        public function getMyRequests($confirmerRank){
            $this->_db->query('select * from request where confirmer_rank =? order by id desc',[$confirmerRank]);
            return $this->_db->get_result();
        }
        
        public function getCount($confirmerRank) :int{
            $arrVal = (array)$this->getMyRequests($confirmerRank);
            return count($arrVal);
        }
        
        //this delete a request from the request table
        public function deleteRequset($id) :bool{
            return $this->_db->query('delete from request where id =?',[$id]);
        }
        
        //this is another version of deleteRequest with different parameters
        public function delRequest($requester_id,$category){
            
            if(!is_array($requester_id)){
                $this->_db->query('delete from request where requester_id = ? and category =?',[$requester_id, $category]);
            }else{
                $x = 1;
                foreach ($requester_id as $req => $cat){
                    if($x === 1){
                        $this->_db->query('delete from request where requester_id = ? and category =?',[$req, $category]);
                        $x++;
                    }else{
                        //using requery
                        $this->_db->requery([$req, $category]);
                    }
                }
            }
        }
        
        
        public function requestExists($requester_id,$category) :bool{
            $this->_db->query('select id from request where requester_id=? and category=?',[$requester_id, $category]);
            if($this->_db->row_count() > 0){
                return true;
            }
            return false;
        }
        
         //this method checks if a receiver has request(s)
        public function hasRequests($confirmerRank) {
            $request = $this->getMyRequests($confirmerRank);
            return (!empty($request)) ? $request:false;
        }
        
        function reset(){
            $this->_sendQryPrepared = false;
        }
        
        public function __destruct() {
            $this->_db2 = null;
        }
    }
