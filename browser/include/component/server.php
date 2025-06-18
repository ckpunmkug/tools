<?php

require_once('class/C.php');

class server
{
	static $config = NULL;

	static function main(array $config)
	{//{{{//
		
		$return = self::set_config($config)
		if(!$return) {
			trigger_error("Can't set config for class", E_USER_WARNING);
			return(false);
		}
		
	}//}}}//
	
	static function set_config(array $config)
	{//{{{//
		
		if(!eval(C::$S.='$config["host"]')) return(false);
		$WEB_SERVER_HOST = $config["host"];
		
		if(!eval(C::$S.='$config["port"]')) return(false);
		$WEB_SERVER_PORT = $config["port"];
		
		if(!eval(C::$S.='$config["hash"]')) return(false);
		WEB_SERVER_HASH = $config["hash"];
		
	}//}}}//
	
}

