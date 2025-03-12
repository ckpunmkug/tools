<?php

class Launch
{
	static $less = '/usr/bin/less';
	static $vim = '/usr/bin/vim';
	static $cwd = CONFIG["www_dir"];
	static $shell = CONFIG["shell"];

	static function get_environments()
	{//{{{//
	
		$NAME = ["LOGNAME", "HOME", "LANG", "TERM", "USER", "PATH"];
		
		$result = [];
		foreach($NAME as $name) {
			$value = getenv($name);
			if(!is_string($value)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$name' => $name]);
				trigger_error("Can't get environment with given name", E_USER_WARNING);
				return(false);
			}
			$result[$name] = $value;
		}
		
		$cwd = getcwd();
		if(!is_string($cwd)) {
			trigger_error("Can't get current working directory", E_USER_WARNING);
			return(false);
		}
		
		$result["PWD"] = $cwd;
		$result["SHELL"] = self::$shell;
		
		return($result);
		
	}//}}}//
	
	static function less(string $file_path, int $line_number = 0)
	{//{{{//
	
		$ENV = self::get_environments();
		if(!is_array($ENV)) {
			trigger_error("Can't get environments", E_USER_WARNING);
			return(false);
		}
		$ENV['_'] = self::$less;
		$ENV['FILE'] = $file_path;
		
		$goto_line = '';
		if($line_number > 0) {
			$goto_line = " +{$line_number}g ";
		}
		
		$cmd = self::$less.' -c -N '.$goto_line.$file_path;
		$STD = [STDIN, STDOUT, STDERR];
		$PIPE = [];
		$cwd = self::$cwd;
		
		$proc = proc_open($cmd, $STD, $PIPE, $cwd, $ENV);
		if(!is_resource($proc)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$cmd' => $cmd]);
			trigger_error("Can't open process for command", E_USER_WARNING);
			return(false);
		}
		
		while(true) {//
			$proc_status = proc_get_status($proc);
			if(!is_array($proc_status)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$cmd' => $cmd]);
				trigger_error("Can't get process status for command", E_USER_WARNING);
				return(false);
			}
			
			if($proc_status["running"] == false) break;
			usleep(100000);
		}// while(true)
		
		proc_close($proc);
		
		return(true);
		
	}//}}}//
	
	static function vim(string $file_path)
	{//{{{//
	
		$ENV = self::get_environments();
		if(!is_array($ENV)) {
			trigger_error("Can't get environments", E_USER_WARNING);
			return(false);
		}
		$ENV['_'] = self::$vim;
		$ENV['FILE'] = $file_path;
		
		$cmd = self::$vim.' '.$file_path;
		$STD = [STDIN, STDOUT, STDERR];
		$PIPE = [];
		$cwd = self::$cwd;
		

		$proc = proc_open($cmd, $STD, $PIPE, $cwd, $ENV);
		if(!is_resource($proc)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$cmd' => $cmd]);
			trigger_error("Can't open process for command", E_USER_WARNING);
			return(false);
		}
		
		while(true) {//
			$proc_status = proc_get_status($proc);
			if(!is_array($proc_status)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$cmd' => $cmd]);
				trigger_error("Can't get process status for command", E_USER_WARNING);
				return(false);
			}
			
			if($proc_status["running"] == false) break;
			usleep(100000);
		}// while(true)
		
		proc_close($proc);
		
	}//}}}//
}

