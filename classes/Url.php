<?php
	class Url{

		private $_baseUrl;
		private $_http;


		public function __construct(){
			$this->_baseUrl = Config::get('server/name');
			$this->_http = Config::get('server/protocol');
			
		}

		//help generate link to a pages
		public function to($url, $cat=100) :string{ //$cat is set to 100 so that te default block can be run when no value is passed in
			if(!isset($cat)){
				$dir = pathinfo($this->getCurrentPage())['dirname'];
				return $dir.'/'.$url;
			}

			switch($cat){
				case 0:
					$category = Config::get('url/home_portal');
				break;
				case 1:
					$category = Config::get('url/mgt_portal');
				break;
				case 2:
					$category = Config::get('url/staff_portal');
				break;
				case 3:
					$category = Config::get('url/std_portal');
				break;
				case 4:
					$category = Config::get('url/exam_portal');
				break;
				case 5:
					$category = Config::get('url/adm_portal');
				break;
				default:
					$category = Config::get('url/home');
			}
			return $this->_baseUrl.'/'.$category.$url;
		}

		

		function getCurrentPage(){
			return $this->_http. Utility::escape($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
		}
               


	}