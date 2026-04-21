<?php

function is_php_source(string $file_path, string $php_bin = '/usr/bin/php')
{
	$return = file_get_contents($file_path);
	if(!is_string($return)) {
		if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
		trigger_error("Can't get php source file contents", E_USER_WARNING);
		return(false);
	}
	$php_source = $return;
	
	$return = token_get_all($php_source);
	if(!is_array($return)) {
		if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
		trigger_error("Can't get all tokens from php source", E_USER_WARNING);
		return(false);
	}
	$TOKEN = $return;
	
	foreach($TOKEN as $token) {
		if(
			is_array($token)
			&& isset($token[0])
			&& (
				$token[0] == T_OPEN_TAG
				|| $token[0] == T_OPEN_TAG_WITH_ECHO
			)
		) {
			$command = "{$php_bin} -l {$file_path}";
			$return = launch($command);
			if(!is_array($return)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
				trigger_error("Can't launch command", E_USER_WARNING);
				return(false);
			}
			$status = $return["status"];
			$stdout = $return["stdout"];
			$stderr = $return["stderr"];
			
			if($status != 0) {
				if(defined('DEBUG') && DEBUG) var_dump([
					'$file_path' => $file_path,
					'$status' => $status,
					'$stdout' => $stdout,
					'$stderr' => $stderr,
				]);
				trigger_error("Can't lint php source file", E_USER_WARNING);
				return(false);
			}
			
			if($status === 0) {
				return(true);
			}
		}
	}// foreach($TOKEN as $token)
	
	return(false);	
}

