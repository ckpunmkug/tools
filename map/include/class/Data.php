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
	
	static function escape(string $string)
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
	
	static function name(string $string)
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
	
	static function exec(string $query)
	{//{{{//
		
		if(!is_object(self::$SQLite3)) {
			trigger_error("SQLite database is not open", E_USER_WARNING);
			return(false);
		}
		
		$return = self::$SQLite3->exec($query);
		if(!$return) {
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
}

