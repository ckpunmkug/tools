<?php

function export(string $json_file, $variable)
{
	$json = json_encode($variable, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	if(!is_string($json)) {
		$error_msg = json_last_error_msg();
		trigger_error("JSON {$error_msg}", E_USER_WARNING);
		return(false);
	}
	
	$bytes = file_put_contents($json_file, $json);
	if(!is_int($bytes)) {
		if (defined('DEBUG') && DEBUG) var_dump(['$json_file' => $json_file]);
		trigger_error("Can't put json content to file", E_USER_WARNING);
		return(false);
	}
	
	return(true);
}

