<?php
class DB
{
	/*
			$_pdo: holds the database connection
			$_row_count: holds the no of affected rows for basic queries, that insert,updated,delete,select e.tc
			$_query: holds the prepared query
			$_result: holds the result as an object
		*/
	private $_pdo, $_row_count, $_query, $_result;
	private static $_instance, $_instance2, $_instance3; # an instance of the pdo connection
	private $_trans_row_count = array(); #row_count for a multi query 
	private $_sql; #hold the inputted sql statement


	//the constructor function which connects to the database. it is modified as private so we can use the singleton pattern to connect to our databases. this is done with the help of the getInstance() method
	public function __construct()
	{
		try {
			$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db_name'), Config::get('mysql/db_username'), Config::get('mysql/db_password'), array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		} catch (PDOException $e) {
			die('Database Connection Failed');
		}
	}


	//this method returns an instance of the pdo connection | this should always be use for the database connection
	public static function get_instance()
	{
		if (!isset(self::$_instance)) {
			self::$_instance = new DB();
		}
		return self::$_instance;
	}

	//this method is provided to obtain another instance of the pdo connection when needed 
	public static function newConnection()
	{
		return new DB();
	}

	//this method is provided to obtain another instance of the pdo connection when needed 
	public static function get_instance2()
	{
		if (!isset(self::$_instance2)) {
			self::$_instance2 = new DB();
		}
		return self::$_instance2;
	}

	//this method is provided to obtain another instance of the pdo connection when needed 
	public static function get_instance3()
	{
		if (!isset(self::$_instance3)) {
			self::$_instance3 = new DB();
		}
		return self::$_instance3;
	}



	/*this method prepares the sql, binds value to it and executes the query
			it returns a boolean based on if the query is successfully executed
			note: that the query executed successful does not mean it returns a result
		*/

	public function query(string $sql, $val = array()): bool
	{
		$this->_sql = $sql;
		if ($this->_query = $this->_pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL))) {
			return $this->insert_val($val);
		}
		return false;
	}



	//this function helps to bindvalues and execute queries
	private function insert_val($values = array())
	{
		if (count($values)) {
			$this->bindVal($values);
		}
		return $this->execute();
	}

	//this function bindvalues to the prepared statement dynamically
	private function bindVal($values)
	{
		$x = 1;
		foreach ($values as $value) {
			$this->_query->bindValue($x, $value);
			$x++;
		}
	}

	public function requery($values = [])
	{
		$this->insert_val($values);
	}

	/*
			this method executes a query using a prepared statement
			and return true if execution is successful
		
		*/
	private function execute(): bool
	{
		try {
			if ($this->_query->execute()) {
				//ensuring that result is only fetched for select queries
				if (strpos($this->_sql, 'select') === 0) {
					$this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
				}
				$this->_row_count = $this->_query->rowCount();
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			//ensure you handle error hear using error log
			echo $e->getMessage();
			return false;
		}
	}


	public function get_result()
	{
		if ($this->row_count()) {
			return $this->_result;
		}
	}

	public function row_count()
	{
		return $this->_row_count;
	}


	//this method is not built to return any result from a select query
	public function trans_query($queries = []): bool
	{
		if (count($queries)) { #checks that an array of queries have been entered
			$error = false;  #set error to false at default
			$this->_trans_row_count = []; #empty row count as default
			try {
				$this->_pdo->beginTransaction(); //this will turn off autocommit
				foreach ($queries as $query) {
					$t_query = $this->_pdo->prepare($query[0]); #prepares the sql
					$x = 1; #instantiated to 1 so it can be incremented while binding values to the prepared statements
					foreach ($query[1] as $val) { #looping through and binding values for
						$t_query->bindValue($x, $val);
						$x++;
					}
					if ($t_query->execute()) {
						$this->_trans_row_count[] = $t_query->rowCount();
					} else {
						$error = false;
					}
				}
				$this->_pdo->commit();
				if (!$error) {
					return true;
				}
				return false;
			} catch (PDOException $e) {
				echo $e->getMessage();
				$this->_pdo->rollBack();
				return false;
			}
		}
	}


	/*
		returns the no of affected row as an array after a trans_query
		*/
	public function trans_row_count()
	{
		return $this->_trans_row_count;
	}

	public function one_result()
	{
		return $this->get_result()[0];
	}

	public  function update(string $table, array $colAndVal, string $condition = ''): bool
	{
		$placeholders = [];
		$sql = 'update ' . $table . ' set ';
		foreach ($colAndVal as $col => $val) {
			$sql .= $col . '=?,';
			$placeholders[] = $val;
		}
		$sql = trim($sql, "\n,");
		if ($condition != ' ') {
			$sql .= ' where ' . $condition;
		}

		return $this->query($sql, $placeholders);
	}

	public  function insert(string $table, array $colAndVal): bool
	{
		$sql = 'insert into ' . $table . '(';
		$columns = array_keys($colAndVal);
		$values = array_values($colAndVal);
		$columns_string = implode(', ', $columns) . ')';
		$placeholders = '?' . str_repeat(',?', count($columns) - 1);
		$sql .= $columns_string . ' values(' . $placeholders . ')';
		return $this->query($sql, $values);
	}

	public function delete(string $table, string $condition = ''): bool
	{
		$sql = 'delete from ' . $table;
		if ($condition != ' ') {
			$sql .= ' where ' . $condition;
		}
		return $this->query($sql);
	}

	public function select(string $table, string $columns, string $condition = ''): array
	{
		$sql = 'select ' . $columns . ' from ' . $table;
		if ($condition != ' ') {
			$sql .= ' where ' . $condition;
		}
		$this->query($sql);
		if ($this->row_count() > 0) {
			return $this->get_result();
		}
		return [];
	}

	public function get(string $table, string $columns, string $condition = ''): array
	{
		$sql = 'select ' . $columns . ' from ' . $table;
		if ($condition != ' ') {
			$sql .= ' where ' . $condition;
		}
		$sql .= ' limit 1';
		$this->query($sql);
		if ($this->row_count() > 0) {
			return $this->one_result();
		}
		return [];
	}
}
