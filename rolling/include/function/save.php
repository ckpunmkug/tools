<?php

function save(string $filename, array $array)
{
	$contents = '';
	foreach($array as $string) {
		$string = strval($string);
		$string = trim($string);
		if($string == '') continue;
		
		if($contents != '') $contents .= "\n";
		$contents .= $string;
	}
	
	$return = file_put_contents($filename, $contents);
	if(!is_int($return)) {
		if(defined('DEBUG') && DEBUG) var_dump(['$filename' => $filename]);
		trigger_error("Can't put merged contents to file", E_USER_WARNING);
		return(false);
	}
	
	return(true);
}

