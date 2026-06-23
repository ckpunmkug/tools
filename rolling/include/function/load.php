<?php

function load(string $filename)
{
	$contents = file_get_contents($filename);
	if(!is_string($contents)) {
		if(defined('DEBUG') && DEBUG) var_dump(['$filename' => $filename]);
		trigger_error("Can't get merged contents from file", E_USER_WARNING);
		return(false);
	}
	
	$array = explode("\n", $contents);
	
	$result = [];
	foreach($array as $string) {
		$string = trim($string);
		$strlen = strlen($string);
		if($strlen > 0) {
			array_push($result, $string);
		}
	}
	
	return($result);
}

