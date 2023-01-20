<?php
class SchoolFees{
    private $_db;
    function __construct()
    {
        $this->_db = DB::get_instance();
    }
    public function insert(string $std_id, string $term, string $session){
        
    }
}