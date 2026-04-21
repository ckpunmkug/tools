<?php

$return = Database::open(PATH["database"]);
if(!$return) {
	trigger_error("Can't open database from file", E_USER_ERROR);
	exit(255);
}

class data
{
	static $create = [];
	static $drop = [];
	static $add = [];
	static $get = [];
	static $update = [];
	static $delete = [];
}

require(__DIR__.'/PHP_FILE.php');
require(__DIR__.'/SEARCH_QUERY.php');
require(__DIR__.'/SEARCH_RESULTS.php');
require(__DIR__.'/TEST_SOURCE.php');

