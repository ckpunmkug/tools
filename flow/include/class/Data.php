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
		
		if($variable === NULL) {
			return(NULL);
		}
		
		$result = strval($variable);
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
		return($array);
		
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
		
		$string = self::$SQLite3->escapeString($string);
		
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

	static function create_table(string $table, array $data)
	{//{{{//
		
		$_ = [];
		
		$_["table"] = self::table($table);
		if(strcmp($_["table"], $table) !== 0) {
			if (defined('DEBUG') && DEBUG) var_dump([
				'$_["table"]' => $_["table"],
				'$table' => $table,
			]);
			trigger_error("Table name have incorrect char(s)", E_USER_WARNING);
			return(false);
		}
		$sql = '';
		
		$sql .= 
			"DROP TABLE IF EXISTS '".$_["table"]."';\n"
			."CREATE TABLE '".$_["table"]."' (\n"
			."\tid INTEGER PRIMARY KEY\n"
		;
		
		foreach($data as $column => $value) {
			$_["column"] = Data::column($column);
			if(strcmp($_["column"], $column) !== 0) {
				if (defined('DEBUG') && DEBUG) var_dump([
					'$_["column"]' => $_["column"],
					$column => $column,
				]);
				trigger_error("Column name have incorrect char(s)", E_USER_WARNING);
				return(false);
			}
			
			$type = gettype($value);
			switch($type) {
				case('integer'):
					$sql .= "\t,{$_['column']} INTEGER";
					if(!empty($value)) $sql .= ' DEFAULT 0';
					break;
				case('double'):
					$sql .= "\t,{$_['column']} REAL";
					if(!empty($value)) $sql .= ' DEFAULT 0.0';
					break;
				case('string'):
					$sql .= "\t,{$_['column']} TEXT";
					if(!empty($value)) $sql .= " DEFAULT ''";
					break;
				default:
					if (defined('DEBUG') && DEBUG) var_dump(['$type' => $type]);
					trigger_error("Unsupported value type", E_USER_WARNING);
					return(false);
			}
			$sql .= "\n";
		}
		
		$sql .= ");";
		
		$return = self::exec($sql);
		if(!$return) {
			trigger_error("Can't perform create table query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	static function select_items(string $table, array $where = [], int $limit = -1, int $offset = -1)
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
		
		if(!empty($where)) {//
			$sql .= "\n\tWHERE";
			$string = '';
			
			foreach($where as $column => $value) {//
				if(!empty($string)) {
					$string .= ' AND';
				}
				$_["column"] = Data::column($column);
				$type = gettype($value);
				
				switch($type) {//
					case('integer'):
						$string .= " {$_['column']}=".self::integer($value);
						break;
					case('double'):
						$string .= " {$_['column']}=".self::real($value);
						break;
					case('string'):
						$string .= " {$_['column']}='".self::text($value)."'";
						break;
					default:
						if (defined('DEBUG') && DEBUG) var_dump(['$type' => $type]);
						trigger_error("Unsupported value type", E_USER_WARNING);
						return(false);
				}// switch($type)
			}// foreach($where as $column => $value)
			
			$sql .= $string;
			
		}// if(!empty($where))
		
		if($limit > 0) {
			$_["limit"] = self::integer($limit);
			$sql .= "\n\tLIMIT {$_["limit"]}";
		}
		
		if($offset >= 0) {
			$_["offset"] = self::integer($offset);
			$sql .= "\n\tOFFSET {$_["offset"]}";
		}
		
		$sql .= ';';
		
		$return = self::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform select query", E_USER_WARNING);
			return(false);
		}
		$result = $return;
		
		if(empty($result)) {
			return(NULL);
		}
		
		return($result);
		
	}//}}}//
	static function insert_item(string $table, array $item)
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
		foreach($item as $column => $value) {//
			
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
	static function get_count(string $table, array $where = [])
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
		
		$count = count($where);
		if($count > 0) {//
			$string = '';
			foreach($where as $column => $value) {//
				$return = is_string($column);
				if(!$return) {
					trigger_error("'column' is not string in 'where' array", E_USER_WARNING);
					return(false);
				}
				$_["column"] = self::column($column);
				
				$length = strlen($string);
				if($length > 0) {
					$string .= ' AND';
				}
				
				$type = gettype($value);
				switch($type) {//
					case('integer'):
						$string .= " {$_['column']}=".self::integer($value);
						break;
					case('double'):
						$string .= " {$_['column']}=".self::real($value);
						break;
					case('string'):
						$string .= " {$_['column']}='".self::text($value)."'";
						break;
					default:
						if (defined('DEBUG') && DEBUG) var_dump(['$type' => $type]);
						trigger_error("Unsupported value type", E_USER_WARNING);
						return(false);
				}// switch($type)
			}// foreach($where as $column => $value)
			
			$sql .= "\n\tWHERE{$string};";
			
		}// if($count > 0)
		
		$return = self::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform 'select count' database query", E_USER_WARNING);
			return(false);
		}
		$result = $return[0]["COUNT(*)"];
		
		return($result);
		
	}//}}}//
}

