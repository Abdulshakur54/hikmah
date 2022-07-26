<?php
	class Admission extends User{

		public function __construct(){
			parent::__construct($cat = 4);
		}
		
                
                 //this method gets an id for admission student after increement by 1
                public static function genId(){
                   $agg = new Aggregate();
                   $id = $agg->lookUp('adm_count', 'sing_val', 'id,=,1') + 1; //gets the current no and increement by 1
                   $agg->edit($id, 'adm_count', 'sing_val', 'id,=,1'); //updates the admission count
                   $preZeros = '';
                   $preZerosCount = 3 - strlen((string)$id);
                   for($i=1;$i<=$preZerosCount;$i++){
                       $preZeros.='0';
                   }
                   return $preZeros.$id;
                }
                
                public function apply($fatherName, $motherName, $phone, $email,$pictureName,$address){
                    return $this->_db->query('update admission set fathername=?, mothername=?, phone=?, email=?, picture=?, applied = ?, address=? where adm_id=?',[$fatherName,$motherName,$phone,$email,$pictureName,true,$address,$this->data()->adm_id]);
                }
                
                public function getData($adm_id){
                    $this->find($adm_id);
                    return $this->data();
                }
                
                
                public function hasApplied($adm_id) :bool{
                    $this->_db->query('select applied from admission where id=?',[$adm_id]);
                    return ($this->_db->one_result()->applied)?true:false;
                }
                
                public function getLogo($sch){
                    $this->_db->query('select logo from school where sch_abbr = ?',[$sch]);
                    return $this->_db->one_result()->logo;
                }
               
	}