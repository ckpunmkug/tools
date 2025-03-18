<?php

require_once('component/Search.php');

class Source
{
	static $www_dir = CONFIG['www_dir'];
	static $php_files_list = CONFIG['php_files_list'];

	static $php_bin = '/usr/bin/php';

	static function main()
	{//{{{//
		
		if(!defined('PARAMETERS_STRING')) {
			trigger_error("`line position` not passed via `parameters string` in command line", E_USER_WARNING);
			return(false);
		}
		$string = PARAMETERS_STRING;
		
		$pattern = '/^(\d+)\:(\d+)$/';
		if(preg_match($pattern, $string, $MATCH) != 1) {
			if (defined('DEBUG') && DEBUG) var_dump(['$string' => $string]);
			trigger_error("Can't parse `FILE:LINE position` from parameters string", E_USER_WARNING);
			return(false);
		}
		$file_number = intval($MATCH[1]);
		$line_number = intval($MATCH[2]);
		
		$file_path = Search::get_file_path($file_number);
		if($file_path === false) return(!user_error("Can't get `file path` using `file number`"));
		
		$position = self::get_reflection($file_path, $line_number);
		if(!is_string($position)) {
			trigger_error("Can't get reflection for source with line", E_USER_WARNING);
			return(false);
		}
		
		echo("{$position} {$file_path}\n");
		
		return(true);
		
	}//}}}//
	
	static function get_reflection(string $file_path, int $line_number)
	{//{{{//
		
		$x80_path = realpath(__DIR__.'/../class/x80.php');
		if(!is_string($x80_path)) {
			trigger_error("Can't get realpath for x80.php", E_USER_WARNING);
			return(false);
		}
		
		
		$command = self::$php_bin.' '.$x80_path.' '.$file_path.' reflection '.strval($line_number).' 2>&1';
		$output = [];
		$status = 0;
		
		exec($command, $output, $status);
		if($status != 0) {
			trigger_error("Can't exec x80", E_USER_WARNING);
			return(false);
		}
		
		if(count($output) != 3) {
			trigger_error("Incorrect exec x80 output", E_USER_WARNING);
			return(false);
		}
		
		return($output[1]);
	}//}}}//
}

