<?php

$return = getenv('HOME', true);
if(!is_string($return)) {
	trigger_error("Environment variable 'HOME' is not set", E_USER_ERROR);
	exit(255);
}
$return = realpath($return);
if(!is_string($return)) {
	trigger_error("Unable to get real path of 'HOME' folder", E_USER_ERROR);
	exit(255);
}
define('HOME', $return);

require('project/config.php');

require('class/Check.php');
require('class/FileSystem.php');
require('function/launch.php');
require('function/encode.php');
require('function/decode.php');

require('class/PHPDebugger.php');

