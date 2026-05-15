<?php
if(PHP_SAPI == 'cli') {
	::init();
}

function header(string $string) {
	$array = [
		"type" => 'http_header',
		"string" => $string,
	];
	$json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	file_put_contents('php://stderr', "\x80{$json}\x80");
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
	private $file = '';
	
	public static function init(string $file) {
		if(self::$class === NULL) {
			self::$class = new ($file);
			return(true);
		}
		trigger_error("Duplicate call x80::init detected", E_USER_ERROR);
		exit(255);
	}
	
	public static function status() {
		if(self::$class === NULL) return(true);
	}
	
	public function __construct(string $file) {
		if(::status() === true) {
			$this->file = $file;
			return($this->start());
		}
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
		
		if(!key_exists($this->file, $code_coverage)) {
			trigger_error("{$this->file} not exists in code coverage", E_USER_WARNING);
			return(false);
		}
		$this->print_code_coverage($code_coverage[$this->file]);
	}
	
	private function print_code_coverage(array $lines) {
		$array = [
			"type" => 'code_coverage',
			"file" => $this->file,
			"lines" => array_keys($lines),
		];
		$json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		file_put_contents('php://stderr', "\x80{$json}\x80");
	}
}

