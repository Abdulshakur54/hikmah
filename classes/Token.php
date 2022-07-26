<?php
class Token
{

	//generates a token to protect crsf attack
	public static function generate($num = 32, $token_name = null)
	{
		if (!isset($token_name)) {
			$token_name = Config::get('session/token_name');
		}
		Session::set($token_name, self::create($num));
		return Session::get($token_name);
	}

	//generates a token made up of random bytes
	public static function create($num = 32)
	{
		return bin2hex(random_bytes($num));
	}


	public static function check($token, $token_name = null): bool
	{
		if (!isset($token_name)) {
			$token_name = Config::get('session/token_name');
		}
		if (Session::exists($token_name) && $token === Session::get($token_name)) {
			Session::delete($token_name);
			return true;
		}
		return false;
	}
}
