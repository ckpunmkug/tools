<?php

require_once('class/Launch.php');

class Tester
{
	static $php_files_list = CONFIG['php_files_list'];
	static $www_dir = CONFIG['www_dir'];
	static $search_dir = CONFIG['search_dir'];
	static $screen = '/usr/bin/screen';
	
	static function main(string $command)
	{//{{{//
		
		$return = chdir(self::$www_dir);
		if(!$return) {
			trigger_error("Can't change to `www` directory", E_USER_WARNING);
			return(false);
		}
	
		$file = getenv('FILE');
		if(!is_string($file)) {
			trigger_error("Can't get `FILE` environment", E_USER_WARNING);
			return(false);
		}
			
		$number = intval($command);
		$line = self::get_line($file, $number);
		
		$return = strcmp($file, self::$php_files_list);
		if($return === 0) {
			$return = self::files_handler($line);
			return($return);
		}
		
		$return = strpos($file, self::$search_dir);
		if($return === 0) {
			$return = self::search_results_handler($line);
			return($return);
		}
		
		$return = strpos($file, self::$www_dir);
		if($return === 0) {
			$return = self::php_file_handler($file, $number);
			return($return);
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

	static function files_handler(string $line)
	{//{{{//
		
		$file = trim($line);
		Launch::less($file);
		
		return(true);
		
	}//}}}//

	static function search_result_handler(string $line)
	{//{{{//

		$pattern = '/^(\d+):(\d+)\s+.+$/';
		$return = preg_match($pattern, $line, $MATCH);
		if($return != 1) {
			if (defined('DEBUG') && DEBUG) var_dump(['$line' => $line]);
			trigger_error("Can't parse `file number` and `line number` from passed line", E_USER_WARNING);
			return(false);
		}
		$file_number = intval($MATCH[1]);
		$line_number = intval($MATCH[2]);
		
		$return = file(self::$php_files_list, FILE_IGNORE_NEW_LINES);
		if(!is_array($return)) {
			trigger_error("Can't get php files list", E_USER_WARNING);
			return(false);
		}
		$PHP_FILE = $return;
		
		if(!key_exists($file_number, $PHP_FILE)) {
			trigger_error("`file number` not exists in `php files` list", E_USER_WARNING);
			return(false);
		}
		$file = $PHP_FILE[$file_number];
		
		Launch::less($file, $line_number);
	
	}//}}}//

	static function php_file_handler(string $file, int $number)
	{//{{{//
	
		$buf_file = tempnam('/tmp', 'screen_buf');
		
		$string = "{$file}:{$number}";
		file_put_contents($buf_file, $string);
		
		system(self::$screen.' -X readbuf '.$buf_file);
		
		unlink($buf_file);
		
		return(true);
		
	}//}}}//
}

