<?php

if(defined('QUIET') && QUIET === true) {
	ini_set('error_reporting', 0);
	ini_set('display_errors', '0');
}
else {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', '1');
	ini_set('html_errors', '0');
}

if(PHP_SAPI == 'cli') {
	$_SERVER["REQUEST_URI"] = '';
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', '0');
}

if(@is_string($_SERVER["REQUEST_URI"]) !== true) {
	trigger_error('Incorrect string $_SERVER["REQUEST_URI"]', E_USER_ERROR);
	exit(255);
}

$return = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if(!is_string($return)) {
	trigger_error('Parse url from $_SERVER["REQUEST_URI"] failed', E_USER_ERROR);
	exit(255);
}
define('URL_PATH', $return);

$return = DB::open(DB["host"], DB["user"], DB["password"], DB["database"]);
if(!$return) {
	trigger_error("Can't connect to database", E_USER_ERROR);
	exit(255);
}

