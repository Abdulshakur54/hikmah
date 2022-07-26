<?php
	class Err{
		public static function log_to_file($err_msg){
			error_log($err_msg,3,Config::get('error/log_file'));
			echo 'success';
		}
	}