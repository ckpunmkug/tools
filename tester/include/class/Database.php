<?php

/*
id INTEGER PRIMARY KEY
string TEXT
int INTEGER
float REAL
*/


class Database
{
	static function encode($variable)
	{//{{{//
		
		$json = json_encode($variable, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if(!is_string($json)) {
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			return(false);
		}
		return($json);
		
	}//}}}//
	static function decode(string $json)
	{//{{{//
	
		$variable = json_decode($json, true);
	 	$error = json_last_error();
		if($variable === NULL && $error !== JSON_ERROR_NONE) {
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			return(NULL);
		}
		return($variable);
		
	}//}}}//
	
	/// Base functions
	
	static $SQLite3 = NULL;
	static $stmt = NULL;
	static function open(string $filename, int $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE)
	{//{{{//
		
		try {
			self::$SQLite3 = new SQLite3($filename, $flags);
		}
		catch(Exception $Exception) {
			if (defined('DEBUG') && DEBUG) var_dump(['path to database file' => $filename]);
			trigger_error($Exception->getMessage(), E_USER_WARNING);
			return(false);
		}

		register_shutdown_function('Database::close');
		
		return(true);
		
	}//}}}//
	static function close()
	{//{{{//
	
		if(!is_object(self::$SQLite3)) {
			return(NULL);
		}
		
		$return = self::$SQLite3->close();
		if(!$return) {
			trigger_error("Can't close `SQLite`", E_USER_WARNING);
			return(false);
		}
		
		self::$SQLite3 = NULL;
		return(true);
		
	}//}}}//
	static function exec(string $query)
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		$return = self::$SQLite3->exec($query);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['sql query' => $query]);
			trigger_error("Exec SQLite3 failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function query(string $query)
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		$SQLite3Result = self::$SQLite3->query($query);
		if(!is_object($SQLite3Result)){
			if (defined('DEBUG') && DEBUG) var_dump(['sql query' => $query]);
			trigger_error("Query SQLite3 failed", E_USER_WARNING);
			return(false);
		}
		
		$result = [];
		while(true) {
			$array = $SQLite3Result->fetchArray(SQLITE3_ASSOC);
			if(!is_array($array)) break;
			array_push($result, $array);
		}
		$SQLite3Result->finalize();
		
		return($result);
		
	}//}}}//
	static function crutch($value)
	{//{{{//
	
		if(!is_string($value)) return($value);
		
		$pattern = '/[^\x20-\xFF\r\n\t]/';
		$value = preg_replace($pattern, '', $value);
		
		return($value);
		
	}//}}}//
	static function stmt()
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		if(!is_object(self::$stmt)) {
			trigger_error("SQLite database statement is not prepared", E_USER_WARNING);
			return(false);
		}
		
		$SQLite3Result = self::$stmt->execute();
		if(!is_object($SQLite3Result)){
			if (defined('DEBUG') && DEBUG) var_dump(['sql query' => self::$stmt->getSQL(true)]);
			trigger_error("Executes a prepared statement failed", E_USER_WARNING);
			return(false);
		}
		
		$result = [];
		
		while(true) {
			$array = $SQLite3Result->fetchArray(SQLITE3_ASSOC);
			if(!is_array($array)) break;
			array_push($result, $array);
		}
		$SQLite3Result->finalize();
		
