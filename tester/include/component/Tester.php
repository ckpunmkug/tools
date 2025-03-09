<?php

class Tester
{
	static function main()
	{//{{{//
	
		$return = chdir(WWW_DIR);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['WWW_DIR' => WWW_DIR]);
			trigger_error("Can't change to `www` directory", E_USER_WARNING);
			return(false);
		}
		
		if(defined('ACTION')) {
			switch(ACTION) {
				case('test'):
					$return = self::test();
					if($return === false) return(!user_error('`test` action failed'));
					break;
				case('files'):
					$return = self::files();
					if($return === false) return(!user_error('`files` action failed'));
					break;
				default:
					trigger_error("Unsupported `action`", E_USER_WARNING);
					return(false);
			}
		}
		
		if(defined('COMMAND')) {
			$file = getenv('FILE');
			if(!is_string($file)) {
				trigger_error("Can't get `FILE` environment", E_USER_WARNING);
				return(false);
			}
			
			$number = intval(COMMAND);
			$line = self::get_line($file, $number);
			$file = WWW_DIR.ltrim($line, '.');
			
			self::less($file);
		}
		
		return(true);
		
	}//}}}//
	static function test()
	{//{{{//
		
		$return = self::less(DATA_DIR.'/php/0');
		var_dump($return);
		
	}//}}}//
	static function files()
	{//{{{//
		
		loop:
		
		$dir = DATA_DIR.'/php';
		$names = scandir($dir);
		if(!is_array($names)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir' => $dir]);
			trigger_error("Can't scan directory with lists of php files", E_USER_WARNING);
			return(false);
		}
		
		$files = [];
		foreach($names as $index => $name) {
			$path = "{$dir}/{$name}";
			
			if(!is_file($path)) continue;
			if(preg_match('/.+\.lst$/', $name) != 1) continue;
			
			$files[$index] = $path;
			echo(sprintf("%7d %s\n", $index, $name));
		}
		
		$command = readline('files > ');
		$command = trim($command);
		
		switch($command) {
			case(''): break;
			case('q'): case('quit'): exit(0);
		}
		
		if(preg_match('/^(\d+)$/', $command, $MATCH) == 1) {
			$index = $MATCH[1];
			if(key_exists($index, $files)) {
				self::less($files[$index]);
			}
		}
		
		goto loop;
		
	}//}}}//
	static function less($file_path)
	{//{{{//
	
		$ENV = self::get_environments();
		if(!is_array($ENV)) {
			trigger_error("Can't get environments", E_USER_WARNING);
			return(false);
		}
		$ENV['_'] = LESS_BIN;
		$ENV['FILE'] = $file_path;
		
		$cmd = LESS_BIN.' -N '.$file_path;
		$STD = [STDIN, STDOUT, STDERR];
		$PIPE = [];
		$cwd = WWW_DIR;
		

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
		$result["SHELL"] = SELF_COMMAND;
		
		return($result);
		
	}//}}}//
	static function get_line(string $file_path, int $line_number)
	{//{{{//
		
		$contents = file_get_contents($file_path);
		if(!is_string($contents)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
			trigger_error("Can't get contents from  file", E_USER_WARNING);
			return(false);
		}
		
		$LINE = explode("\n", $contents);
		
		if(!key_exists(($line_number-1), $LINE)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$line_number' => $line_number]);
			trigger_error("Line not exists with given number", E_USER_WARNING);
			return(false);
		}
		
		return($LINE[$line_number-1]);
		
	}//}}}//
}

