<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Hos2
 *
 * @author Abdulshakur
 */
class Hos2 extends Management{
    
    
    function getStudents($classId){
        $this->_db->query('select id,std_id,fname,oname,lname from student where class_id = ? and active = 1',[$classId]);
        return $this->_db->get_result();
    }
    
}