		return($result);
		
	}//}}}//
	
	/// Escape or prepare data for use in sql query
	
	static function check_table_name(string $string)
	{//{{{//
		
		$pattern = '/^[_a-zA-Z0-9\/]+$/';
		$return = preg_match($pattern, $string);
		if($return != 1) {
			if (defined('DEBUG') && DEBUG) var_dump(['table name' => $string]);
			trigger_error("Table name contains invalid characters", E_USER_WARNING);
			return(false);
		}
		return(true);
		
	}//}}}//
	static function check_column_name(string $string)
	{//{{{//
		
		$pattern = '/^[_a-zA-Z0-9]+$/';
		$return = preg_match($pattern, $string);
		if($return != 1) {
			if (defined('DEBUG') && DEBUG) var_dump(['column name' => $string]);
			trigger_error("Column name contains invalid characters", E_USER_WARNING);
			return(false);
		}
		return(true);
		
	}//}}}//
	static function check_columns_type(string $table, array $data) // [column_name0, ... , column_nameN]
	{//{{{//
		
		if(!self::check_table_name($table)) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT name, type FROM PRAGMA_TABLE_INFO('{$table}');
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$info = self::query($sql);
		if(!is_array($info)) {
			trigger_error("Can't perform select database query", E_USER_WARNING);
			return(false);
		}
		
		$output = [];
		$c = count($info);
		for($i = 0; $i < $c; $i += 1) {
			$column = $info[$i]["name"];
			$type = $info[$i]["type"];
			
			if(!key_exists($column, $data)) continue;
			
			if(
				($type == 'TEXT' && is_string($data[$column]))
				|| ($type == 'INTEGER' && is_int($data[$column]))
				|| ($type == 'REAL' && is_float($data[$column]))
			){
				array_push($output, $column);
				continue;
			}
			
			if (defined('DEBUG') && DEBUG) var_dump([$column => $data[$column]]);
			trigger_error("Incorrect data item value type", E_USER_WARNING);
			return(false);
		}
		
		return($output);
		
	}//}}}//

	/// Tables operations

	static function table_exists(string $table) // bool
	{//{{{//
		
		if(!self::check_table_name($table)) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//		
		$return = self::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform select database query", E_USER_WARNING);
			return(false);
		}
		
		$count = count($return);
		if($count > 0) {
			return(true);
		}
		
		return(false);
		
	}//}}}//
	static function create_table(string $table, array $COLUMN) // bool
	{//{{{// 
	
		// Usage
		/* {{{
		
		$table = '/test';
		$COLUMN = ['
			'text' => '',
			'integer' => 0,
			'real' => 0.0,
		];
		$return = D::create_table($table, $COLUMN, true);
		
		}}} */
		
		if(!self::check_table_name($table)) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$columns = '';		
		foreach($COLUMN as $column => $value) 
		{
			if(!self::check_column_name($column)) {
				trigger_error("Incorrect column name", E_USER_WARNING);
				return(false);
			}
			
			$columns .= ', ';
			
			$type = gettype($value);
			switch($type) 
			{
				case('integer'):
					$columns .= "{$column} INTEGER";
					break;
				case('double'):
					$columns .= "{$column} REAL";
					break;
				case('string'):
					$columns .= "{$column} TEXT";
					break;
				default:
					trigger_error("Unsupported column value type", E_USER_WARNING);
					return(false);
			}
		}
		
		$sql = 
///////////////////////////////////////////////////////////////
<<<HEREDOC
DROP TABLE IF EXISTS '{$table}';
CREATE TABLE '{$table}' (id INTEGER PRIMARY KEY{$columns});
HEREDOC;
///////////////////////////////////////////////////////////////	
		$return = self::exec($sql);
		if(!$return) {
			trigger_error("Can't perform create table database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	/// Data moving
	
	static function insert_item(string $table, array $data) // int id
	{//{{{//
		
		if(!self::check_table_name($table)) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$columns = self::check_columns_type($table, $data);
		if(!is_array($columns)) {
			trigger_error("Incorrect type in data", E_USER_WARNING);
			return(false);
		}
		
		$S = ['', ''];
		foreach($columns as $column) {
			if(strlen($S[0]) > 0) $S[0] .= ', ';
			if(strlen($S[1]) > 1) $S[1] .= ', ';
			$S[0] .= $column;
			$S[1] .= ":{$column}";
		}
		$sql = "INSERT INTO '{$table}' ({$S[0]}) VALUES ({$S[1]});";
		self::$stmt = self::$SQLite3->prepare($sql);
		
		foreach($columns as $column) {
			if(is_string($data[$column])) 
				$type = SQLITE3_TEXT;
			elseif(is_int($data[$column]))
				$type = SQLITE3_INTEGER;
			elseif(is_float($data[$column]))
				$type = SQLITE3_FLOAT;
				
			self::$stmt->bindValue(":{$column}", self::crutch($data[$column]), $type);
		}
		
		$return = self::$stmt->execute();
		if(!is_object($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['sql query' => self::$stmt->getSQL(true)]);
			trigger_error("Can't perform database prepared statement", E_USER_WARNING);
			return(false);
		}
		
		$sql = "SELECT max(id) FROM '{$table}';";
		$array = self::query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		$id = $array[0]["max(id)"];
		
		return($id);
		
	}//}}}//
	static function select_items(string $table, array $where = [], int $limit = 0, int $offset = 0) // array
	{//{{{//
		
		if(!self::check_table_name($table)) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = "SELECT * FROM '{$table}'";
		if(count($where) > 0) {
			$columns = self::check_columns_type($table, $where);
			if(!is_array($columns)) {
				trigger_error("Incorrect type in where", E_USER_WARNING);
				return(false);
			}
			
			$string = '';
			foreach($columns as $column) {
				if(strlen($string) > 0) $string .= ' AND ';
				$string .= "{$column}=:{$column}";
			}
			$string = " WHERE {$string}";
			
			$sql .= $string;
		}
		if($limit > 0) {
			$sql .= " LIMIT {$limit}";
		}
		if($offset > 0) {
			$sql .= " OFFSET {$offset}";
		}
		$sql .= ';';
		self::$stmt = self::$SQLite3->prepare($sql);
		
		foreach($columns as $column) {
			if(is_string($where[$column])) 
				$type = SQLITE3_TEXT;
			elseif(is_int($where[$column]))
				$type = SQLITE3_INTEGER;
			elseif(is_float($where[$column]))
				$type = SQLITE3_FLOAT;
				
			self::$stmt->bindValue(":{$column}", self::crutch($where[$column]), $type);
		}
		
		$array = self::stmt();
		if(!is_array($array)) {
			trigger_error("Can't perform database statement", E_USER_WARNING);
			return(false);
		}
		
		return($array);
		
	}//}}}//

}

