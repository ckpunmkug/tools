<?php

class Action
{
	static function setup()
	{//{{{//
		
		$log = '';
		
		if(file_exists(PATH["database"])) goto label_open_database; ////
		
		$return = dirname(PATH["database"]);
		if(!is_string($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['PATH["database"]' => PATH["database"]]);
			trigger_error("Can't get basename from 'database' path", E_USER_WARNING);
			return(false);
		}
		$path = $return;
		
		if(file_exists($path)) goto label_check_dir; ///////////////////
		
		$return = mkdir($path, 0755, true);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't create directory for database file", E_USER_WARNING);
			return(false);
		}
		$log .= "Directory created\n";
		goto label_open_database;
		
		label_check_dir: ///////////////////////////////////////////////
		
		$return = FileSystem::is_dir_rwx($path, true, true, true, false);
		if(!$return) {
			trigger_error("Incorrect directory for database file", E_USER_WARNING);
			return(false);
		}
		$log .= "Existing directory checked\n";
		
		label_open_database: ///////////////////////////////////////////
		
		if(Database::$SQLite3 !== NULL) goto label_create_tables;
		
		$return = Database::open(PATH["database"]);
		if(!$return) {
			trigger_error("Can't open database from file", E_USER_ERROR);
			exit(255);
		}
		$log .= "Database opened\n";
		
		label_create_tables: ///////////////////////////////////////////
		
		$return = Method::create_tables();
		if(!$return) {
			trigger_error("Can't create tables", E_USER_WARNING);
			return(false);
		}
		$log .= "Tables created\n";
		
		echo($log); ////////////////////////////////////////////////////
		
		return(true);
		
	}//}}}//
	
	static function search()
	{//{{{//
		
		if(!eval(Check::$string.='$_SERVER["HTTP_REFERER"]')) return(false);
		$http_referer = $_SERVER["HTTP_REFERER"];
		
		if(!eval(Check::$string.='$_POST["path"]')) return(false);
		$path = $_POST["path"];
		
		if(!eval(Check::$string.='$_POST["filter"]')) return(false);
		$filter = $_POST["filter"];
		
		if(!eval(Check::$string.='$_POST["pattern"]')) return(false);
		$pattern = $_POST["pattern"];
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$parent = intval($_POST["id"]);
		
		$return = FileSystem::get_dir_contents($path);
		if(!is_array($return)) {
			trigger_error("Can't get dir contents", E_USER_WARNING);
			return(false);
		}
		$PATH = $return;
		
		$return = Method::apply_path_filter($PATH, $filter);
		if(!is_array($return)) {
			trigger_error("Can't apply filter to pathes list", E_USER_WARNING);
			return(false);
		}
		$PATH = $return;
		
		$return = Method::find_lines($PATH, $pattern);
		if(!is_array($return)) {
			trigger_error("Can't find lines in files", E_USER_WARNING);
			return(false);
		}
		$SEARCH_RESULT = $return;
			
		$query = data::$add["search_query"]($parent, $pattern);
		if(!is_int($query)) {
			trigger_error("Can't add 'search_query'", E_USER_WARNING);
			return(false);
		}
		
		$return = data::$add["SEARCH_RESULT"]($query, $SEARCH_RESULT);
		if(!$return) {
			trigger_error("Can't add 'SEARCH_RESULT'", E_USER_WARNING);
			return(false);
		}
	
		header("Location: {$http_referer}");
		
		return(true);
		
	}//}}}//
	
	static function delete_query()
	{//{{{//
		
		if(!eval(Check::$string.='$_SERVER["HTTP_REFERER"]')) return(false);
		$http_referer = $_SERVER["HTTP_REFERER"];
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		$return = data::$delete["search_query"]($id);
		if(!$return) {
			trigger_error("Can't delete 'search_query'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: {$http_referer}");
		
		return(true);
		
	}//}}}//

	static function delete_results()
	{//{{{//
		
		if(!eval(Check::$string.='$_SERVER["HTTP_REFERER"]')) return(false);
		$http_referer = $_SERVER["HTTP_REFERER"];
		
		if(!eval(Check::$array.='$_POST["ID"]')) return(false);
		$ID = $_POST["ID"];
		
		foreach($ID as $key => $id) {
			if(!eval(Check::$string.='$id')) return(false);
			$ID[$key] = intval($id);
		}
		
		$return = data::$delete["search_results"]($ID);
		if(!$return) {
			trigger_error("Can't delete 'search_results'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: {$http_referer}");
		
		return(true);
		
	}//}}}//
}

