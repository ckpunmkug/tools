<?php 

class €
{
	private static $class = NULL;
	public static function init() {
		if(self::$class === NULL) {
			self::$class = new €();
			return($GLOBALS["\x80"]);
		}
		trigger_error("Duplicate call x80::init detected", E_USER_ERROR);
		exit(255);
	}
	public static function status() {
		if(self::$class === NULL) return(true);
	}
	public function __construct() {
		if(€::status() === true) return($this->start());
		trigger_error("Duplicate declare x80 class detected", E_USER_ERROR);
	}
	public function __destruct() {
		defined("\x80") ? $x80 = € : $x80 = NULL;
		$result = $this->stop($x80);
		file_put_contents('php://stderr', "\x80\n".strval($result)."\n\x80");
	}
	
	private $command = NULL;
	private $parameters = NULL;
	private $data = [];
	private $error = false;
	
	private function start()
	{//{{{//
		
		$this->command = @strval($GLOBALS["argv"][2]);
		switch($this->command) {
			case('reflection'):
			$this->reflection_start();
			break;
			
			default:
			$this->default_start();
			break;
		}
		
	}//}}}//
	private function stop($x80)
	{//{{{//
		
		if($this->error) return(false);
		
		$result = '';
	
		switch($this->command) {
			case('reflection'):
			$result = $this->reflection_stop();
			break;
			
			default:
			$result = $this->default_stop($x80);
			break;
		}
		
		return($result);
		
	}//}}}//
	
	private function default_start()
	{//{{{//
		
		if(defined('VERBOSE') && VERBOSE) {
				user_error("Default start");
		}
		
		if(@is_string($GLOBALS["argv"][1]) != true) {
			trigger_error("Current trace source not passed in command line", E_USER_ERROR);
			exit(255);
		}
		$GLOBALS["\x80"] = $GLOBALS["argv"][1];
		
		unset($GLOBALS["argv"], $GLOBALS["argc"]);
		$GLOBALS["_SERVER"] = [];
		
		xdebug_start_code_coverage();
		
	}//}}}//
	private function default_stop($file)
	{//{{{//
	
		if($file === NULL) {
			$this->error = true;
			trigger_error("`x80` not defined for content `path to file for coverage`", E_USER_WARNING);
			return(false);
		}
	
		$code_coverage = xdebug_get_code_coverage();
		xdebug_stop_code_coverage();
	
		$array = $code_coverage[$file];
		if(!is_array($array)) {
			trigger_error("Can't get `code coverage` for passed file", E_USER_WARNING);
			return('file = '.$file);
		}
		
		$result = '';
		foreach($array as $line_number => $state) {
			if($state != 1) continue;
			if(strlen($result) > 0) $result .= ' ';
			$result .= strval($line_number);
		}
		
		$result = $file."\n".$result;
		return($result);
		
	}//}}}//
	
	private function reflection_start()
	{//{{{//
		
		$prepend_source = function(string $file, string $source)
		{//{{{//
			
			$contents = file_get_contents($file);
			if(!is_string($contents)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't get contents from file", E_USER_WARNING);
				return(false);
			}
			
			$contents = $source.$contents;
			
			$return = file_put_contents($file, $contents);
			if(!is_int($return)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't put contents to file", E_USER_WARNING);
				return(false);
			}
			
			$length = strlen($source);
			
			return($length);
			
		};//}}}//
		
		$this->data['file_path'] = strval($GLOBALS["argv"][1]);
		$this->data['line_number'] = intval($GLOBALS["argv"][3]);
		
		$GLOBALS["\x80"] = $this->data['file_path'];
		
		$return = $prepend_source($this->data['file_path'], '<?php return(NULL); ?>');
		if(!is_int($return)) {
			$this->error = true;
			trigger_error("Can't prepend source to file", E_USER_ERROR);
			exit(255);	
		}
		$this->data['source_length'] = $return;
		
		$this->data['defined_functions'] = get_defined_functions(true);
		$this->data['declared_classes'] = get_declared_classes();
		
		return(true);
		
	}//}}}//
	private function reflection_stop()
	{//{{{//
		
		$unprepend_source = function(string $file, int $length)
		{//{{{//
			
			$contents = file_get_contents($file);
			if(!is_string($contents)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't get contents from file", E_USER_WARNING);
				return(false);
			}
			
			$contents = substr($contents, $length);
			
			$return = file_put_contents($file, $contents);
			if(!is_int($return)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't put contents to file", E_USER_WARNING);
				return(false);
			}
			
			return(true);
			
		};//}}}//
		
		$get_difference = function(array $functions, array $classes)
		{//{{{//
			
			$before = $functions["user"];
			$array = get_defined_functions(true);
			$after = $array['user'];
			$result = array_diff($after, $before);
			
			$array = get_declared_classes();
			$classes = array_diff($array, $classes);
			
			foreach($classes as $class) {
				$methods = get_class_methods($class);
				foreach($methods as $method) {
					array_push($result, "{$class}::{$method}");
				}
			}
			
			return($result);
			
		};//}}}//
		
		$get_context_lines = function(string $file, array $names)
		{//{{{//
			
			$result = [0 => []];
			$pattern = '/^L(\d+)\s+.+$/';
			
			$LINE = [];
			exec("phpdbg -p {$file} 2>&1", $LINE);
			foreach($LINE as $line) {
				if(preg_match($pattern, $line, $MATCH) != 1) continue;
				if(in_array($MATCH[1], $result[0])) continue;
				array_push($result[0], intval($MATCH[1]));
			}
			
			foreach($names as $name) {
				$result[$name] = [];
				$LINE = [];
				exec("phpdbg -p={$name} {$file} 2>&1", $LINE);
				foreach($LINE as $line) {
					if(preg_match($pattern, $line, $MATCH) != 1) continue;
					if(in_array($MATCH[1], $result[$name])) continue;
					array_push($result[$name], intval($MATCH[1]));
				}
			}
			
			return($result);
			
		};//}}}//
		
		$return = $unprepend_source($this->data['file_path'], $this->data['source_length']);
		if(!$return) {
			$this->error = true;
			trigger_error("Can't unprepend source in file", E_USER_ERROR);
			exit(255);
		}
		
		$context_names = $get_difference($this->data['defined_functions'], $this->data['declared_classes']);
		
		$context_lines = $get_context_lines($this->data['file_path'], $context_names);
		
		$result = [];
		
		foreach($context_lines as $context => $line_numbers) {
			if(in_array($this->data['line_number'], $line_numbers)) {
				$result['context'] = $context;
				$result['begin'] = $line_numbers[0];
				break;
			}
		}
		
		if(count($result) == 0) {
			$this->error = true;
			trigger_error("Can't found line by number in php source context", E_USER_ERROR);
			exit(255);
		}
		
		
		if($result['context'] === 0) {
			$result ="{$result['begin']}";
		}
		else {
			$result = "{$result['context']} {$result['begin']}";
		}
		
		return($result);
		
	}//}}}//
}
if(true) require(€::init());

