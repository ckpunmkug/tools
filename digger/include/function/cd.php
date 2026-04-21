<?php

// Usage
/*{{{
$STRING = [];
for($i = 100; $i < 105; $i += 1) {
	$STRING[$i] = '';
}

$cd = count($STRING);
foreach($STRING as $key => $string) {
	if(!cd($cd, $key, 3)) continue;
	sleep(1);
}
}}}*/

function cd(int &$cd, $key = '', int $start = -1) // countdown
{
	if(!(defined('VERBOSE') && VERBOSE)) return(true);
	
	$cd -= 1; 
	if($key !== '') $key = strval($key).' ';
	if($start !== -1 && $start < $cd) return(false);
	
	$string = sprintf("\r%05d {$key}", $cd);
	//echo($string);
	file_put_contents('php://stderr', $string);
	
	//if($cd == 0) echo("\n");
	if($cd == 0) file_put_contents('php://stderr', "\n");
	
	return(true);
}

