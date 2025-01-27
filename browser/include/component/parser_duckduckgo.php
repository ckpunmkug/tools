<?php

class parser
{
	
	static function action(string $name)
	{//{{{//
		
		switch($name) {
			case("next"):
				echo("xa-xa-xa");
				return(true);
			case("create_database"):
				$return = self::create_database(CONFIG["parser"]["database_file"]);
				if(!$return) {
					trigger_error("Can't create database", E_USER_WARNING);
					return(false);
				}
				return(true);
			default:
				trigger_error("Unsupported action on `parser`", E_USER_WARNING);
				return(false);
		}
		
	}//}}}//

	static function start(string $action)
	{//{{{//
		
		switch($action) {
			case('database_create'): 
				$return = self::database_create(self::$database_path);
				if($return !== true) {
					trigger_error("Action `database_create` failed", E_USER_WARNING);
					return(false);
				}
				return(true);
			default:
				if (defined('DEBUG') && DEBUG) var_dump(['$action' => $action]);
				trigger_error("Unsupported action", E_USER_WARNING);
				return(false);
		}
		
	}//}}}//
	
	static $database_path = '';
	
	static function database(string $path)
	{//{{{//
		
		$return = file_put_contents($path, '');
		if(!is_int($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't create empty file for database", E_USER_WARNING);
			return(false);
		}
		$filename = realpath($path);
		
		try {
			$SQLite3 = new SQLite3($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
		}
		catch(Exception $Exception) {
			trigger_error($Exception->getMessage(), E_USER_WARNING);
			return(false);
		}
		
		$first_query = @strval($_POST["first_query"]);
		if(empty($first_query)) {
			if (defined('DEBUG') && DEBUG) @var_dump(['$_POST["first_query"]' => $_POST["first_query"]]);
			trigger_error("Incorrect string `first_query` incoming parameter", E_USER_WARNING);
			return(false);
		}
		
		$_ = [
			"first_query" => $SQLite->
		];
		
		$sql =
		
		return(true);
		
	}//}}}//
	
	
}

