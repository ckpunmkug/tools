<?php

define('DEBUG', true);
define("QUIET", false);

switch(PHP_SAPI)
{	
	case('cli'):
	define('VERBOSE', true);
	require('block/cli.php');
	break;
	
	case('cli-server'):
	define('VERBOSE', false);
	require('block/server.php');
	break;

	default:
	if(defined('DEBUG') && DEBUG) var_dump(['PHP_SAPI' => PHP_SAPI]);
	trigger_error("Unsupported 'PHP_SAPI'", E_USER_ERROR);
	exit(255);
}

require('class/Check.php');
require('function/launch.php');
require('function/encode.php');
require('function/decode.php');
require('function/export.php');
require('function/import.php');

require('class/SQLite.php');
require('class/FileSystem.php');

__halt_compiler();

