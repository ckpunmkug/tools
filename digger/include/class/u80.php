<?php
if(PHP_SAPI != 'phpdbg') {
	::init();
}

function header(string $string) {
	file_put_contents('php://stderr', "\x81{$string}\x81");
}
function is_uploaded_file(string $path) {
        if(is_file($path) && is_readable($path)) return(true);
        return(false);
}
function move_uploaded_file(string $source, string $destination) {
        return(copy($source, $destination));
}
class 
{
	private static $class = NULL;
	
	public static function init() {
		if(self::$class === NULL) {
			self::$class = new ();
			return(true);
		}
		trigger_error("Duplicate call x80::init detected", E_USER_ERROR);
		exit(255);
	}
	
	public static function status() {
		if(self::$class === NULL) return(true);
	}
	
	public function __construct() {
		if(::status() === true) return($this->start());
		trigger_error("Duplicate declare x80 class detected", E_USER_ERROR);
	}
	
	public function __destruct() {
		$this->stop();
	}
	
	private function start() {
		xdebug_start_code_coverage();
	}
	
	private function stop() {
		$code_coverage = xdebug_get_code_coverage();
		xdebug_stop_code_coverage();
		$this->print_code_coverage($code_coverage);
	}
	
	private function print_code_coverage(array $code_coverage) {
		$json = json_encode($code_coverage, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		file_put_contents('php://stderr', "\x80{$json}\x80");
	}
}

