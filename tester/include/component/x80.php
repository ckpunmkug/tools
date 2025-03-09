<?php 

class @
{
	private static $class = NULL;
	public static function init() {
		if(self::$class === NULL) {
			self::$class = new @();
			return(true);
		}
		trigger_error("Duplicate call x80::init detected", E_USER_ERROR);
		exit(255);
	}
	public static function status() {
		if(self::$class === NULL) return(true);
	}
	public function __construct() {
		if(@::status() === true) return($this->start());
		trigger_error("Duplicate declare x80 class detected", E_USER_ERROR);
	}
}
@::init();
