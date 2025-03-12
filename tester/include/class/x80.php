<?php 

class €
{
	private static $class = NULL;
	public static function init() {
		if(self::$class === NULL) {
			self::$class = new €();
			$result = $GLOBALS["source"];
			unset($GLOBALS["source"]);
			return($result);
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
		$return = $this->stop(€);
		if(!is_string($return)) {
			trigger_error("'x80->stop' have no output", E_USER_WARNING);
			return(false);
		}
		file_put_contents('php://stderr', "\x80\n{$return}\n\x80");
	}
	private function start()
	{//{{{//
	
		if(@is_string($GLOBALS["argv"][1]) != true) {
			trigger_error("Current trace source not passed in command line", E_USER_ERROR);
			exit(255);
		}
		$GLOBALS["source"] = $GLOBALS["argv"][1];
		
		unset($GLOBALS["argv"], $GLOBALS["argc"]);
		$GLOBALS["_SERVER"] = [];
		
		xdebug_start_code_coverage();
		
	}//}}}//
	private function stop(string $file)
	{//{{{//
	
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
}
require(€::init());
