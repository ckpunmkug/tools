<?php

class Action
{
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
		
		$return = Extension::apply_path_filter($PATH, $filter);
		if(!is_array($return)) {
			trigger_error("Can't apply filter to pathes list", E_USER_WARNING);
			return(false);
		}
		$PATH = $return;
		
		$return = Extension::find_lines($PATH, $pattern);
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

