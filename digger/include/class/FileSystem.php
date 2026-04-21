<?php

class FileSystem
{
	static $enable_notice = false; // display a notification if the directory cannot be read
	static $max_count_files = 0xFFFF; // maximum number of files in the list
	static $count_files = 0;

	static function find_to_list(string $dir_path)
	{//{{{//
		
		if($dir_path != '/') {
			$dir_path = rtrim($dir_path, '/');
		}
		
		$return = is_dir($dir_path);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$dir_path' => $dir_path]);
			trigger_error("Passed path is not directory", E_USER_WARNING);
			return(false);
		}
		
		$return = is_readable($dir_path);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$dir_path' => $dir_path]);
			trigger_error("Passed directory is not readable", E_USER_WARNING);
			return(false);			
		}
		
		$scandir = function($directory_path) {
		
			$return = @scandir($directory_path);
			if(!is_array($return)) {
				if(self::$enable_notice) {
					if(defined('DEBUG') && DEBUG) var_dump(['$directory_path' => $directory_path]);
					trigger_error("Can't scan directory", E_USER_WARNING);
				}
				return(false);
			}
			$NAME = $return;
			
			$FILE = [];
			$LINK = [];
			$DIR = [];
			$OTHER = [];
			
			foreach($NAME as $name) {
				
				if($name == '.' || $name == '..') continue;
				
				$path = "{$directory_path}/{$name}";
				if(is_link($path)) {
					array_push($LINK, $path);
				}
				elseif(is_dir($path)) {
					array_push($DIR, $path);
				}
				elseif(is_file($path)) {
					array_push($FILE, $path);
				}
				else {
					array_push($OTHER, $path);
				}
				
			}// foreach($NAME as $name)
			
			asort($FILE);
			asort($LINK);
			asort($DIR);
			asort($OTHER);
			
			$result = array_merge($FILE, $LINK, $DIR, $OTHER);
		
			return($result);
			
		};// $scandir = function($directory_path)
		
		$files = $scandir($dir_path);
		$count = count($files);
		for($index = 0; $index < $count; $index += 1) {
		
			$count = count($files);
			if($count > self::$max_count_files) break;
			
			$current_path = $files[$index];
			
			if(is_link($current_path)) continue;
			
			if(is_dir($current_path)) {
				$begin = array_slice($files, 0, $index+1);				
				$end = array_slice($files, $index+1);				
				
				$return = $scandir($current_path);
				if(!is_array($return)) continue;
				
				$files = array_merge($begin, $return, $end);
			}
			
		}// for($index = 0; $index < count($directory_path); $index++)
		
		return($files);
		
	}//}}}//
	
	static function is_directory_rwx(string $directory_path, bool $test_readable = true, bool $test_writable = true, bool $test_executable = true)
	{//{{{//
		
		$return = file_exists($directory_path);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$directory_path' => $directory_path]);
			trigger_error("Directory not exists", E_USER_WARNING);
			return(false);
		}
		
		$return = is_link($directory_path);
		if($return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$directory_path' => $directory_path]);
			trigger_error("Directory is link", E_USER_WARNING);
			return(false);
		}
		
		$return = is_dir($directory_path);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$directory_path' => $directory_path]);
			trigger_error("Path to file is not directory", E_USER_WARNING);
			return(false);
		}
		
		if($test_readable) {
			$return = is_readable($directory_path); 
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$directory_path' => $directory_path]);
				trigger_error("Directory is not readable", E_USER_WARNING);
				return(false);
			}
		}
		
		if($test_writable) {
			$return = is_writable($directory_path);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$directory_path' => $directory_path]);
				trigger_error("Directory is not writable", E_USER_WARNING);
				return(false);
			}
		}
		
		if($test_executable) {
			$return = is_executable($directory_path);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$directory_path' => $directory_path]);
				trigger_error("Directory is not executable", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
		
	}//}}}//
	
	static function is_file_rwx(string $file_path, bool $test_readable = true, bool $test_writable = true, bool $test_executable = true)
	{//{{{//
		
		$return = file_exists($file_path);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
			trigger_error("File not exists", E_USER_WARNING);
			return(false);
		}
		
		$return = realpath($file_path);
		if(!is_string($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
			trigger_error("Can't get realpath for file", E_USER_WARNING);
			return(false);
		}
		
		$return = is_link($file_path);
		if($return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
			trigger_error("File is link", E_USER_WARNING);
			return(false);
		}
		
		$return = is_file($file_path);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
			trigger_error("Path to file is not file", E_USER_WARNING);
			return(false);
		}
		
		if($test_readable) {
			$return = is_readable($file_path); 
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
				trigger_error("File is not readable", E_USER_WARNING);
				return(false);
			}
		}
		
		if($test_writable) {
			$return = is_writable($file_path);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
				trigger_error("File is not writable", E_USER_WARNING);
				return(false);
			}
		}
		
		if($test_executable) {
			$return = is_executable($file_path);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
				trigger_error("File is not executable", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
		
	}//}}}//
}

