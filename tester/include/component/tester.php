<?php

class tester
{
	static $cms_dir = NULL;
	
	static $TABLE = [
		'PHP_FILE' => '/tester/PHP_FILE',
	];
	
	static function main(array $config, string $action, string $in)
	{//{{{//
		
		$return = self::set_parameters_from_configuration($config);
		if($return !== true) {
			if (defined('DEBUG') && DEBUG) var_dump(['config array' => $config]);
			trigger_error("Can't set class parameters from config array", E_USER_WARNING);
			return(false);
		}
		
                $ACTION = ['test', 'prepare'];
                if(in_array($action, $ACTION)) {
                        $out = self::$action($in);
                        if(!is_string($out)) {
				trigger_error("Action {$action} failed", E_USER_WARNING);
                                return(false);
                        }
                }
                else {
                        trigger_error("Unsupported action", E_USER_WARNING);
                        return(false);
                        
                }
		
		return($out);
		
	}//}}}//
	
	static function set_parameters_from_configuration(array $config)
	{//{{{//
		
		if(!eval(Check::$string.='$config["cms_dir"]')) return(false);
		self::$cms_dir = $config["cms_dir"];
		
		return(true);
		
	}//}}}//

	static function test()
	{//{{{//
		
		$PHP_FILE = self::get_PHP_FILE(self::$cms_dir);
		if(!is_array($PHP_FILE)) {
			if (defined('DEBUG') && DEBUG) var_dump(['cms directory' => $cms_dir]);
			trigger_error("Can't get list of php source files from cms directory", E_USER_WARNING);
			return(false);
		}
		
		$return = self::save_PHP_FILE($PHP_FILE);
		if(!$return) {
			trigger_error("Can't save 'PHP_FILE' into database", E_USER_WARNING);
			return(false);
		}
		
		return('');
		
	}//}}}//

	static function prepare()
	{//{{{//
		
		$r = D::open(self::$database_file);
		if(!$r) return !trigger_error("Can't open database from file", E_USER_WARNING);
		
		return(true);
		
	}//}}}//

	static function get_PHP_FILE(string $path)
	{//{{{//
		
		$PATH = FileSystem::get_PATH($path);
		if(!is_array($PATH)) {
			if (defined('DEBUG') && DEBUG) var_dump(['folder' => self::$cms_dir]);
			trigger_error("Can't get all files paths from folder", E_USER_WARNING);
			return(false);
		}
		
		if(defined('VERBOSE') && VERBOSE) 
			file_put_contents('php://stderr', "\nFilter out PHP sources\n");
		
		$PHP_FILE = [];
		$count = count($PATH);
		foreach($PATH as $key => $path)
		{
			if(defined('VERBOSE') && VERBOSE)
				file_put_contents('php://stderr', sprintf("\r(%08d) [%d] ", ($count -= 1), $key));
			
			$return = Source::is_php_file($path);
			if(!$return) continue;

			$return = Source::syntax_check_file($path);
			if(preg_match('/^.+php$/', $path) == 1) {
				if(!$return) {
					if (defined('DEBUG') && DEBUG) var_dump(['php file' => $path]);
					trigger_error("Syntax check error for php file", E_USER_NOTICE);
					continue;
				}
			}
			else {
				if($return) {
					if (defined('DEBUG') && DEBUG) var_dump(['not php source' => $path]);
					trigger_error("Syntax check OK for not php source", E_USER_NOTICE);
				}
			}

			array_push($PHP_FILE, $path);
		}
		
		if(defined('VERBOSE') && VERBOSE) 
			file_put_contents('php://stderr', "\n");
		
		return($PHP_FILE);
		
	}//}}}//

	static function save_PHP_FILE(array $PHP_FILE)
	{//{{{//
		
		$table = self::$TABLE['PHP_FILE'];
		$COLUMN = [
			"path" => '',
		];
		$return = Database::create_table($table, $COLUMN);
		if(!$return) {
			trigger_error("Can't create 'PHP_FILE' table", E_USER_WARNING);
			return(false);
		}
		
		if(defined('VERBOSE') && VERBOSE) 
			file_put_contents('php://stderr', "\nSave PHP_FILE array into database\n");
		
		$count = count($PHP_FILE);
		foreach($PHP_FILE as $key => $php_file) 
		{
			if(defined('VERBOSE') && VERBOSE)
				file_put_contents('php://stderr', sprintf("\r(%08d) [%d] ", ($count -= 1), $key));

			$data = ["path" => $php_file];
			$r = Database::insert_item($table, $data);
			if(!is_int($r)) return !trigger_error("Can't insert 'php_file' into database", E_USER_WARNING);
		}
		
		if(defined('VERBOSE') && VERBOSE)
			file_put_contents('php://stderr', "\n");
		
		return(true);
		
	}//}}}//

}

