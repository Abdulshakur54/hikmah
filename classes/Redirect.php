<?php
	class Redirect{
		public static function to($location){
			if(is_numeric($location)){
				switch($location) {
					case 404:
						http_response_code(404);
						include 'include/error/404.php';
						exit();				}
			}
			header('Location: '.$location);
			exit();
		}

		//this function helps us to redirect using absolute links by defining some links to be appended to
		public static function home($location, $cat = 100){
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
			$url = Config::get('server/name').'/'.$category.$location;
			header('Location: '.$url);
			exit();
		}
	} 