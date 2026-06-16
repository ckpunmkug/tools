<?php

function export(string $filename, $variable)
{
	$contents = encode($variable);
	if(!is_string($contents)) {
		trigger_error("Can't encode variable to json", E_USER_WARNING);
		return(false);
	}
	
	$return = file_put_contents($filename, $contents);
	if(!is_int($return)) {
		if(defined('DEBUG') && DEBUG) var_dump(['$filename' => $filename]);
		trigger_error("Can't put json contents to file", E_USER_WARNING);
		return(false);
	}
	
	return(true);
}

