<?php

set_include_path(__DIR__.'/../include');
require_once('class/C.php');
require_once('class/Data.php');
require_once('component/duckduckgo.php');

$s = '/tmp/data.sqlite';
$r = Data::open($s);
if(!$r) {
	trigger_error("Can't open sqlite database file", E_USER_ERROR);
	exit(255);
}

if(true) // zero
{//{{{//
	$config = [];
	$config["table"]["query"] = '/duckduckgo/test/query';
	$config["table"]["result"] = '/duckduckgo/test/result';
	
	$action = 'get_queries';
	$data = [];
	$return = duckduckgo::main($config, $action, $data);
	if(!$return) {
		trigger_error("Can't get queries", E_USER_ERROR);
		exit(255);
	}
	$ARRAY = duckduckgo::$output;
	foreach($ARRAY as $array) {
		foreach($array as $key => $value) {
			echo("{$key} = {$value}\n");
		} 
		echo("\n");
	}
	
	$action = 'get_results';
	$data = [];
	$return = duckduckgo::main($config, $action, $data);
	if(!$return) {
		trigger_error("Can't get results", E_USER_ERROR);
		exit(255);
	}
	$ARRAY = duckduckgo::$output;
	foreach($ARRAY as $array) {
		foreach($array as $key => $value) {
			echo("{$key} = {$value}\n");
		} 
		echo("\n");
	}
	
}//}}}//

