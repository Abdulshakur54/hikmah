<?php

//this class assumes that each user has a unique username and a unique id
class User
{

	protected $_db; //stores an instance of connection to the database
	private $_data; //stores the data of a known user
	private $_pwd_col; //stores the password column name of the user table
	private $_table_name;
	private $_id_col;
	private $_username_col;
	private $_ses_name;
	private $_ck_table;
	private $_ck_id_col;
	private $_ck_name;
	public function __construct($cat = null, $user_id = null)
	{ //100 is the default value
		$this->_db = DB::get_instance();
		switch ($cat) {
			case 1:
				#users
				$this->_table_name = Config::get('users/table_name0');
				$this->_username_col  = Config::get('users/username_column0');
				#session
				$this->_ses_name = Config::get('session/name0');
				#cookie
				$this->_ck_table = Config::get('cookie/table0');
				$this->_ck_id_col = Config::get('cookie/id_column0');
				$this->_ck_name = Config::get('cookie/name0');
				break;
			case 2:
				$this->_table_name = Config::get('users/table_name1');
				$this->_username_col  = Config::get('users/username_column1');
				$this->_ses_name = Config::get('session/name1');
				#cookie
				$this->_ck_table = Config::get('cookie/table1');
				$this->_ck_id_col = Config::get('cookie/id_column1');
				$this->_ck_name = Config::get('cookie/name1');
				break;
			case 3:
				$this->_table_name = Config::get('users/table_name2');
				$this->_username_col  = Config::get('users/username_column2');
				$this->_ses_name = Config::get('session/name2');
				#cookie
				$this->_ck_table = Config::get('cookie/table2');
				$this->_ck_id_col = Config::get('cookie/id_column2');
				$this->_ck_name = Config::get('cookie/name2');
				break;
			case 4:
				$this->_table_name = Config::get('users/table_name3');
				$this->_username_col  = Config::get('users/username_column3');
				$this->_ses_name = Config::get('session/name3');
				#cookie
				$this->_ck_table = Config::get('cookie/table3');
				$this->_ck_id_col = Config::get('cookie/id_column3');
				$this->_ck_name = Config::get('cookie/name3');
				break;
			default:
				$this->_table_name = Config::get('users/table_name');

				$this->_username_col  = Config::get('users/username_column');
				$this->_ses_name = Config::get('session/name');
				#cookie
				$this->_ck_table = Config::get('cookie/table');
				$this->_ck_id_col = Config::get('cookie/id_column');
				$this->_ck_name = Config::get('cookie/name');
				break;
		}
		if ($user_id) {
			$this->find($user_id); //populate the $_data instance variable                    
		}
		$this->_pwd_col = Config::get('users/password_column'); //gets password column
		$this->_id_col = Config::get('users/id_column');
	}



	/*this method creates a new user to the database
			$user_values should be an associative array
			whose keys are used as column names and values
			are used as table values
			it returns a boolean value
                        this method does not hash any user values, thus users password should be hashed before being used as part of the parameter
			*/
	public function create($user_values): bool
	{
		$sql = "insert into " . $this->_table_name . '(';
		$insert = '';
		$values = array();
		$placeholder = '';
		foreach ($user_values as $column_name => $field_val) {
			$insert .= $column_name . ',';
			$placeholder .= '?,';
			$values[] = $field_val;
		}
		$sql .= substr($insert, 0, strlen($insert) - 1) . ') values(' . substr($placeholder, 0, strlen($placeholder) - 1) . ')';
		return $this->_db->query($sql, $values);
	}


	/*this method assumes that each user has a unique username
			it logs in a user and sets a cookie if the $remember parameter is true
		*/
	public function login($remember = false): bool
	{
		$log_in = true; //$log_in will be true if there is successful login without any error
		$id_col = $this->_id_col;
		$id = $this->data()->$id_col;
		Session::set($this->_ses_name, $id);
		if ($remember) {
			$cookie_table = $this->_ck_table;
			$id_column = $this->_ck_id_col;
			$cookie_column = Config::get('cookie/hash_column');
			/* check if the user has a hash in the hash table*/
			if ($this->_db->query('select * from ' . $cookie_table . ' where ' . $id_column . ' = ?', array($id))) {
				if ($this->_db->row_count()) { //returns true if a row is found

					//delete the hash
					$log_in = $this->_db->query('delete from ' . $cookie_table . ' where ' . $id_column . ' =?', array($id));
				}
			} else {
				$log_in = false;
			}
			$hash = Hash::generate(Token::create(32)); //generates a hash usen a random token
			//insert the hash into the cookie table and set the cookie
			$id_col = $this->_ck_id_col;
			if ($this->_db->query('insert into ' . $cookie_table . '(' . $id_column . ', ' . $cookie_column . ') values(?,?)', array($id, $hash))) {

				//store or set the cookie
				Cookie::set($this->_ck_name, $hash, time() + Config::get('cookie/expiry'));
				$log_in = true;


				//added so as to extend cookie for hikmah
				Cookie::set('cookie_table', $cookie_table, time() + Config::get('cookie/expiry'));
				//end of added to extend cookie for hikmah
			} else {
				$log_in = false;
			}
		}
		return $log_in;
	}


