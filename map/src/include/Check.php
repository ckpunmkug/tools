<?php
class Check
{
	static function dir(string $dir_name)
	{//{{{
		$return = file_exists($dir_name);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
			trigger_error("Directory does not exist", E_USER_WARNING);
			return(false);
		}
		
		$return = is_dir($dir_name);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
			trigger_error("The given dirname is not a directory", E_USER_WARNING);
			return(false);
		}
		
		$return = is_readable($dir_name);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
			trigger_error("The directory exists, but it cannot be read", E_USER_WARNING);
			return(false);
		}
		
		$return = is_writable($dir_name);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
			trigger_error("The directory exists but is not writable", E_USER_WARNING);
			return(false);
		}
		
		$return = is_executable($dir_name);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir_name' => $dir_name]);
			trigger_error("The directory exists but cannot be executed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}
	
	static function is_file_exists(string $file_name)
	{//{{{
		if(!file_exists($file_name)) {
			return(false);
		}
		
		if(!is_file($file_name)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file_name' => $file_name]);
			trigger_error("The file exists, but it is not a regular file", E_USER_WARNING);
			return(false);
		}
		
		if(!is_readable($file_name)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file_name' => $file_name]);
			trigger_error("The file exists, but it is not readable", E_USER_WARNING);
			return(false);
		}
		
		if(!is_writable($file_name)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file_name' => $file_name]);
			trigger_error("The file exists, but it is not writable", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}
	
}

