<?php
	class Cookie{

		public static function set($cookie_name, $cookie_value, $expiry){
			$options = array(
				'expires' =>$expiry,
				'path' =>Config::get('cookie/path'),
				'domain' =>Config::get('cookie/domain'),
				'secure' =>Config::get('cookie/secure'),
				'httponly' =>Config::get('cookie/httponly'),
				'samesite' => Config::get('cookie/samesite')
			);
			setcookie($cookie_name, $cookie_value, $options);
		}


// . '; ' . Config::get('cookie/samesite'),


		public static function get($cookie_name){
			return Utility::escape($_COOKIE[$cookie_name]);
		}


		public static function exists($cookie_name){
			return isset($_COOKIE[$cookie_name]);
		}

		public static function delete($cookie_name){
			if(self::exists($cookie_name)){
				self::set($cookie_name,'',time()-3600);
			}
		}
	}