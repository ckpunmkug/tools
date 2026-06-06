<?php

require('class/HTML.php');

$session_id = session_id();

var_dump([
	"session_save_path" => session_save_path(),
	"CSRF_TOKEN" => CSRF_TOKEN,
	"DEBUG" => defined('DEBUG') ? DEBUG : NULL,
	"VERBOSE" => defined('VERBOSE') ? VERBOSE : NULL,
	"QUIET" => defined('QUIET') ? QUIET : NULL,
]);

if(defined('VERBOSE') && VERBOSE) {
	trigger_error("Test verbose", E_USER_NOTICE);
}

if(defined('DEBUG') && DEBUG) var_dump(['DEBUG' => DEBUG]);
trigger_error("Test warning", E_USER_WARNING);

trigger_error("Test error", E_USER_ERROR);
exit(255);

