<?php
$GLOBALS['configuration'] = array(
	'mysql' => array(
		'host' => '127.0.0.1',
		'db_name' => 'hikmah',
		'db_password' => '190587Ab',
		'db_username' => 'Abdulshakur54'
	),
	'cookie' => array(
		'table' => 'users_cookie',
		'table0' => 'mgt_cookie',
		'table1' => 'staff_cookie',
		'table2' => 'std_cookie',
		'table3' => 'adm_cookie',
		'id_column' => 'user_id', //this must be the foreign key of the id in the users table
		'id_column0' => 'mgt_id', //this must be the foreign key of the id in the users table
		'id_column1' => 'staff_id', //this must be the foreign key of the id in the users table
		'id_column2' => 'std_id', //this must be the foreign key of the id in the users table
		'id_column3' => 'adm_id', //this must be the foreign key of the id in the users table
		'hash_column' => 'hash',
		'expiry' => 864000, //2 weeks
		'name' => 'remember',
		'name0' => 'rem_mgt',
		'name1' => 'rem_staff',
		'name2' => 'rem_std',
		'name3' => 'rem_adm',
		'path' => '/', //this means all in the domain including subdomains can use the cookie
		'samesite' => 'strict',  //can have value of none, lax, strict
		'domain' => Utility::escape($_SERVER['SERVER_NAME']),
		'secure' => false,    //sets if cookies can only be transmitted by https requests
		'httponly' => true //set if cookies should not be accessed by client side scripting languages

	),
	'session' => array(
		'name' => 'user',
		'name0' => 'mgt',
		'name1' => 'staff',
		'name2' => 'std',
		'name3' => 'adm',
		'token_name' => 'token',
		'lastpage' => 'lastpage',
		'alt_lastpage' => 'alt_lastpage',
		'options' => ['gc_maxlifetime' => 300, 'cookie_secure' => false, 'cookie_httponly' => true, 'use_strict_mode' => true]
	),
	'users' => array(
		'table_name' => 'users',
		'table_name0' => 'management',
		'table_name1' => 'staff',
		'table_name2' => 'student',
		'table_name3' => 'admission',
		'menu_table'=>'users_menu',
		'username_column' => 'user_id', //the column holding the username in the users table
		'username_column0' => 'mgt_id', //the column holding the username in the users table
		'username_column1' => 'staff_id', //the column holding the username in the users table
		'username_column2' => 'std_id', //the column holding the username in the users table
		'username_column3' => 'adm_id', //the column holding the username in the users table
		'password_column' => 'password', //the column holding the password in the users table
		'id_column' => 'id',
		'alert' => 'ex_alert',
		'alert0' => 'ex_mgt_alert',
		'alert1' => 'ex_staff_alert',
		'alert2' => 'ex_std_alert',
		'alert3' => 'ex_adm_alert'
	),
	'webmail' => [
		'username' => "info@hikmahschools.com", // SMTP account username
		'password' => "cZ?gGG~I_cW_", // SMTP account password
		'name' => 'Hikmah Group Of Schools'
	],
	'server' => array(
		'name' => 'http://' . Utility::escape($_SERVER['HTTP_HOST']) . '/hikmah',
		'protocol' => 'http://'
	),
	'error' => array(
		'log_file' => '../errors/log_file.txt'
	),
	'url' => array( //this is needed to handle urls in the website
		'home' => '',
		'home_portal' => 'portal/',
		'mgt_portal' => 'portal/management/',
		'std_portal' => 'portal/student/',
		'staff_portal' => 'portal/staff/',
		'adm_portal' => 'portal/admission/',
		'exam_portal' => 'exam/'
	),
	'menu' => [
		'menu_table' => 'menu',
		'role_table' => 'role',
		'user_menu_table' => 'user_menu',
		'role_menu_table'=>'roles_menu'
	],
	'audit'=>[
		'table'=>'audit'
	],
	'hikmah'=>[
		'subject_teacher_role'=>23,
		'class_teacher_role'=>24,
		'staff_role'=>6
	],
	'sms'=>[
'email'=>'mabdulshakur54@gmail.com',
'password'=>'190587Ab',
'sender_name'=>'HIKMAH',
	]
);

class Config
{

	public static function get(string $path)
	{
		$config = $GLOBALS['configuration'];
		if (!empty($path)) {
			$array = explode('/', $path);

			foreach ($array as $path) {
				if (isset($config[$path])) {
					$config = $config[$path];
				}
			}
			return $config;
		} else {
			echo 'HelperClassError: Error with parameter passed';
		}
	}
}
