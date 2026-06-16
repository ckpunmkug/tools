<?php

function import(string $filename)
{
	$contents = file_get_contents($filename);
	if(!is_string($contents)) {
		if(defined('DEBUG') && DEBUG) var_dump(['$filename' => $filename]);
		trigger_error("Can't get json contents from file", E_USER_WARNING);
		return(false);
	}
	
	$variable = decode($contents);
	if($variable === false) {
		trigger_error("Can't decode json contents", E_USER_WARNING);
		return(false);
	}
	
	return($variable);
}

