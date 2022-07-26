<?php
    class Aggregate{
        private $_db;

        public function __construct(){
            $this->_db = DB::get_instance();
        }

        public function exists($value, $column_name, $table_name){
            $this->_db->query('select '.$column_name.' from '.$table_name.' where '.$column_name.'=?',[$value]);
            return ($this->_db->row_count() > 0) ? true:false;
        }
        
        public function lookUp($column,$table,$condition){
            $cons = explode(',', $condition);
            $this->_db->query('select '.$column.' from '.$table.' where '.$cons[0].$cons[1].'?',[$cons[2]]);
            if($this->_db->row_count() > 0){
                return $this->_db->one_result()->$column;
            }
            return;   
        }
        
        public function edit($value,$column,$table,$condition) :bool{
            $cons = explode(',', $condition);
            return $this->_db->query('update '.$table.' set '.$column.' = ? where '.$cons[0].$cons[1].'?',[$value, $cons[2]]);
        }
        
        
        //deletes a single row from a given table
        public function rowDelete($table,$condition) :bool{
            $cons = explode(',', $condition);
            return $this->_db->query('delete from '.$table.' where '.$cons[0].$cons[1].'?',[$cons[2]]);
        }
        
        public function sum($column,$table,$condition=null){
            if($condition !== null){
                $con = explode(',', $condition);
                $this->_db->query('select sum('.$column.') as summation from '.$table.' where '.$con[0].$con[1].'?',[$con[2]]);
            }else{
                $this->_db->query('select sum('.$column.') as summation from '.$table);
            }
            return $this->_db->one_result()->summation;
        }
        
         public function max($column,$table,$condition=null){
            if($condition !== null){
                $con = explode(',', $condition);
                $this->_db->query('select max('.$column.') as maximum from '.$table.' where '.$con[0].$con[1].'?',[$con[2]]);
            }else{
                $this->_db->query('select max('.$column.') as maximum from '.$table);
            }
            return $this->_db->one_result()->maximum;
        }
        
        public function min($column,$table,$condition=null){
            if($condition !== null){
                $con = explode(',', $condition);
                $this->_db->query('select min('.$column.') as minimum from '.$table.' where '.$con[0].$con[1].'?',[$con[2]]);
            }else{
                $this->_db->query('select min('.$column.') as minimum from '.$table);
            }
            return $this->_db->one_result()->minimum;
        }
        
    }