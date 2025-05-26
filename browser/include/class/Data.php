<?php

/*
id INTEGER PRIMARY KEY
string TEXT
int INTEGER
float REAL
*/


class Data
{

	static function json_encode($variable)
	{//{{{//
		
		$json = json_encode($variable, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if(!is_string($json)) {
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			return(false);
		}
		return($json);
		
	}//}}}//
	static function json_decode(string $json)
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
	
	static $SQLite3 = NULL;
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

		register_shutdown_function('Data::close');
		
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
			if (defined('DEBUG') && DEBUG) var_dump(['$query' => $query]);
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
			if (defined('DEBUG') && DEBUG) var_dump(['table name' => $string]);
			trigger_error("Column name contains invalid characters", E_USER_WARNING);
			return(false);
		}
		return(true);
		
	}//}}}//
	static function text(string $string)
	{//{{{//
		
		$string = base64_encode($string);
		return($string);
		
	}//}}}//
	static function integer($number)
	{//{{{//
		
		$number = intval($number);
		$number = strval($number);
		
		return($number);
		
	}//}}}//
	static function real($number)
	{//{{{//
		
		$number = floatval($number);
		$number = strval($number);
		
		return($number);
		
	}//}}}//

	static function table_exists(string $table)
	{//{{{//
		
		$return = self::check_table_name($table);
		if(!$return) {
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
			trigger_error("Database query for find table by name failed", E_USER_ERROR);
			exit(255);
		}
		
		$count = count($return);
		if($count > 0) {
			return(true);
		}
		
		return(false);
		
	}//}}}//
	static function create_table(string $table, array $COLUMN, bool $add_auto_increment_id = false) // bool
	{//{{{// 
	
		// Usage
		/* {{{
		
		$table = '/test';
		$COLUMN = ['
			// columns with default zero value
			'text0' => '',
			'integer0' => 0,
			'real0' => 0.0,
			// columns without default value
			'text1' => '1',
			'integer1' => 1,
			'real1' => 1.0,
		];
		$return = Data::create_table($table, $COLUMN, true);
		
		 }}} */
		
		$return = self::check_table_name($table);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$columns = '';
		if($add_auto_increment_id && !key_exists('id', $COLUMN)) {
			$columns = "id INTEGER PRIMARY KEY";
		}
		
		foreach($COLUMN as $column => $value) {
			
			$return = self::check_column_name($column);
			if(!$return) {
				trigger_error("Incorrect column name", E_USER_WARNING);
				return(false);
			}
			
			if(strlen($columns) > 0) $columns .= ', ';
			
			$type = gettype($value);
			switch($type) {
			
				case('integer'):
					$columns .= "{$column} INTEGER";
					if($value === 0) $columns .= ' DEFAULT 0';
					break;
					
				case('double'):
					$columns .= "{$column} REAL";
					if($value === 0.0) $columns .= ' DEFAULT 0.0';
					break;
					
				case('string'):
					$columns .= "{$column} TEXT";
					if($value === '') $columns .= " DEFAULT ''";
					break;
					
				default:
					if (defined('DEBUG') && DEBUG) var_dump(['$type' => $type]);
					trigger_error("Unsupported column value type", E_USER_WARNING);
					return(false);
					
			} //switch($type)
			
		} //foreach($columns as $column => $value)
		
		$sql = 
///////////////////////////////////////////////////////////////
<<<HEREDOC
DROP TABLE IF EXISTS '{$table}';
CREATE TABLE '{$table}' ({$columns});
HEREDOC;
///////////////////////////////////////////////////////////////
		if(defined('TESTING')) var_dump($sql);
		
		$return = self::exec($sql);
		if(!$return) {
			trigger_error("Can't perform create table query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function insert_item(string $table, array $data)
	{//{{{//
	
		$return = self::check_table_name($table);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$table}'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$columns = '';
		$values = '';
		foreach($data as $column => $value) {
			
			if(strlen($columns) > 0) $columns .= ', ';
			if(strlen($values) > 0) $values .= ', ';
					
			$return = self::check_column_name($column);
			if(!$return) {
				trigger_error("Incorrect column name", E_USER_WARNING);
				return(false);
			}
			$columns .= $column;
			
			$type = gettype($value);
			switch($type) {
			
				case('integer'):
					$values .= self::integer($value);
					break;
					
				case('double'):
					$values .= self::real($value);
					break;
					
				case('string'):
					$values .= "'".self::text($value)."'";
					break;
					
				default:
					if (defined('DEBUG') && DEBUG) var_dump(['$type' => $type]);
					trigger_error("Unsupported value type", E_USER_WARNING);
					return(false);
					
			} //switch($type)
		} //foreach($where as $column => $value)
		
		$sql .= " ({$columns}) VALUES ({$values});";
		if(defined('TESTING')) var_dump($sql);
	
		$return = self::exec($sql);
		if(!$return) {
			trigger_error("Can't perform database insert query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function update_item(string $table, array $data, int $id)
	{//{{{//
	
		$return = self::check_table_name($table);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE '{$table}' SET 
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$string = '';
		foreach($data as $column => $value) {
		
			$strlen = strlen($string);
			if($strlen > 0) $string .= ', ';
			
			$return = self::check_column_name($column);
			if(!$return) {
				trigger_error("Incorrect column name", E_USER_WARNING);
				return(false);
			}
			$string .= "{$column}=";
			
			$type = gettype($value);
			switch($type) {
			
				case('integer'):
					$string .= self::integer($value);
					break;
					
				case('double'):
					$string .= self::real($value);
					break;
					
				case('string'):
					$string .= "'".self::text($value)."'";
					break;
					
				default:
					if (defined('DEBUG') && DEBUG) var_dump(['$type' => $type]);
					trigger_error("Unsupported value type", E_USER_WARNING);
					return(false);
					
			} //switch($type)
		} //foreach($where as $column => $value)
		
		$sql .= $string;
		
		$id = self::integer($id);
		$sql .= " WHERE id={$id}";
		
		$sql .= ';';
		if(defined('TESTING')) var_dump($sql);
		
		$return = self::exec($sql);
		if(!$return) {
			trigger_error("Can't perform database update query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function select_item(string $table, string $where = '', int $offset = 0) // array
	{//{{{//
		
		$return = self::check_table_name($table);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$strlen = strlen($where);
		if($strlen > 0) $sql .= " WHERE {$where}";
		
		$sql .= " LIMIT 1";
		
		$offset = self::integer($offset);
		$sql .= " OFFSET {$offset}";
		
		$sql .= ';';
		if(defined('TESTING')) var_dump($sql);
		
		$return = self::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform select query", E_USER_WARNING);
			return(false);
		}
		$result = $return;
		
		if(count($result) == 0) {
			return(NULL);
		}
		$result = $result[0];
		
		foreach($result as $i => $column) {
				if(is_string($column)) $result[$i] = base64_decode($column);
		}
	
		return($result);
		
	}//}}}//
	static function select_items(string $table, array $COLUMN = [], string $where = '')
	{//{{{//
			
		$return = self::check_table_name($table);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$return = count($COLUMN);
		if($return > 0) $columns = '';
		else $columns = '*';

		foreach($COLUMN as $column) {
					
			$return = self::check_column_name($column);
			if(!$return) {
				trigger_error("Incorrect column name", E_USER_WARNING);
				return(false);
			}
			
			if(strlen($columns) > 0) $columns .= ', ';
			
			$columns .= $column;
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT $columns FROM '{$table}'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
		$return = strlen($where);
		if($return > 0) $sql .= " WHERE {$where}";
		
		$sql .= ';';
		if(defined('TESTING')) var_dump($sql);
	
		$result = Data::query($sql);
		if(!is_array($result)) {
			trigger_error("Can't perform select items query", E_USER_WARNING);
			return(false);
		}
		
		foreach($result as $i => $item) {
			foreach($item as $j => $column) {
				if(is_string($column)) $result[$i][$j] = base64_decode($column);
			}
		}
	
		return($result);
		
	}//}}}//
	static function delete_items(string $table, string $where = '1')
	{//{{{//
			
		$return = self::check_table_name($table);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM '{$table}'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
		$return = strlen($where);
		if($return > 0) $sql .= " WHERE {$where}";
		
		$sql .= ';';
		if(defined('TESTING')) var_dump($sql);
	
		$result = Data::exec($sql);
		if(!$result) {
			trigger_error("Can't perform delete items query", E_USER_WARNING);
			return(false);
		}
	
		return($result);
		
	}//}}}//
	static function select_count(string $table, string $where = '')
	{//{{{//
	
		$return = self::check_table_name($table);
		if(!$return) {
			trigger_error("Incorrect table name", E_USER_WARNING);
			return(false);
		}
		
		$sql =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(*) FROM '{$table}'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$strlen = strlen($where);
		if($strlen > 0) $sql .= " WHERE {$where}";
		
		$sql .= ';';
		if(defined('TESTING')) var_dump($sql);
		
		$return = self::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform select count query", E_USER_WARNING);
			return(false);
		}
		$result = $return[0]["COUNT(*)"];
		
		return($result);
		
	}//}}}//

}

