<?php
	
	class Input{
		public static $_inp_mtd;


		//this method checks if a form has been submitted via post or get method
		public static function submitted($mtd = 'post'){
			
			switch ($mtd) {

				case 'post':
					if(!empty($_POST)){
						self::$_inp_mtd = $_POST;
						return true;
					}else{
						return false;
					}
				
				case 'get':
					if(!empty($_GET)){
						self::$_inp_mtd = $_GET;
						return true;
					}else{
						return false;
					}

				default:
					return false;
			}
		}


		//this method gets the userinput from a form field using the method which the form was submitted with
		public static function get($fieldname){
			if(isset(self::$_inp_mtd[$fieldname])){
				return self::$_inp_mtd[$fieldname];
			}
			return '';
		}

		//this method gets the userinput from a form field using the method which the form was submitted with
		//it returns a decoded value
		public static function getDecoded($fieldname){
			if(isset(self::$_inp_mtd[$fieldname])){
				return rawurldecode(self::$_inp_mtd[$fieldname]);
			}
			return '';
		}

	}