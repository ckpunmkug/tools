<?php
class Data
{
	static $dir_name = NULL;
	
	// Returns the canonicalized absolute path to the directory, otherwise false.
	static function set_dir_name(string $dir_name)
	{//{{{
		$return = Check::dir($dir_name);
		if(!$return) return !user_error("Directory check failed");
		
		$dir_name = realpath($dir_name);
		if(!is_string($dir_name)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
			trigger_error("Cannot get the canonicalized absolute path of a directory", E_USER_WARNING);
			return(false);
		}
		
		self::$dir_name = $dir_name;
		return($dir_name);
	}//}}}	

	static function use_directory(string $dir_name)
	{//{{{
		if(preg_match('/^\/.+$/', $dir_name) != 1) {
			$dir_name = self::$dir_name."/{$dir_name}";
		}
		
		$return = file_exists($dir_name);
		if(!$return) {
			$return = mkdir($dir_name, 0755, true);
			if(!$return) {
				if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
				trigger_error("Can't create directory for using to operate with data", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = Check::dir($dir_name);
		if(!$return) { return(!
			trigger_error("Check directory for using to operate with data failed", E_USER_WARNING)); }
		
		$dir_name = realpath($dir_name);
		if(!is_string($dir_name)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
			trigger_error("Cannot get the canonicalized absolute path of a directory", E_USER_WARNING);
			return(false);
		}
		
		return($dir_name);
	}//}}}

	static function export(string $file_name, $variable)
	{//{{{
		if(preg_match('/^\/.+$/', $file_name) != 1) {
			$file_name = self::$dir_name."/{$file_name}";
		}
		
		$json = json_encode($variable, JSON_PRETTY_PRINT);
		if(!is_string($json)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$variable' => $variable]);
			trigger_error("Can't encode variable to json", E_USER_WARNING);
			return(false);
		}
		
		$return = file_put_contents($file_name, $json);
		if(!is_int($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file_name' => $file_name]);
			trigger_error("Can't put json contents to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}

	static function import(string $file_name)
	{//{{{
		if(preg_match('/^\/.+$/', $file_name) != 1) {
			$file_name = self::$dir_name."/{$file_name}";
		}
		
		$contents = file_get_contents($file_name);
		if(!is_string($contents)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file_name' => $file_name]);
			trigger_error("Can't get json contents from file", E_USER_WARNING);
			return(false);
		}
		
		$variable = json_decode($contents, true);
		return($variable);
	}//}}}

	static function get_files_from_dir(string $dir_name)
	{//{{{
		if(preg_match('/^\/.+$/', $dir_name) != 1) {
			$dir_name = self::$dir_name."/{$dir_name}";
		}
		
		$result = [];
		
		$FILE_NAME = scandir($dir_name, SCANDIR_SORT_ASCENDING);
		if(!is_array($FILE_NAME)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
			trigger_error("Can't list files and directories inside the specified path", E_USER_WARNING);
			return(false);
		}
		
		foreach($FILE_NAME as $file_name) {
			$path = "{$dir_name}/{$file_name}";
			if(!is_file($path)) continue;
			array_push($result, [
				"path" => $path,
				"name" => $file_name,
			]);
		}
		
		return($result);
	}//}}}
	
}
