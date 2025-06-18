<?php

require_once('class/PHPDebugger.php');

class Debugger
{
	static $php_test_file = CONFIG['php_test_file'];
	static $www_dir = CONFIG['www_dir'];
	static $command_file = CONFIG['data_dir'].'/debugger.cmd';
	
	static function main() 
	{//{{{//
		
		$PHPDebugger = new PHPDebugger(self::$php_test_file, self::$www_dir);
		
		$file = self::$command_file;
		$return = file_get_contents($file);
		if(!is_string($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
			trigger_error("Can't get `debugger commands` from file", E_USER_WARNING);
			return(false);
		}
		$COMMAND = explode("\n", $return);
		
		foreach($COMMAND as $command) {
			$command = trim($command);
			
			if(strlen($command) == 0) continue;
			if(preg_match('/^#.*$/', $command) == 1) continue;
			
			$PHPDebugger->send($command);
		}
		
		return(true);
		
	}//}}}//
}

