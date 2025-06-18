<?php

require_once('class/Launch.php');

class Search
{
	static $php_files_list = CONFIG['php_files_list'];
	static $search_dir = CONFIG['search_dir'];
	static $www_dir = CONFIG['www_dir'];
	static $PHP_FILE = [];
	static $RESULT_FILE = [];
	
	static function main()
	{//{{{//
		
		begin:
		
		$file = self::$php_files_list;
		$return = file($file, FILE_IGNORE_NEW_LINES);
		if(!is_array($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
			trigger_error("Can't get `php files list` from file", E_USER_WARNING);
			return(false);
		}
		self::$PHP_FILE = $return;
		
		$return = self::get_RESULT_FILE(self::$search_dir);
		if(!is_array($return)) {
			trigger_error("Can't get list of results files", E_USER_WARNING);
			return(false);
		}
		self::$RESULT_FILE = $return;
		
		self::print_RESULT_FILE();
		
		$command = readline('search > ');
		$return = self::exec_command($command);
		
		if($return !== true) {
			user_error('Incorrect command');
		}
		
		goto begin;
		
	}//}}}//
	
	static function get_RESULT_FILE(string $dir)
	{//{{{//
		
		exec("ls -t {$dir}", $output, $status);
		if($status != 0) {
			if (defined('DEBUG') && DEBUG) var_dump(['$dir' => $dir]);
			trigger_error("Can't get file names from directory with `search results`", E_USER_WARNING);
			return(false);
		}
		$NAME = array_reverse($output);
		
		$result = [];
		foreach($NAME as $name) {
			$path = "{$dir}/{$name}";
		
			if(!is_file($path)) continue;
			
			$pattern = urldecode($name);
			$array = [
				"pattern" => $pattern,
				"path" => $path,
			];
			
			array_push($result, $array);
		}
		
		return($result);
		
	}//}}}//
	
	static function print_RESULT_FILE()
	{//{{{//
		
		echo("\n");
		
		foreach(self::$RESULT_FILE as $index => $result_file) {
			$pattern = strval($result_file["pattern"]);
			$string = sprintf("%2d %s\n", $index, $pattern);
			echo($string);
		}
		
		echo("\n");
		
	}//}}}//

	static function exec_command(string $command)
	{//{{{//
	
		$command = trim($command);
		
		$pattern = '/^(q|quit)$/';
		$return = preg_match($pattern, $command, $MATCH);
		if($return == 1) {
			exit(0);
		}
		
		$pattern = '/^\/(.+)\/([i]*)$/';
		$return = preg_match($pattern, $command, $MATCH);
		if($return == 1) {
			$pattern = '/'.$MATCH[1].'/'.$MATCH[2];
			self::new_search($pattern);
			return(true);
		}
		
		$pattern = '/^(\d+)$/';
		$return = preg_match($pattern, $command, $MATCH);
		if($return == 1) {
			$index = $MATCH[1];
			if(key_exists($index, self::$RESULT_FILE)) {
				Launch::less(self::$RESULT_FILE[$index]["path"]);
				return(true);
			}
		}
		
		$pattern = '/^(\d+)e$/';
		$return = preg_match($pattern, $command, $MATCH);
		if($return == 1) {
			$index = $MATCH[1];
			if(key_exists($index, self::$RESULT_FILE)) {
				Launch::vim(self::$RESULT_FILE[$index]["path"]);
				return(true);
			}
		}
		
		$pattern = '/^(\d+)d$/';
		$return = preg_match($pattern, $command, $MATCH);
		if($return == 1) {
			$index = $MATCH[1];
			if(key_exists($index, self::$RESULT_FILE)) {
				unlink(self::$RESULT_FILE[$index]["path"]);
				return(true);
			}
		}
		
		return(false);
		
	}//}}}//

	static function new_search($pattern)
	{//{{{//
		
		$return = self::preg_match($pattern);
		if(!is_array($return)) {
			trigger_error("`Search::preg_match` failed", E_USER_WARNING);
			return(false);
		}
		$result = $return;
		
		$return = self::save_result($pattern, $result);
		if(!$return) {
			trigger_error("Can't save search result to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function preg_match(string $pattern)
	{//{{{//
		
		$result = [];
		
		foreach(self::$PHP_FILE as $file_number => $file) {
			$LINE = file($file);
			if(!is_array($LINE)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't get lines in to array from file", E_USER_WARNING);
				return(false);
			}
			
			foreach($LINE as $key => $value) {
				$line = trim($value);
				$line_number = $key + 1;
				
				if(preg_match($pattern, $line) != 1) continue;
				
				$array = [
					"file" => $file,
					"file_number" => $file_number,
					"line" => $line,
					"line_number" => $line_number,
				];
				array_push($result, $array);
			}
		}
		
		return($result);
		
	}//}}}//
	
	static function save_result(string $pattern, array $result)
	{//{{{//
		
		$name = urlencode($pattern);
		$file = self::$search_dir."/{$name}";
		
		$contents = '';
		foreach($result as $data) {
			$file_number = intval($data["file_number"]);
			$line_number = intval($data["line_number"]);
			$line = trim($data["line"]);
			
			$string = sprintf("%05d:%05d  %s\n", $file_number, $line_number, $line);
			$contents .= $string;
		}
		
		$return = file_put_contents($file, $contents);
		if(!is_int($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
			trigger_error("Can't put `search result` contents to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function get_file_path(int $file_number)
	{//{{{//
		
		$file = self::$php_files_list;
		$contents = file_get_contents($file);
		if(!is_string($contents)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
			trigger_error("Can't get contents of `php files list` file", E_USER_WARNING);
			return(false);
		}
		$FILE_PATH = explode("\n", $contents);
		
		$return = key_exists($file_number, $FILE_PATH);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file_number' => $file_number]);
			trigger_error("File with paased number not exists in list", E_USER_WARNING);
			return(false);
		}
		$file_path = trim($FILE_PATH[$file_number]);
		
		return($file_path);
		
	}//}}}//
}

