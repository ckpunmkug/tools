<?php

require_once('function/launch.php');

class Tracer
{
	static $php = '/usr/bin/php'.' -c /etc/php/8.2/cli/tester.ini';
	static $x80 = __DIR__.'/../class/x80.php';
	static $php_test_file = CONFIG["php_test_file"];
	static $stdout_file = CONFIG["data_dir"].'/stdout.html';
	
	static function main()
	{//{{{//
	
		$path = self::$x80;
		$x80 = realpath($path);
		if(!is_string($x80)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't get real path for x80 component", E_USER_WARNING);
			return(false);
		}
		
		$path = self::$php_test_file;
		$current = realpath($path);
		if(!is_string($current)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't get real path for current trace source", E_USER_WARNING);
			return(false);
		}
		
		$command = self::$php." {$x80} {$current}";
		$return = launch($command, 10);
		if(!is_array($return)) {
			trigger_error("Can't launch x80", E_USER_WARNING);
			return(false);
		}
		$status = $return["status"];
		$stdout = $return["stdout"];
		$stderr = $return["stderr"];
		
		echo($stderr);
		
		$file = self::$stdout_file;
		$return = file_put_contents($file, $stdout);
		if(!is_int($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
			trigger_error("Can't put `test file stdout` to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
}

