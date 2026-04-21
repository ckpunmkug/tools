<?php

function import(string $json_file)
{//{{{//
	
	$json = file_get_contents($json_file);
	if(!is_string($json)) {
		if (defined('DEBUG') && DEBUG) var_dump(['$json_file' => $json_file]);
		trigger_error("Can't get json content from file", E_USER_WARNING);
		return(false);
	}
	
	$variable = json_decode($json, true);
	$error = json_last_error();
	if($variable === NULL && $error !== JSON_ERROR_NONE) {
		$error_msg = json_last_error_msg();
		trigger_error("JSON {$error_msg}", E_USER_WARNING);
		return(false);
	}
	
	return($variable);
	
}//}}}//

