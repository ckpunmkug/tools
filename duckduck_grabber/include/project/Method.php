<?php

class Method
{
	static function setup_database()
	{//{{{//
		
		$return = Method::is_database_dir_exists();
		if(!$return) {
			$return = Method::create_database_dir();
			if(!$return) {
				trigger_error("Can't create 'database' dir", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = Method::is_database_file_exists();
		if(!$return) {
			$return = Method::create_database_file();
			if(!$return) {
				trigger_error("Can't create 'database' file", E_USER_WARNING);
				return(false);
			}
		}
		
		if(!is_object(Database::$SQLite3)) {
			$return = Database::open(PATH["database"]);
			if(!$return) {
				trigger_error("Can't open 'database' file", E_USER_WARNING);
				return(false);
			}
		}
	
		$return = Method::create_tables_if_not_exists();
		if(!$return) {
			trigger_error("Can't create tables in database", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function is_database_dir_exists()
	{//{{{//
		
		$path = dirname(PATH["database"]);
		
		$return = file_exists($path);
		if(!$return) return(false);
		
		$return = FileSystem::is_dir_rwx($path, true, true, true, false);
		if(!$return) {
			trigger_error("Incorrect 'database' path", E_USER_ERROR);
			exit(255);
		}
		
		return(true);
		
	}//}}}//

	static function create_database_dir()
	{//{{{//
		
		$path = dirname(PATH["database"]);
		
		$return = mkdir($path, 0755, true);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't create 'database' dir", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function is_database_file_exists()
	{//{{{//
		
		$path = PATH["database"];
		
		$return = file_exists($path);
		if(!$return) return(false);
		
		$return = FileSystem::is_file_rwx($path, true, true, false, false);
		if(!$return) {
			trigger_error("Incorrect 'database' file", E_USER_ERROR);
			exit(255);
		}
		
		return(true);
		
	}//}}}//

	static function create_database_file()
	{//{{{//
		
		$path = PATH["database"];
		
		$return = Database::open($path);
		if(!$return) {
			trigger_error("Can't open 'database' file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function create_tables_if_not_exists()
	{//{{{//
		
		foreach(data::$create as $key => $method) {
			$return = data::$create[$key]();
			if(!$return) {
				trigger_error("Can't create '{$key}' table", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
		
	}//}}}//
}

