<?php

class Tree
{

	// php_files_list - List of paths to files with php sources.
	// file_name = php.[-_a-zA-Z0-9]+.lst
	
	function __construct()
	{//{{{//
	
		while(true) {
			$command = readline('/tree >');
			switch($command) {
				case('q'):
				case('quit'):
					exit(0);
				case('l'):
				case('list'):
					$this->list();
				default:
					trigger_error("Unkown command", E_USER_WARNING);
					return(false);
			}
		}
		
	}//}}}//
	
	function get_PHP_FILES_LIST()
	{//{{{//
	
		$NAME = scandir(DATA_DIR_PATH);
		if(!is_array($NAME)) {
			if (defined('DEBUG') && DEBUG) var_dump(['DATA_DIR_PATH' => DATA_DIR_PATH]);
			trigger_error("Can't get file names from data directory", E_USER_WARNING);
			return(false);
		}
		
		$FILE_LIST = [];
		foreach($NAME as $name) {
			$path = DATA_DIR_PATH.'/'.$name;
			if(!is_file($path)) continue;
			
			$regexp = '/^php\.([\-\_a-zA-Z0-9]+)\.lst$/';
			$return = preg_match($regexp, $name, $MATCH);
			if($return != 1) continue;
			
			array_push($FILE_LIST, [
				"name" => $MATCH[1],
				"path" => $path,
			]);
		}
		
		foreach($FILE_LIST as $index => $array) {
			$string = sprintf("%7d %s\n", $index, $array['name']);
			echo($string);
		}
		
		return($FILE_LIST);
		
	}//}}}//
	
	static function print_FILE_LIST
	
}