	/*
			this method logs out a user by unsetting a session
			returns vo												\	
		*/
	public function logout(): bool
	{
		$log_out = false;  //$log_out will be true when logout operation is successful without error
		$session_id = Session::get($this->_ses_name); //stores the session before deleting it

		if (Session::exists($this->_ses_name)) {
			Session::delete($this->_ses_name);
			$log_out = true;
		}

		//remove cookie from database and reset it to trigger removal from user client

		if (Cookie::exists($this->_ck_name)) {
			//delete cookie from get_browser
			Cookie::delete($this->_ck_name);
			//added so as to extend cookie for hikmah
			Cookie::delete('cookie_table');
			Cookie::delete('user');
			//end of added to extend cookie for hikmah
			//delete cookie from database
			$log_out = $this->_db->query('delete from ' . $this->_ck_table . ' where ' . $this->_ck_id_col . ' = ?', array($session_id));
		}
		session_destroy(); //completely destroy any association with session data
		return $log_out;
	}



	/*
			method checks if the user is logged in
			it does this depending on if the user session is set
			it queries the users data which can be gotten from the data() method
			returns a boolean
		*/
	public function isLoggedIn(): bool
	{
		$logged_in = Session::exists($this->_ses_name);
		if ($logged_in) {
			$this->find($this->ses_id());
		}
		return $logged_in;
	}



	/*
			this method checks if a user is in the database and returns a boolean
			$user_id should be either a username or a numeric id.
			at default, this method will also get the users data from the query, this
			data can ge gotten from the data() method.
			$get_data parameter should be false, if all you want is to verify if the user exists
		*/
	public function find($user_param, $get_data = true): bool
	{
		$column = (is_numeric($user_param)) ? $this->_id_col : $this->_username_col;
		if ($this->_db->query('select * from ' . $this->_table_name . ' where ' . $column . ' = ?', array($user_param))) {
			if ($get_data && $this->_db->row_count() > 0) {
				$this->_data = $this->_db->one_result();
				return true;
			}
			return false;
		}
	}



	//a getter for data
	public function data()
	{
		return $this->_data;
	}


	//a getter for the rank
	public function getRank($pos = null): int
	{
		//gets the rank from the data gotten from the datatabase user table's query
		if (!isset($pos)) {
			return $this->data()->rank;
		}
	}



	//a getter for the postion
	public static function getPosition($rank, $asst = 0, $fullPosition = false)
	{
		switch ($rank) {
			case 1:
				switch ($asst) {
					case 0:
						return 'Director';
					case 1:
						return 'Deputy Director';
					case 2:
						return 'Secretary to Director';
				}
			case 2:
				switch ($asst) {
					case 0:
						return 'A.P.M';
					case 1:
						return 'Deputy A.P.M';
					case 2:
						return 'Secretary to A.P.M';
				}
			case 3:
				switch ($asst) {
					case 0:
						return 'Accountant';
					case 1:
						return 'Deputy A.P.M';
					case 2:
						return 'Secretary to Accountant';
				}
			case 4:
				switch ($asst) {
					case 0:
						return 'I.C';
					case 1:
						return 'Deputy A.P.M';
					case 2:
						return 'Secretary to I.C';
				}
			case 5:
				switch ($asst) {
					case 0:
						return 'H.O.S';
					case 1:
						return 'Deputy H.O.S';
					case 2:
						return 'Secretary to H.O.S';
				}
			case 6:
				switch ($asst) {
					case 0:
						return 'H.R.M';
					case 1:
						return 'Deputy A.P.M';
					case 2:
						return 'Secretary to H.R.M';
				}
			case 7:
				return 'Teacher';
			case 8:
				return 'Staff';
			case 9:
				return 'Student';
			case 10:
				return 'Student';
			case 11:
				return 'Admission Student';
			case 12:
				return 'Admission Student';
			case 13:
				return 'Alumni';
			case 14:
				return 'Alumni';
			case 15:
				return 'Teacher';
			case 16:
				return 'Staff';
			case 17: //Islamiyah head of school
				switch ($asst) {
					case 0:
						return 'Mudir';
					case 1:
						return 'Vice Mudir';
					case 2:
						return 'Secretary to Mudir';
				}
		}
	}

