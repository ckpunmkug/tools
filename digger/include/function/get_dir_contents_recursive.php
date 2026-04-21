<?php

// get directory items recursively
function get_dir_contents_recursive(
	string $dir_path, // path to the directory to start from
	bool $enable_notice = false, // display a notification if the directory cannot be read
	int $max_count_files = 0xFFFF // maximum number of files in the list
) {	
	/*
	$result = [
		'/path/to/item/0',
		...
		'/path/to/item/N',
	];// or false if failure
	
	$a = get_dir_contents_recursive('/srv/wordpress/www', true);
	var_dump(count($a)); // int(3710)
	*/
	
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
			if($enable_notice) {
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
		
		//$result = array_merge($FILE, $LINK, $DIR, $OTHER);
		$result = array_merge($DIR, $FILE, $LINK, $OTHER);
		
		return($result);
		
	};// $scandir = function($directory_path)
	
	$files = $scandir($dir_path);
	$count = count($files);
	for($index = 0; $index < $count; $index += 1) {
	
		$count = count($files);
		if($count > $max_count_files) break;
		
		$current_path = $files[$index];
		
		if(is_link($current_path)) continue;
		
		if(is_dir($current_path)) {
			//$begin = array_slice($files, 0, $index+1);				
			//$end = array_slice($files, $index+1);				
			
			$return = $scandir($current_path);
			if(!is_array($return)) continue;
			
			//$files = array_merge($begin, $return, $end);
			$files = array_merge($files, $return);
		}
		
	}// for($index = 0; $index < count($directory_path); $index++)
	
	return($files);
}

