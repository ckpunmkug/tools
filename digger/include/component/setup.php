<?php

require_once('function/get_dir_contents_recursive.php');
require_once('function/is_php_source.php');

function main(array $argv)
{
	if(true) // create data directory
	{//{{{//
			
		$return = file_exists(PATH["data"]);
		if($return) {
			$return = FileSystem::is_directory_rwx(PATH["data"], true, true, true);
			if(!$return) {
				trigger_error("Incorrect 'data' directory", E_USER_WARNING);
				return(false);
			}
		}
		else {
			$return = mkdir(PATH["data"], 0750, true);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['PATH["data"]' => PATH["data"]]);
				trigger_error("Can't create 'data' directory", E_USER_WARNING);
				return(false);
			}
		}
		
		if(defined('VERBOSE') && VERBOSE) 
			file_put_contents('php://stderr', "\nDirectory created\n");
		
	}//}}}//
	
	if(true) // create files: start, commands, notes
	{//{{{//
			
		$KEY = [ "start", "commands", "notes" ];
		foreach($KEY as $key) {
			$file = PATH[$key];
			$return = file_exists($file);
			if($return) {
				$return = FileSystem::is_file_rwx($file, true, true, false);
				if(!$return) {
					trigger_error("Incorrect existed file", E_USER_WARNING);
					return(false);
				}
			}
			else {
				$return = file_put_contents($file, '');
				if(!is_int($return)) {
					if(defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
					trigger_error("Can't create empty file", E_USER_WARNING);
					return(false);
				}
			}
		}
		
		if(defined('VERBOSE') && VERBOSE) 
			file_put_contents('php://stderr', "\nFiles created\n");
		
	}//}}}//
	
	require('data/class.php');
	
	if(true) // create database tables
	{//{{{//
		
		$TABLE = ["PHP_FILE", "SEARCH_QUERY", "SEARCH_RESULTS", "TEST_SOURCE"];
		foreach($TABLE as $table) {
			
			$return = data::$drop[$table]();
			if(!$return) {
				trigger_error("Can't drop table", E_USER_WARNING);
				return(false);
			}
			
			$return = data::$create[$table]();
			if(!$return) {
				trigger_error("Can't create table", E_USER_WARNING);
				return(false);
			}
			
		}// foreach($TABLE as $table)
		
		if(defined('VERBOSE') && VERBOSE) 
			file_put_contents('php://stderr', "\nTables created\n");
		
	}//}}}//
	
	if(true) // add PHP_FILE
	{//{{{//
		
		$return = get_dir_contents_recursive(PATH["cms"], true);
		if(!is_array($return)) {
			trigger_error("Can't recursive get contents from cms dir", E_USER_WARNING);
			return(false);
		}
		$PATH = $return;
			
		if(defined('VERBOSE') && VERBOSE) 
			file_put_contents('php://stderr', "\nSearch php files\n");
	
		$PHP_FILE = [];
		$cd = count($PATH);
		foreach($PATH as $path) {
			cd($cd);
			
			$return = is_link($path);
			if($return) continue;
			
			$return = is_file($path);
			if(!$return) continue;
			
			$return = is_php_source($path);
			if(!$return) continue;
			
			$pattern = '/^.+\.php$/';
			$return = preg_match($pattern, $path);
			if($return != 1) {
				if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
				trigger_error("Source php file have unusual extension", E_USER_WARNING);
			}
			
			array_push($PHP_FILE, $path);
			
		}// foreach($PATH as $path)
		
		if(defined('VERBOSE') && VERBOSE) 
			file_put_contents('php://stderr', "\nAdd php files to database\n");
		
		$return = data::$add["PHP_FILE"]($PHP_FILE);
		if(!$return) {
			trigger_error("Can't add php files to database", E_USER_WARNING);
			return(false);
		}
		
	}//}}}//
	
	return(true);
}