	private static function get_random_greeting(): string
	{
		$greetings = ['How have you been?', 'How is it going?', 'Nice to have you back', 'Hope you having a good day'];
		$index = rand(0, count($greetings) - 1);
		return $greetings[$index];
	}

	public function get_role_id($rank, $asst = 0)
	{
		$role_table = Config::get('menu/role_table');
		switch ($rank) {
			case 1:
				return $this->_db->get($role_table, 'id', "role='director'")->id;
			case 2:
				return $this->_db->get($role_table, 'id', "role='Academic Planning Manager'")->id;
			case 3:
				return $this->_db->get($role_table, 'id', "role='Accountant'")->id;
			case 4:
				return $this->_db->get($role_table, 'id', "role='Islamiyyah Coordinator'")->id;
			case 5:
			case 17:
				return $this->_db->get($role_table, 'id', "role='Head of School'")->id;
			case 6:
				return $this->_db->get($role_table, 'id', "role='Human Resource Manager'")->id;
			case 7:
			case 8:
			case 15:
			case 16:
				return $this->_db->get($role_table, 'id', "role='Staff'")->id;
			case 9:
			case 10:
				return $this->_db->get($role_table, 'id', "role='Student'")->id;
			case 11:
			case 12:
				return $this->_db->get($role_table, 'id', "role='Admission Student'")->id;
			case 13:
			case 14:
				return $this->_db->get($role_table, 'id', "role='Alumni'")->id;
		}
	}


	//a getter for the postion
	public static function getFullPosition($rank)
	{
		switch ($rank) {
			case 1:
				return 'Director';
			case 2:
				return 'Academic Planning Manager';
			case 3:
				return 'Accountant';
			case 4:
				return 'Islamiyah Co-Ordinator';
			case 5:
				return 'Head of School';
			case 6:
				return 'Human Resource Manager';
			case 7:
				return 'Teacher';
			case 8:
				return 'Staff';
			case 9:
				return 'Student';
			case 10:
				return 'Student';
			case 11:
				return 'Admission Student';
			case 12:
				return 'Admission Student';
			case 13:
				return 'Alumni';
			case 14:
				return 'Alumni';
			case 15:
				return 'Teacher';
			case 16:
				return 'Staff';
			case 17:
				return 'Mudir'; //islamiyah head of school
		}
	}

	//this method checks if the user has an account and if his password matches the password stored in the database
	public function pass_match($username, $password): bool
	{

		if ($this->find($username)) { //checks if the user exist
			$pwd_col = $this->_pwd_col;
			return password_verify($password, $this->data()->$pwd_col); //verifty the password
		}
		return false;
	}


	/*
			this method updates users data in the users table, 
			the $values param is an associate array of where each key is a column
			name and each value is a corresponding update value
			returns a boolean
		*/

	public function update($values = array(), $id = null): bool
	{
		$sql = 'update ' . $this->_table_name . ' set ';
		$data = array();
		foreach ($values as $col => $col_val) {
			$sql .= $col . ' = ?,';
			$data[] = $col_val;
		}
		$sql = substr($sql, 0, strlen($sql) - 1) . ' where ' . $this->_id_col . ' =?';
		if (isset($id)) {
			$data[] = $id;
		} else {
			$data[] = $this->ses_id();
		}
		return $this->_db->query($sql, $data);
	}

	/*
            used when a user wants to change password
         * the method confirms that the password the user enters as same as the password in the database
         * before permitting to change his password to a new one
         * $old_pwd parameter is the password that is to be changed to new password
         */
	public function conf_pwd($old_pwd): bool
	{
		$pwd_col = $this->_pwd_col;
		return password_verify($old_pwd, $this->data()->$pwd_col);
	}


	//returns the session id if the user is logged in
	public function ses_id()
	{
		return Session::get($this->_ses_name);
	}


	public  function changePassword($new_pwd): bool
	{
		$val = array();
		$val[$this->_pwd_col] = password_hash($new_pwd, PASSWORD_DEFAULT);
		return $this->update($val);
	}

	public function getUsernameColumn()
	{
		return $this->_username_col;
	}

	public function getIdColumn()
	{
		return $this->_id_col;
	}



