<?php

/*
	Д - DOCROOT_PATH
	Ф - CURRENT_PHP
*/

if(!true) {
	$_SERVER = [
		"PHP_SELF" => substr(Ф, strlen(Д)),
		"SCRIPT_FILENAME" => Ф,
	];	
}

define('Д', '/srv/wordpress/www');

Ж::init();

function header(string $string) {
	file_put_contents('php://stderr', "\n\x81\n{$string}\n\x81\n");
}
function is_uploaded_file(string $path) {
        if(is_file($path) && is_readable($path)) return(true);
        return(false);
}
function move_uploaded_file(string $source, string $destination) {
        return(copy($source, $destination));
}
class Ж
{
	private static $class = NULL;
	public static function init() {
		if(self::$class === NULL) {
			self::$class = new Ж();
			return(true);
		}
		trigger_error("Duplicate call x80::init detected", E_USER_ERROR);
		exit(255);
	}
	public static function status() {
		if(self::$class === NULL) return(true);
	}
	public function __construct() {
		if(Ж::status() === true) return($this->start());
		trigger_error("Duplicate declare x80 class detected", E_USER_ERROR);
	}
	public function __destruct() {
		$this->stop();
	}
	private function start() {
		$_SERVER = [
			"HTTP_HOST" => 'example.com',
			"PHP_SELF" => '/index.php',
		];
		if(PHP_SAPI == 'coverage') {
			phpdbg_start_oplog();
		}
	}
	private function stop() {
		if(PHP_SAPI == 'coverage') {
			$return = phpdbg_end_oplog();
			$this->print_oplog($return);
		}	
	}
	private function print_oplog(array $oplog) {
		$return = key_exists(Ф, $oplog);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['file' => Ф]);
			trigger_error("'file' not exists in 'oplog' array", E_USER_WARNING);
			return(false);
		}
		$result = [];
		$array = $oplog[Ф];
		foreach($array as $line => $count) {
			array_push($result, $line);
		}
		asort($result);
		$text = implode(" ", $result);
		file_put_contents('php://stderr', "\n\x80\n{$text}\n\x80\n");
	}
}
