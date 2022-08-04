<?php
	
	class Input{
		//this method checks if a form has been submitted via post or get method
		public static function submitted($mtd = 'post'){
			 $serverMethod = Utility::escape($_SERVER['REQUEST_METHOD']);
			 return (strtoupper($mtd) === $serverMethod) ? true:false;
		}


		//this method gets the userinput from a form field using the method which the form was submitted with
		public static function get($fieldname){
			if(!empty($_REQUEST[$fieldname])){
				return $_REQUEST[$fieldname];
			}
			return '';
		}

		//this method gets the userinput from a form field using the method which the form was submitted with
		//it returns a decoded value
		public static function getDecoded($fieldname){
		if (!empty($_REQUEST[$fieldname])) {
			return rawurldecode($_REQUEST[$fieldname]);
		}
		return '';
		}

	}