	public function isRemembered(): bool
	{
		if (Session::exists($this->_ses_name)) {
			$this->find($this->ses_id()); //get user data
			return true;
		}
		if (Cookie::exists($this->_ck_name)) {

			if ($this->_db->query('select * from ' . $this->_ck_table . ' where ' . Config::get('cookie/hash_column') . ' = ?', array(Cookie::get($this->_ck_name)))) {

				if ($this->_db->row_count()) {
					//login the user by setting sessions
					$ck_id_col = $this->_ck_id_col;
					Session::set($this->_ses_name, $this->_db->one_result()->$ck_id_col);
					$this->find($this->ses_id()); //get user data
					return true;
				}
				return false;
			}
			return false;
		}
		return false;
	}

	public static function select($role_id = ''): array
	{
		$db = DB::get_instance();
		$table = Config::get('users/table_name');
		$user_column = Config::get('users/user_id');
		if ($role_id = '') {
			return $db->select($table, '*', '', $user_column);
		}
		return $db->get($table, '*', 'role_id = ' . $role_id, $user_column);
	}

	public static function get($role_id = ''): array
	{
		$db = DB::get_instance();
		$table = Config::get('users/table_name');
		$user_column = Config::get('users/user_id');
		if ($role_id = '') {
			return $db->select($table, '*', '', $user_column);
		}
		return $db->get($table, '*', 'role_id = ' . $role_id, $user_column);
	}

	public static function get_profile(string $username)
	{
		$first_letter = substr($username, 0, 1);
		$db = DB::get_instance();
		$data = [];
		if (is_numeric($first_letter)) {
			$db->query('select admission.*,states.name as state_of_origin,local_governments.name as lga_of_origin from admission inner join states on states.id = admission.state inner join local_governments on local_governments.id = admission.lga where admission.adm_id = ?', [$username]);
			if ($db->row_count() > 0) {
				$data = $db->one_result();
			}
		} else {

			switch (strtoupper($first_letter)) {
				case 'H':
					$db->query('select student.*,student2.*,class.class,states.name as state_of_origin,local_governments.name as lga_of_origin from student inner join student2 on student.std_id=student2.std_id inner join class on student.class_id = class.id inner join states on states.id = student2.state inner join local_governments on local_governments.id = student2.lga where student.std_id = ?', [$username]);
					if ($db->row_count() > 0) {
						$data = $db->one_result();
					}
					break;
				case 'M':
					$db->query('select management.*,states.name as state_of_origin,local_governments.name as lga_of_origin from management inner join states on states.id = management.state inner join local_governments on local_governments.id = management.lga where management.mgt_id = ?', [$username]);
					if ($db->row_count() > 0) {
						$data = $db->one_result();
					}
					break;
				case 'S':
					$db->query('select staff.*,states.name as state_of_origin,local_governments.name as lga_of_origin from staff inner join states on states.id = staff.state inner join local_governments on local_governments.id = staff.lga where staff.staff_id = ?', [$username]);
					if ($db->row_count() > 0) {
						$data = $db->one_result();
					}
					break;
			}
		}
		return $data;
	}

	public static function get_specifics($username, $rank, $position)
	{
		$specifics = [];
		$user = new User();
		$url = new Url();
		$specifics['rank'] = self::getPosition($rank, $position);
		$specifics['role'] = User::getFullPosition($rank);
		$first_letter = substr($username, 0, 1);
		if (is_numeric($first_letter)) {
			$specifics['profile_photo_path'] = $url->to('uploads/passports/', 5);
			$specifics['show_parents'] = false;
		} else {

			switch (strtoupper($first_letter)) {
				case 'H':
					$specifics['profile_photo_path'] = $url->to('uploads/passports/', 3);
					$specifics['show_parents'] = true;
					break;
				case 'M':
					$specifics['profile_photo_path'] = $url->to('uploads/passports/', 1);
					$specifics['show_parents'] = false;
					break;
				case 'S':
					$specifics['profile_photo_path'] = $url->to('uploads/passports/', 2);
					$specifics['show_parents'] = false;
					break;
			}
		}
		return $specifics;
	}

	public static function get_link(string $username):string{
		$first_letter = substr($username, 0, 1);
		if (is_numeric($first_letter)) {
			return 'admission';
		} else {

			switch (strtoupper($first_letter)) {
				case 'H':
					return 'student';
				case 'M':
					$db = DB::get_instance();
					$rank = $db->get('management','rank',"mgt_id='$username'")->rank;
					$pos = User::getPosition($rank);
					switch($pos){
						case 'Director':
							return 'management/director';
						case 'A.P.M':
							return 'management/apm';
						case 'H.R.M':
							return 'management/hrm';
						case 'H.O.S':
							return 'management/hos';
						case 'Accountant':
							return 'management/accountant';
						case 'I.C':
							return 'management/ic';
					}
				case 'S':
					return 'staff';
				default:
				return '';
			}
		}
	}

