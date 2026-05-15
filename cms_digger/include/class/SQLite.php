<?php

class Database
{
	// Usage
	/*{{{
	
	define('DEBUG', true);
	define('VERBOSE', true);
	
	$return = Database::open('database.sqlite');
	if(!$return) {
		trigger_error("Can't open database file", E_USER_WARNING);
		return(false);
	}
	
	$table_name = "/abcd/xyz";
	$columns_describe = [
			"i" => 0,
			"f" => 0.0,
			"s" => '',
	];
	$return = Database::create_table($table_name, $columns_describe);
	if(!$return) {
		trigger_error("Can't create table", E_USER_WARNING);
		return(false);
	}
	
	$sql = 
<<<HEREDOC
INSERT INTO '{$table_name}' (i, f, s) VALUES (:i, :f, :s);
HEREDOC;
	$data = [
		'i' => 1,
		'f' => 1.1,
		's' => '01',
	];
	$return = Database::query($sql, $data);
	if(!is_int($return)) {
		trigger_error("Can't insert into table", E_USER_WARNING);
		return(false);
	}
	$id = $return;
	
	$sql = 
<<<HEREDOC
UPDATE '{$table_name}' SET i=:i, f=:f, s=:s WHERE id=:id;
HEREDOC;
	$data = [
		'i' => 2,
		'f' => 2.2,
		's' => '02',
		'id' => $id,
	];
	$return = Database::query($sql, $data);
	if(!$return) {
		trigger_error("Can't update into table", E_USER_WARNING);
		return(false);
	}
	
	$sql = 
<<<HEREDOC
SELECT * FROM '{$table_name}';
HEREDOC;
	$return = Database::query($sql);
	if(!is_array($return)) {
		trigger_error("Can't select from table", E_USER_WARNING);
		return(false);
	}
	var_dump($return);
	
	$sql = 
<<<HEREDOC
DELETE FROM '{$table_name}' WHERE id=:id;
HEREDOC;
	$data = [
		'id' => $id,
	];
	$return = Database::query($sql, $data);
	if(!$return) {
		trigger_error("Can't delete from table", E_USER_WARNING);
		return(false);
	}
	
	}}}*/

	static $SQLite3 = NULL;
	
