<?php

$lifetime = 1*60*60*24;

session_set_cookie_params([
	'lifetime' => $lifetime,
	'path' => "/",
	'domain' => NULL,
	'secure' => false,
	'httponly' => true,
	'samesite' => 'Strict'
]);

$return = getenv('session_save_path', true);
if(!is_string($return)) {
	trigger_error("Environment variable 'session_save_path' is not set", E_USER_ERROR);
	exit(255);
}
session_save_path($return);

session_start(['gc_maxlifetime' => $lifetime]);