	public static function get_user_greeting(string $username): string
	{
		$db = DB::get_instance();
		$first_letter = substr($username, 0, 1);
		if (is_numeric($first_letter)) {
			$table = 'admission';
			$cookie_table = 'adm_cookie';
			$user_data = $db->get($table, 'fname,oname,lname', "adm_id ='$username'");
			if (!empty($db->get($cookie_table, 'id', "adm_id = '$username'"))) {
				return 'Welcome back, ' . ucfirst($user_data->fname);
			} else {
				return '<span>Good ' . Utility::getPeriod() . ' ' . ucfirst($user_data->fname) . ', ' . self::get_random_greeting() . '</span>';
			}
		} else {

			switch (strtoupper($first_letter)) {
				case 'H':
					$table = 'student';
					$cookie_table = 'std_cookie';
					$user_data = $db->get($table, 'fname,oname,lname', "std_id ='$username'");
					if (!empty($db->get($cookie_table, 'id', "std_id = '$username'"))) {
						return 'Welcome back, ' . ucfirst($user_data->fname);
					} else {
						return '<span>Good ' . Utility::getPeriod() . ' ' . ucfirst($user_data->fname) . ', ' . self::get_random_greeting() . '</span>';
					}
				case 'M':
					$table = 'management';
					$cookie_table = 'mgt_cookie';
					$user_data = $db->get($table, 'fname,oname,lname,rank,asst', "mgt_id ='$username'");
					$rank = (int)$user_data->rank;
					$position = self::getPosition($rank, (int)$user_data->asst);
					if (!empty($db->get($cookie_table, 'id', "mgt_id = '$username'"))) {
						return 'Welcome back, ' . $position;
					} else {
						return '<span>Good ' . Utility::getPeriod() . ' ' . $position . ', ' . self::get_random_greeting() . '</span>';
					}
				case 'S':
					$table = 'staff';
					$cookie_table = 'staff_cookie';
					$user_data = $db->get($table, 'fname,oname,lname,title', "staff_id ='$username'");
					if (!empty($db->get($cookie_table, 'id', "staff_id = '$username'"))) {
						return 'Welcome back, ' . $user_data->title . '. ' . ucfirst($user_data->fname);
					} else {
						return '<span>Good ' . Utility::getPeriod() . ' ' . $user_data->title . '. ' . ucfirst($user_data->fname) . ', ' . self::get_random_greeting() . '</span>';
					}
				default:
					return '';
			}
		}
	}

	public static function get_profile_image_path($username) :string{

		$db = DB::get_instance();
		$url = new Url();
		$first_letter = substr($username, 0, 1);
		if (is_numeric($first_letter)) {
			$picture = $db->get('admission', 'picture', "adm_id ='$username'")->picture;
			return $url->to('uploads/passports/'.$picture,5);
		} else {

			switch (strtoupper($first_letter)) {
				case 'H':
					$picture = $db->get('student', 'picture', "std_id ='$username'")->picture;
					return $url->to('uploads/passports/' . $picture, 3);
				case 'M':
					$picture = $db->get('management', 'picture', "mgt_id ='$username'")->picture;
					return $url->to('uploads/passports/' . $picture, 1);
				case 'S':
					$picture = $db->get('staff', 'picture', "staff_id ='$username'")->picture;
					return $url->to('uploads/passports/' . $picture, 2);
				default:
					return '';
			}
		}
	
	}

	public static function hikmahDashboardRememberMe(): bool
	{
		if (Cookie::exists('cookie_table')) {
			$cookie = Cookie::get('cookie_table');
			switch ($cookie) {
				case 'mgt_cookie':
					$user_class = 'Management';
					break;
				case 'staff_cookie':
					$user_class = 'Staff';
					break;
				case 'std_cookie':
					$user_class = 'Student';
					break;
				case 'adm_cookie':
					$user_class = 'Admission';
					break;
				default:
					return false;
			}
			$user = new $user_class();
			$remembered =  $user->isRemembered();
			if ($remembered) {
				$username = Cookie::get('user');
				Session::set('user',$username);
				$greeting = User::get_user_greeting($username);
				Session::set_flash(Config::get('hikmah/flash_welcome'),$greeting);
				return true;
			}
			return false;
		}
		return false;
	}
}
