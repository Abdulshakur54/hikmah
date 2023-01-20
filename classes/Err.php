<?php
	class Err{
		public static function log_to_file($err_msg){
			error_log($err_msg,3,Config::get('error/log_file'));
			echo 'success';
		}

	public static function tryExec(Closure $closure, string $errorMsg, DB $conn = null)
	{
		try {
			if (!empty($conn)) {
				$conn->beginTransaction();
				$closure();
				$conn->commit();
			} else {
				$closure();
			}
		} catch (PDOException $e) {
			if (!empty($conn)) {
				$conn->rollBack();
			}
			echo $errorMsg;
		}
	}
	}