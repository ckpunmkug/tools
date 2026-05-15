<?php

function decode(string $json)
{//{{{

	$variable = json_decode($json, true);
	$error = json_last_error();
	if($variable === NULL && $error !== JSON_ERROR_NONE) {
		if(defined('DEBUG') && DEBUG) var_dump(['$json' => $json]);
		$error_msg = json_last_error_msg();
		trigger_error("JSON {$error_msg}", E_USER_WARNING);
		return(false);
	}
	
	return($variable);
	
}//}}}

