<?php
class Session
{

	//sets a session
	public static function set($name, $value)
	{
		$_SESSION[$name] = $value;
	}

	public static function get($name)
	{
		if (self::exists($name)) {
			return $_SESSION[$name];
		}
	}

	public static function exists($name): bool
	{
		return (isset($_SESSION[$name])) ? true : false;
	}

	public static function delete($name)
	{
		if (self::exists($name)) {
			unset($_SESSION[$name]);
		}
	}

	//this method sets a flash message
	public static function set_flash($name, $value)
	{
		self::set($name, $value);
	}

	//this method gets the flash message
	public static function get_flash($name)
	{
		if (self::exists($name)) {
			$session = self::get($name); //get the value of the flash message before deleting it
			self::delete($name);
			return $session;
		}
		return '';
	}


	// public static function setLastPage($page){
	// 	$_SESSION['lastpage'] = $page;
	// }

	// public static function lastPageExists(){
	//             $currPage = Config::get('server/protocol').Utility::escape($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
	//             if(isset($_SESSION['lastpage']) && (dirname($_SESSION['lastpage'])== dirname($currPage))){
	//                 return true;
	//             }
	//             return false;
	// }

	// public static function getLastPage(){
	// 	return $_SESSION['lastpage'];
	// }

	public static function setLastPage($page)
	{
		if(self::getLastPage() != $page){
			Session::set(Config::get('session/alt_lastpage'), Session::getLastPage());
			Session::set(Config::get('session/lastpage'), $page);
		}
		
	}

	public static function lastPageExists()
	{

		if (Session::exists(Config::get('session/lastpage'))) {
			return true;
		}
		return false;
	}

	public static function getLastPage()
	{
		return Session::get(Config::get('session/lastpage'));
	}

	public static function getAltLastPage()
	{
		return Session::get(Config::get('session/alt_lastpage'));
	}
}
