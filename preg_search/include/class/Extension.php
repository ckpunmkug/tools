<?php

class Extension
{
	static function apply_path_filter(array $PATH, string $filter)
	{//{{{//
		
		$result = [];
		foreach($PATH as $path) {
			if(!is_file($path)) continue;
		
			$return = preg_match($filter, $path);
			if($return === false) {
				if(defined('DEBUG') && DEBUG) var_dump(['$filter' => $filter]);
				trigger_error("Can't perform regular expression", E_USER_WARNING);
				return(false);
			}
			
			if($return != 1) continue;
			array_push($result, $path);
		}
		
		return($result);
		
	}//}}}//
	
	static function find_lines(array $PATH, string $pattern)	
	{//{{{//
		
		$SEARCH_RESULT = [];
		foreach($PATH as $file) {
		
			$LINE = file($file);
			if(!is_array($LINE)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't get lines array from file", E_USER_WARNING);
				return(false);
			}
			
			foreach($LINE as $key => $value) {
			
				$line = trim($value);
				$number = $key + 1;
				
				$return = preg_match($pattern, $line);
				if($return === false) {
					if(defined('DEBUG') && DEBUG) var_dump(['$pattern' => $pattern]);
					trigger_error("Incorrect 'pattern'", E_USER_WARNING);
					return(false);
				}
				if($return != 1) continue;
				
				array_push($SEARCH_RESULT, [
					"file" => $file,
					"line" => $line,
					"number" => $number,
				]);
				
			}// foreach($LINE as $key => $value)
			
		}// foreach($PHP_FILE as $file)
		
		return($SEARCH_RESULT);
		
	}//}}}//
}

