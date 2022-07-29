<?php
	
	class Validation{
		private $_conn;
		private $_error = array();
		public function __construct($open_db_conn = false){
			if(isset($open_db_conn)){
				$this->_conn = DB::get_instance();
			}
		}

		public function check($arr_of_val):bool{
			//looping through the fieldnames
			foreach($arr_of_val as $fieldname => $val){

				//lopping through the rules and the rule_values
                                $name = '';
                                $empty='';
				foreach($val as $rule => $rule_val){
					$value = trim(Input::get($fieldname));//gets the input from the user
					$empty = (empty($value) && $value != 0) ? true: false;

					switch($rule){
						case 'name': 
								$name = $rule_val; //set the name that will be used to refer to the form field
								break;

						case 'required':
							$required = $rule_val; //determines if a field is required
							if($empty && $required){
								$this->_error[] = $name.' is required';
							}
							break;

						case 'max':
							  if(!$empty && strlen($value) > $rule_val){
							  		$this->_error[] = $name.' should be maximum of '.$rule_val. ' characters';
							  }
							  break;
						case 'min':
							  if(!$empty && strlen($value) < $rule_val){
							  		$this->_error[] = $name. ' should be minimum of '.$rule_val. ' characters';
							  }
							  break;
						case 'pattern':
							$rule_val = '/'.$rule_val.'/';
							if(!preg_match($rule_val, $value) && !$empty){
								$this->_error[] = 'Invalid '.$name;
							}
							break;
						case 'same':
							if($value !== Input::get($rule_val)){
								$this->_error[] = $name .' must match Password Field';
							}
							break;
						case 'unique':
                                                        if(!$empty){
                                                            if(strpos($rule_val, '/')){
                                                                    $data = explode('/', $rule_val); #used to devide the string into column_name and table_name
                                                                    $table_column = $data[0];
                                                                    $table_name = $data[1];
                                                            }

                                                            $this->_conn = DB::get_instance();
                                                            $this->_conn->query("select ".$table_column." from ".$table_name." where ".$table_column." = ?", array($value));
                                                            if($this->_conn->row_count()){
                                                                    $this->_error[] = $name .' has already been taken';
                                                            }
                                                        }
								
							break;
						case 'in':
							if(!in_array($value, $rule_val)){
								$this->_error[]=$name. ' is unavailable';
							}
						break;
						case 'maximum':
							if(!$empty && (int)$value > $rule_val){	
								$this->_error[] = $name.' should be maximum of '.$rule_val;
							}
						break;
						case 'minimum':
							if(!$empty && (int)$value < $rule_val){	
								$this->_error[] = $name.' should be minimum of '.$rule_val;
							}
						break;
                                                
                                                case 'exists':
                                                    if(!$empty){
                                                        if(strpos($rule_val, '/')){
                                                                $data = explode('/', $rule_val); #used to devide the string into column_name and table_name
                                                                $table_column = $data[0];
                                                                $table_name = $data[1];
                                                        }

                                                        $this->_conn = DB::get_instance();
                                                        $this->_conn->query("select ".$table_column." from ".$table_name." where ".$table_column." = ?", array($value));
                                                        if($this->_conn->row_count() < 0){
                                                                $this->_error[] = $name .' does not exist';
                                                        }
                                                    }

                                                    break;
					}
				}
			}

			return $this->passed();
		}
                
                //$fileIndexes is an array of associative arrays
                public function checkFile(array $fileIndexes) {
                    foreach ($fileIndexes as $fileindex => $rules){
                        $name=''; //initialize to empty for each file
                        $fileObj = ''; //initialize to empty for each file
                        $empty = ''; //initialize to empty for each file
                        foreach ($rules as $rule => $rule_val) {
                            switch($rule) {
                                case 'name':
                                    $name = $rule_val;
                                    $fileObj = new File($fileindex);
                                    break;
                               case 'required':
                                    $empty = (!$fileObj->isSelected())?true:false;
                                    if($rule_val && $empty){
                                        $this->_error[] = 'File not selected';
                                    }
                                    break;
                               case 'maxSize':
                                    if(!$empty && $fileObj->size() > $rule_val){
                                        $this->_error[] = $name.' size should not exceed '.$rule_val.' KB';
                                    }
                                    break;
                               case 'extension':
                                    if(!$empty && !$fileObj->isValidExtension($rule_val)){
                                        if(is_array($rule_val)){
                                            $exts=[]; //stores formatted extensions 
                                            foreach($rule_val as $val){
                                                $exts[]="'.".$val."'"; //prefix each extension with a dot and put single quotes around it
                                            }
                                            $this->_error[] = "Only ".implode(", ", $exts)." are the allowed extensions for ".$name;
                                        }else{
                                            $this->_error[] = "only '".$rule_val."' is the allowed extension for ".$name;
                                        }
                                    }
                                    break;

                                default:
                                    break;
                            }
                        }
                    }
                    return $this->passed();
                }

		private function passed() :bool{
			if(count($this->_error) > 0){
				return false;
			}
			return true;
		}

		public function errors(){
			return $this->_error;
		}
	}