	static function open(string $database_file_path) // (bool) true
	{//{{{//
		
		try {
			self::$SQLite3 = new SQLite3($database_file_path, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
		}
		catch(Exception $Exception) {
			
			$error_message = "SQLite3: ".$Exception->getMessage();
			trigger_error($error_message, E_USER_WARNING);
			
			if(defined('DEBUG') && DEBUG) var_dump(['$database_file_path' => $database_file_path]);
			trigger_error("Can't construct SQLite3 class", E_USER_WARNING);
			
			return(false);
		}
		register_shutdown_function('Database::close');
		
		return(true);
		
	}//}}}//
	
	static function close()
	{//{{{//
		
		if(is_object(self::$SQLite3)) {
			$return = self::$SQLite3->close();
			if(!$return) {
				trigger_error("Can't close SQLite3 database", E_USER_WARNING);
				return(false);
			}
			self::$SQLite3 = NULL;
		}
		
	}//}}}//
	
	static function query(string $sql, array $data = []) // (bool) true || (array) $ROW || (int) $id
	{//{{{//
		
		$return = self::$SQLite3->prepare($sql);
		if(!is_object($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$sql' => $sql]);
			trigger_error("Can't prepare sql request", E_USER_WARNING);
			return(false);
		}
		$SQLite3Stmt = $return;
		
		foreach($data as $key => $value) {
			if(!(
				is_string($key)
				&& preg_match('/^[_a-zA-Z0-9]+$/', $key) === 1
			)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$key' => $key]);
				trigger_error("Incorrect key of data item", E_USER_WARNING);
				return(false);
			}
			
			$type = gettype($value);
			switch($type) {
				case('integer'):
					$type = SQLITE3_INTEGER;
					break;
				case('double'):
					$type = SQLITE3_FLOAT;
					break;
				case('string'):
					$type = SQLITE3_BLOB;
					break;
				default:
					if(defined('DEBUG') && DEBUG) var_dump(['$value' => $value]);
					trigger_error("Unsupported type of data item value", E_USER_WARNING);
					return(false);
			}
			
			$return = $SQLite3Stmt->bindValue(":{$key}", $value, $type);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump([
					':{$key}' => ":{$key}",
					'$value' => $value,
				]);
				trigger_error("Can't bind value to statement variable", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = $SQLite3Stmt->execute();
		if(!is_object($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$sql' => $sql]);
			trigger_error("Can't execute a prepared statement", E_USER_WARNING);
			return(false);
		}
		$SQLite3Result = $return;
		
		$result = true;
		
		$pattern = '/^\s*select\s+.+$/i';
		$return = preg_match($pattern, $sql);
		if($return === 1) {
			$result = [];
			while(true) {
				$return = $SQLite3Result->fetchArray(SQLITE3_ASSOC);
				if($return === false) break;
				array_push($result, $return);
			}
		}
		
		$pattern = '/^\s*insert\s+.+$/i';
		$return = preg_match($pattern, $sql);
		if($return === 1) {
			$result = self::$SQLite3->lastInsertRowID();
		}
		
		$SQLite3Result->finalize();
		$SQLite3Stmt->close();
		
		return($result);
		
	}//}}}//
	
	static function check_table_name(string $table_name) // (bool) true
	{//{{{//
		
		$pattern = '/^[_a-zA-Z0-9\/\-\.]+$/';
		$return = preg_match($pattern, $table_name);
		if($return !== 1) {
			if (defined('DEBUG') && DEBUG) var_dump(['$table_name' => $table_name]);
			trigger_error("Table name contains invalid characters", E_USER_WARNING);
			return(false);
		}
		return(true);
		
	}//}}}//
	
	static function check_column_name(string $column_name) // (bool) true
	{//{{{//
		
		$pattern = '/^[_a-zA-Z0-9]+$/';
		$return = preg_match($pattern, $column_name);
		if($return !== 1) {
			if (defined('DEBUG') && DEBUG) var_dump(['$column_name' => $column_name]);
			trigger_error("Column name contains invalid characters", E_USER_WARNING);
			return(false);
		}
		return(true);
		
	}//}}}//
	
	// DROP TABLE IF EXISTS
	static function drop_table(string $table_name) // (bool) true
	{//{{{//
	 	
		$return = self::check_table_name($table_name);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$table_name}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = self::query($sql);
		if(!$return) {
			trigger_error("Can't drop table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	// CREATE TABLE IF NOT EXISTS
	static function create_table(string $table_name, array $columns_describe) // (bool) true
	{//{{{//
	 	
		$return = self::check_table_name($table_name);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE TABLE IF NOT EXISTS '{$table_name}' (
	id INTEGER PRIMARY KEY AUTOINCREMENT
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		foreach($columns_describe as $name => $describe) {
			$return = self::check_column_name($name);
			if(!$return) {
				trigger_error("Incorrect column name", E_USER_WARNING);
				return(false);
			}
		
			$sql .= "\n\t,";
					
			$type = gettype($describe);
			switch($type) {
				case('integer'):
					$sql .= "{$name} INTEGER";
					break;
				case('double'):
					$sql .= "{$name} REAL";
					break;
				case('string'):
					$sql .= "{$name} BLOB";
					break;
				default:
					if(defined('DEBUG') && DEBUG) var_dump(['$describe' => $describe]);
					trigger_error("Unsupported type of describe value", E_USER_WARNING);
					return(false);
			}
	
		}
		$sql .= "\n);";
		
		$return = self::query($sql);
		if(!$return) {
			trigger_error("Can't create table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
}

