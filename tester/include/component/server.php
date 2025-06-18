<?php

require_once('class/C.php');

class server
{
	static $php = NULL;
	static $host = NULL;
	static $port = NULL;
	static $credentials_md5 = NULL;
	
	static function main(array $config)
	{//{{{//
		
		$return = self::get_config_parameters($config);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['config array' => $config]);
			trigger_error("Can't get parameters from passed 'config' array", E_USER_WARNING);
			return(false);
		}
		
		$return = self::start_built_in_server();
		if(!$return) {
			trigger_error("Can't start built in server", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function get_config_parameters(array $config)
	{//{{{//
		
		if(!defined('PROJECT_DIR')) {
			trigger_error("Constant 'PROJECT_DIR' not defined", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(C::$S.='$config["php"]')) return(false);
		self::$php = $config["php"];
		
		if(!eval(C::$S.='$config["host"]')) return(false);
		self::$host = $config["host"];
		
		if(!eval(C::$I.='$config["port"]')) return(false);
		self::$port = $config["port"];
		
		if(!eval(C::$S.='$config["credentials_md5"]')) return(false);
		self::$credentials_md5 = $config["credentials_md5"];
		
		return(true);
		
	}//}}}//

	static function start_built_in_server()
	{//{{{//
		
		$file = PROJECT_DIR.'/docroot/index.php';
		if(!C::FR($file)) {
			trigger_error("Can't access to 'docroot/index.php' file", E_USER_WARNING);
			return(false);
		}
		
		$command = self::$php
			.' -S '.self::$host.':'.strval(self::$port)
			." {$file}"
		;
		
		system($command);
		
	}//}}}//
}

