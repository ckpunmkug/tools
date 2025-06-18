<?php

require_once('function/launch.php');

class Source
{
	static $php = '/usr/bin/php';
	
	static function is_php_file(string $path)
	{//{{{//
		
		if(!( file_exists($path) && is_file($path) && is_readable($path) )) return(false);
		
		$source = file_get_contents($path);
		if(!is_string($source)) return(false);
		
		$TOKEN = @token_get_all($source);
		foreach ($TOKEN as $token) {
			if (is_array($token) && ($token[0] == T_OPEN_TAG || $token[0] == T_OPEN_TAG_WITH_ECHO) ) return(true);
		}
		
		return(false);
		
	}//}}}//
	
	static function syntax_check_file(string $path)
	{//{{{//
		
		$command = self::$php." -l {$path}";
		$return = launch($command, 60);
		if(!is_array($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['syntax check command' => $command]);
			trigger_error("Can't launch syntax check command", E_USER_WARNING);
			return(false);
		}
		
		if($return["status"] == 0) return(true);
		else return(false);
		
	}//}}}//
}

