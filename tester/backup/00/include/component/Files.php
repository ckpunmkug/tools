<?php

require_once('class/Launch.php');

class Files
{
	static $php_files_list = CONFIG['php_files_list'];
	static $www_dir = CONFIG['www_dir'];
	
	static function main()
	{//{{{//
		
		$return = chdir(self::$www_dir);
		if(!$return) {
			trigger_error("Can't change to `www` directory", E_USER_WARNING);
			return(false);
		}
		
		Launch::less(self::$php_files_list);
		
		return(true);
		
	}//}}}//
}

