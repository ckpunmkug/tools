<?php

class Main
{
	function __construct()
	{//{{{//
		
		$return = Main::create_folder(PATH["database"]);
		if(!$return) {
			trigger_error("Can't create 'data' folder", E_USER_ERROR);
			exit(255);
		}
		
		$return = Main::create_database(PATH["database"]);
		if(!$return) {
			trigger_error("Can't create 'data' file", E_USER_ERROR);
			exit(255);
		}
		
		$return = Main::create_tables();
		if(!$return) {
			trigger_error("Can't create tables", E_USER_ERROR);
			exit(255);
		}
		
	}//}}}//
	
	static function create_folder(string $path)
	{//{{{//
		
		$return = dirname($path);
		if(!is_string($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't get dirname from path", E_USER_WARNING);
			return(false);
		}
		$path = $return;
		
		$return = file_exists($path);
		if(!$return) {
			$return = mkdir($path, 0755, true);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
				trigger_error("Can't create folder for path", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = FileSystem::is_dir_rwx($path, true, true, true);
		if(!$return) {
			trigger_error("Incorrect directory path", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function create_database(string $path)
	{//{{{//
		
		$return = Database::open($path);
		if(!$return) {
			trigger_error("Can't open database file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function create_tables()
	{//{{{//
		
		$return = data::$drop["SEARCH_QUERY"]();
		if(!$return) {
			trigger_error("Can't drop 'SEARCH_QUERY'", E_USER_WARNING);
			return(false);
		}
		
		$return = data::$create["SEARCH_QUERY"]();
		if(!$return) {
			trigger_error("Can't drop 'SEARCH_QUERY'", E_USER_WARNING);
			return(false);
		}
		
		$return = data::$drop["SEARCH_RESULTS"]();
		if(!$return) {
			trigger_error("Can't drop 'SEARCH_RESULTS'", E_USER_WARNING);
			return(false);
		}
		
		$return = data::$create["SEARCH_RESULTS"]();
		if(!$return) {
			trigger_error("Can't drop 'SEARCH_RESULTS'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
}

