<?php

function encode($variable)
{//{{{

	$json = json_encode($variable, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	if(!is_string($json)) {
		if(defined('DEBUG') && DEBUG) var_dump(['$variable' => $variable]);
		$error_msg = json_last_error_msg();
		trigger_error("JSON {$error_msg}", E_USER_WARNING);
		return(false);
	}
	
	return($json);
	
}//}}}

