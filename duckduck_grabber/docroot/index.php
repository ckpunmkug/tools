<?php

define('VERBOSE', false);

$realpath = realpath(__DIR__.'/..');
if(!is_string($realpath)) {
	trigger_error("Unable to get real path of project root folder", E_USER_ERROR);
	exit(255);
}
define('DIR', $realpath);

set_include_path(DIR.'/include');
require('block/initialization.php');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
ini_set('html_errors', '0');

require('class/HTML.php');
require('function/t2h.php');
require('function/layout_form.php');

require('data/class.php');
if(file_exists(PATH["database"])) {
	$return = Database::open(PATH["database"]);
	if(!$return) {
		trigger_error("Can't open database from file", E_USER_ERROR);
		exit(255);
	}
}

require('project/Page.php');
require('project/Action.php');
require('project/Method.php');

require('class/Main.php');
new Main;

