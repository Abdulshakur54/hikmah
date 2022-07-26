<?php
	class Management extends User{

		public function __construct(){
			parent::__construct($cat = 1);
		}
                
                //this code can be edited incase deputies or secretaries are needed
		public static function getPositions($type = 1){
                    
                    switch ($type){
                        case 1:
                            return [
                            'Director'=>1,'A.P.M'=>2,'Accountant'=>3,'H.R.M'=>6,'I.C'=>4,'H.O.S'=>5,'Mudir'=>17
                            ];
                        case 2:
                            return ['Director','A.P.M','Accountant','H.R.M','I.C','H.O.S','Mudir'];
                        case 3:
                            return [
                            'Director'=>1,'Academic Planning Manager'=>2,'Accountant'=>3,'Human Resource Manager'=>6,'Islamiyah Co-Ordinator'=>4,'Head of School'=>5,'Mudir'=>17
                            ];
                            
                    }
			
		}
                
                //this method gets an id for management member after increement by 1
                public static function genId(){
                   $agg = new Aggregate();
                   $id = $agg->lookUp('mgt_count', 'sing_val', 'id,=,1') + 1; //gets the current no and increement by 1
                   $agg->edit($id, 'mgt_count', 'sing_val', 'id,=,1'); //updates the admission count
                   $preZeros = '';
                   $preZerosCount = 3 - strlen((string)$id);
                   for($i=1;$i<=$preZerosCount;$i++){
                       $preZeros.='0';
                   }
                   return $preZeros.$id;
                }
                
                public function getManagementIds($rank){
                    $mgtIds = [];
                    $column = Config::get('users/username_column0');
                    $this->_db->query('select '.$column.' from '.Config::get('users/table_name0').' where rank=?',[$rank]);
                    if($this->_db->row_count() > 0){
                        $res = $this->_db->get_result();
                        foreach ($res as $val){
                            $mgtIds[] = $val->$column;
                        }
                    }
                    return $mgtIds;
                }
                
                
                //this method is to be used for deputies and secretaries
                public static function getBossRank($rank){
                    switch($rank){
                        //for deputy director and secretary
                        case 21:
                        case 31:
                            return 1;
                        //for deputy apm and secretary
                        case 22:
                        case 32:
                            return 2;
                        //for deputy accountant and secretary
                        case 23:
                        case 33:
                            return 3;
                        //for deputy I.C and secretary
                        case 24:
                        case 34:
                            return 4;
                        //for deputy H.O.S and secretary
                        case 25:
                        case 35:
                            return 5;
                        //for deputy H.R.M and secretary
                        case 26:
                        case 36:
                            return 6;
                        //for deputy Mudir and secretary
                        case 27:
                        case 37:
                            return 17;
                    }
                }
                
                
                //function return 0 shows he is a boss, returns 1 shows he is a deputy, returns 2 shows he is a secretary
                public static function getAsstVal($rank){
                    switch($rank){
                        //for management deputy
                        case 21:
                        case 22:
                        case 23:
                        case 24:
                        case 25:
                        case 26:
                        case 27:
                            return 1;
                        //for management secretary
                        case 31:
                        case 32:
                        case 33:
                        case 34:
                        case 35:
                        case 36:
                        case 37:
                            return 2;
                        //for management boss
                        default:
                            return 0;
                    }
                }
		
	}