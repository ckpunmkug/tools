<?php

class Data
{
	static function get_bool($variable = NULL)
	{//{{{//
		
		if($variable === NULL) {
			return(NULL);
		}
		
		$result = boolval($variable);
		return($result);
		
	}//}}}//
	static function get_int($variable = NULL)
	{//{{{//
		
		if($variable === NULL) {
			return(NULL);
		}
		
		$result = intval($variable);
		return($result);
		
	}//}}}//
	static function get_float($variable = NULL)
	{//{{{//
		
		if($variable === NULL) {
			return(NULL);
		}
		
		$result = floatval($variable);
		return($result);
		
	}//}}}//
	static function get_string($variable = NULL)
	{//{{{//
		
		if($variable === NULL || is_string($variable) == false) {
			return(NULL);
		}
		
		$result = $variable;
		return($result);
		
	}//}}}//
	static function get_array($variable = NULL)
	{//{{{//
		
		if($variable === NULL || is_array($variable) == false) {
			return(NULL);
		}
		
		$result = $variable;
		return($result); 
		
	}//}}}//

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
	static function export(string $filename, $variable)
	{//{{{//
	
		$contents = self::encode($variable);
		if(!is_string($contents)) {
			trigger_error("Can't encode variable to json", E_USER_WARNING);
			return(false);
		}
		
		$return = file_put_contents($filename, $contents);
		if(!is_int($return)) {
			trigger_error("Can't put json contents to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//	
	static function import(string $filename)
	{//{{{//
		
		$contents = file_get_contents($filename);
		if(!is_string($contents)) {
			trigger_error("Can't get json contents from file", E_USER_WARNING);
			return(false);
		}
		
		$variable = self::decode($contents);
		if($variable === NULL) {
			trigger_error("Can't decode json contents", E_USER_WARNING);
			return(false);
		}
		
		return($variable);
		
	}//}}}//
	static function save(string $filename, array $array)
	{//{{{//
		
		$contents = '';
		foreach($array as $string) {
			$string = self::get_string($string);
			if(!empty($contents)) $contents .= "\n";
			$contents .= "{$string}";
		}
		
		$return = file_put_contents($filename, $contents);
		if(!is_int($return)) {
			trigger_error("Can't put merged contents to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function load(string $filename)
	{//{{{//
		
		$contents = file_get_contents($filename);
		if(!is_string($contents)) {
			trigger_error("Can't get merged contents from file", E_USER_WARNING);
			return(false);
		}
		
		$array = explode("\n", $contents);
		
		$result = [];
		foreach($array as $string) {
			$string = trim($string);
			$strlen = strlen($string);
			if($strlen > 0) {
				array_push($result, $string);
			}
		}
		
		return($result);
		
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
	
	static function table(string $string)
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		$pattern = '([^\x20-\xFF])';
		$string = preg_replace($pattern, '', $string);
		
		$string = self::$SQLite3->escapeString($string);
		
		return($string);
		
	}//}}}//
	static function column(string $string)
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		$pattern = '([^\_a-zA-Z0-9])';
		$string = preg_replace($pattern, '', $string);
		
		$string = self::$SQLite3->escapeString($string);
		
		return($string);
		
	}//}}}//
	static function text(string $string)
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		$pattern = '([^\x09\x0D\x0A\x20-\xFF])';
		$string = preg_replace($pattern, '', $string);
		
		$pattern = '([\'])';
		$string = preg_replace($pattern, "'||char(39)||'", $string);
		
		$pattern = '([\\\\])';
		$string = preg_replace($pattern, "'||char(92)||'", $string);
		
		//$string = self::$SQLite3->escapeString($string);
		
		return($string);
		
	}//}}}//
	static function integer($number)
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		$number = intval($number);
		$number = strval($number);
		
		return($number);
		
	}//}}}//
	static function real($number)
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		$number = floatval($number);
		$number = strval($number);
		
		return($number);
		
	}//}}}//

	static function table_exists(string $table)
	{//{{{//
		
		$_ = [
			'table' => self::table($table),
		];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT name FROM sqlite_master WHERE type='table' AND name='{$_["table"]}';
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
	static function create_table(string $table, array $columns, bool $id = true) // bool
	{//{{{// 
	
		// Usage
		/* {{{
		$table = '/test';
		$columns = ['
			// columns without default values
			'text0' => '', // strval(false)
			'integer0' => 0, // intval(false)
			'real0' => 0.0,
			// columns with zero default value
			'text1' => '1', // strval(true)
			'integer1' => 1, // intval(true)
			'real1' => 1.0, // floatval(true)
		];
		$return = Data::create_table($table, $columns);
		if($return === false) return(!user_error('Returned FALSE'));
		
DROP TABLE IF EXISTS '/test';
CREATE TABLE '/test' (
        id INTEGER PRIMARY KEY,
        text0 TEXT,
        integer0 INTEGER,
        real0 REAL,
        text1 TEXT DEFAULT '',
        integer1 INTEGER DEFAULT 0,
        real1 REAL DEFAULT 0.0
);
		 }}} */
		
		$_ = [];
		$sql = '';
		
		if($id) {
			$_["columns"] = "\n\tid INTEGER PRIMARY KEY";
		}
		else {
			$_["columns"] = "";
		}
		
		foreach($columns as $column => $value) {//
			
			if(strlen($_["columns"]) > 0) {
				$_["columns"] .= ',';
			}
			
			$_["column"] = Data::column($column);
			
			$type = gettype($value);
			switch($type) {//
				case('integer'):
					$_["columns"] .= "\n\t{$_['column']} INTEGER";
					if(boolval($value)) $_["columns"] .= ' DEFAULT 0';
					break;
				case('double'):
					$_["columns"] .= "\n\t{$_['column']} REAL";
					if(boolval($value)) $_["columns"] .= ' DEFAULT 0.0';
					break;
				case('string'):
					$_["columns"] .= "\n\t{$_['column']} TEXT";
					if(boolval($value)) $_["columns"] .= " DEFAULT ''";
					break;
				default:
					if (defined('DEBUG') && DEBUG) var_dump(['$type' => $type]);
					trigger_error("Unsupported column value type", E_USER_WARNING);
					return(false);
			}// switch($type)
			
		}// foreach($columns as $column => $value)
		$_["columns"] .= "\n";
		
		$_["table"] = self::table($table);
		$sql .= 
///////////////////////////////////////////////////////////////
<<<HEREDOC
DROP TABLE IF EXISTS '{$_["table"]}';
CREATE TABLE '{$_["table"]}' ({$_["columns"]});
HEREDOC;
///////////////////////////////////////////////////////////////
		
		$return = self::exec($sql);
		if(!$return) {
			trigger_error("Can't perform create table query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function select_item(string $table, string $where = '', int $offset = 0) // array
	{//{{{//
	
		$sql = '';
		$_ = [
			"table" => self::table($table),
		];
		
		$sql .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$_["table"]}'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$strlen = strlen($where);
		if($strlen > 0) {
			$sql .= "\n\tWHERE {$where}";
		}
		
		$sql .= "\n\tLIMIT 1";
		
		$_["offset"] = self::integer($offset);
		$sql .= "\n\tOFFSET {$_["offset"]}";
		
		$sql .= ';';
		
		$return = self::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform select query", E_USER_WARNING);
			return(false);
		}
		$result = $return;
		
		if(count($result) == 0) {
			return(NULL);
		}
		
		return($result[0]);
		
	}//}}}//
	static function insert_item(string $table, array $data)
	{//{{{//
	
		$sql = '';
		$_ = [
			"table" => self::table($table),
		];
		
		$sql .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$_["table"]}'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$columns = '';
		$values = '';
		foreach($data as $column => $value) {//
			
			if(strlen($columns) !== 0) {
				$columns .= ', ';
			}
			if(strlen($values) !== 0) {
				$values .= ', ';
			}
			
			$_["column"] = Data::column($column);
			$columns .= $_["column"];
			
			$type = gettype($value);
			switch($type) {//
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
			}// switch($type)
		}// foreach($where as $column => $value)
		
		$sql .= " (\n\t{$columns}\n) VALUES (\n\t{$values}\n);";
		
		$return = self::exec($sql);
		if(!$return) {
			trigger_error("Can't perform database insert query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function update_item(string $table, array $data, int $id)
	{//{{{//
	
		$sql = '';
		$_ = [
			"table" => self::table($table),
		];
		
		$sql .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE '{$_["table"]}' SET
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$string = '';
		foreach($data as $column => $value) {//
			$strlen = strlen($string);
			if($strlen > 0) {
				$string .= ',';
			}
			$string .= "\n\t";
			
			$_["column"] = Data::column($column);
			$string .= $_["column"].'=';
			
			$type = gettype($value);
			switch($type) {//
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
			}// switch($type)
		}// foreach($where as $column => $value)
		
		$sql .= $string;
		
		$_["id"] = self::integer($id);
		$sql .= "\n\tWHERE id={$_['id']}";
		
		$sql .= ';';
		
		$return = self::exec($sql);
		if(!$return) {
			trigger_error("Can't perform database update query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function select_count(string $table, string $where = '')
	{//{{{//
	
		$_ = [];
		$sql = '';
		
		$_["table"] = self::table($table);
		$sql .=
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(*) FROM '{$_["table"]}'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$strlen = strlen($where);
		if($strlen > 0) {
			$sql .= "\n\tWHERE {$where};";
		}
		else {
			$sql .= ';';
		}
		
		$return = self::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform select count query", E_USER_WARNING);
			return(false);
		}
		$result = $return[0]["COUNT(*)"];
		
		return($result);
		
	}//}}}//
}

