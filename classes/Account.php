<?php

class Account{
    
    private $_db;
    private $_mgtFirstLetter = 'M';
    
    public function __construct() {
        $this->_db = DB::get_instance();
    }
    
    public function getAccountDetails($receiver){
        $firstLetter = strtoupper(substr($receiver,0,1));
        if($firstLetter === $this->_mgtFirstLetter){
            //this shows that $reciever is a management member
            $table = Config::get('users/table_name0');
            $column = Config::get('users/username_column0');
        }else{
            //this show $receiver is a staff
            $table = Config::get('users/table_name1');
            $column = Config::get('users/username_column1');
        }
        
        $this->_db->query('select account.*,'.$table.'.fname,'.$table.'.lname,'.$table.'.oname from account inner join '.$table.' on account.receiver = '.$table.'.'.$column.' where account.receiver=?',[$receiver]);
        if($this->_db->row_count() > 0){
            return $this->_db->one_result();
        }
    }
    //this function uses prepared statement when the $receiver is an array of receivers
    public function updateSalary($receiver, $salary=null){
        if(!is_array($receiver)){
            $this->_db->query('update account set salary = ?, approved = ? where receiver = ?',[$salary,false,$receiver]);
        }else{
            $x = 1;
            foreach ($receiver as $rec => $sal){
                if($x === 1){
                    $this->_db->query('update account set salary = ?, approved = ? where receiver = ?',[$sal,false,$rec]);
                    $x++;
                }else{
                    //using requery
                    $this->_db->requery([$sal,false,$rec]);
                }
            }
        }
        
    }
    
    public function getSalariesDetails($category){
       switch($category){
            case 1:
               $table_name = Config::get('users/table_name0');
               $col_name = Config::get('users/username_column0');
               break;
            case 2:
                $table_name = Config::get('users/table_name1');
                $col_name = Config::get('users/username_column1');
       }
       $this->_db->query('select '.$table_name.'.fname, '.$table_name.'.lname, '.$table_name.'.oname, account.* from '.$table_name.' inner join account on '.$table_name.'.'.$col_name.' = account.receiver');
       return $this->_db->get_result();
    }
}