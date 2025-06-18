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
	$action = 'reset';
	$data = [
		"query" => "query00",
		"queries" => [
			"query01",
			"query02",
		],
		"results" => [
			[
			"url" => 'url00.0',
			"title" => 'title00.0',
			"description" => 'description00.0',
			],
			[
			"url" => 'url00.1',
			"title" => 'title00.1',
			"description" => 'description00.1',
			],
		],
	];
	duckduckgo::main($config, $action, $data);
}//}}}//

