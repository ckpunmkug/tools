<?php

class Project
{
	static function is_setup(array $PATH_keys)
	{//{{{//
	
		$counter = 0;
		
		foreach($PATH_keys as $key) {
			$return = FileSystem::is_file_rwx(PATH[$key], true, true, false, false);
			if($return) {
				$counter += 1;
			}
			else {
				trigger_error("File '{$key}' is not setup", E_USER_NOTICE);
			}
		}
		
		$return = count($PATH_keys);
		if($counter != $return) {
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function setup_dir(string $dir_path)
	{//{{{//
		
		$return = file_exists($dir_path);
		if(!$return) {
			$return = mkdir($dir_path, 0755, true);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$dir_path' => $dir_path]);
				trigger_error("Can't make dir", E_USER_WARNING);
				return(false);
			}
			return(true);
		}
		
		$return = FileSystem::is_dir_rwx($dir_path, true, true, true, false);
		if(!$return) {
			trigger_error("Incorrect dir in path", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function setup_file(string $file_path, string $file_contents)
	{//{{{//
		
		$return = dirname($file_path);
		$return = self::setup_dir($return);
		if(!$return) {
			trigger_error("Can't setup dir", E_USER_WARNING);
			return(false);
		}
		
		$return = file_exists($file_path);
		if($return) {
			$return = FileSystem::is_file_rwx($file_path, true, true, false, false);
			if(!$return) {
				trigger_error("Incorrect file in path", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = file_put_contents($file_path, $file_contents);
		if(!is_int($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
			trigger_error("Can't put contents to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function setup_config()
	{//{{{//
		
		$path = DIR.'/include/project/default.php';
		$return = file_get_contents($path);
		if(!is_string($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't get 'default config' contents from file", E_USER_WARNING);
			return(false);
		}
		$config_contents = $return;
		
		$return = self::setup_file(PATH["config"], $config_contents);
		if(!$return) {
			trigger_error("Can't setup file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function setup_database()
	{//{{{//
		
		$return = self::setup_file(PATH["database"], '');
		if(!$return) {
			trigger_error("Can't setup file", E_USER_WARNING);
			return(false);
		}
		
		$return = Database::open(PATH["database"]);
		if(!$return) {
			trigger_error("Can't open database from file", E_USER_WARNING);
			return(false);
		}
		
		foreach(data::$create as $table => $function) {
			$return = data::$create[$table]();
			if(!$return) {
				trigger_error("Can't create table in database", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
		
	}//}}}//
	
	static function purge_file(string $file_path, int $dir_level = 0)
	{//{{{//
		
		$return = file_exists($file_path);
		if($return) {
			$return = unlink($file_path);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
				trigger_error("Can't unlink file", E_USER_WARNING);
				return(false);
			}
		}
		
		$path = $file_path;
		for($i = $dir_level; $i > 0; $i -= 1) {
			$path = dirname($path);
			$return = self::purge_dir($path);
			if(!$return) {
				trigger_error("Can't purge dir", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
		
	}//}}}//

	static function purge_dir(string $dir_path)
	{//{{{//
		
		$return = file_exists($dir_path);
		if($return) {
			$return = scandir($dir_path);
			if(!is_array($return)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$dir_path' => $dir_path]);
				trigger_error("Can't scan dir", E_USER_WARNING);
				return(false);
			}
			if(count($return) == 2) {
				$return = rmdir($dir_path);
				if(!$return) {
					if(defined('DEBUG') && DEBUG) var_dump(['$dir_path' => $dir_path]);
					trigger_error("Can't remove dir", E_USER_WARNING);
					return(false);
				}
			}
		}
		return(true);
		
	}//}}}//
}

