<?php

$return = getenv('USER', true);
if(!is_string($return)) {
	trigger_error("Environment variable 'USER' is not set", E_USER_ERROR);
	exit(255);
}
$user = $return;

$return = getenv('PASSWORD', true);
if(!is_string($return)) {
	trigger_error("Environment variable 'PASSWORD' is not set", E_USER_ERROR);
	exit(255);
}
$password = $return;

$apache_request_headers = apache_request_headers();
$return = array_key_exists('Authorization', $apache_request_headers);
if(!$return) {
	http_response_code(401);
	header('WWW-Authenticate: Basic realm="Access to the php built-in web server", charset="UTF-8"');
	exit(0);
}
	
$pattern = '/^([^\s]+)\s+([^\s]+)$/';
$string = $apache_request_headers['Authorization'];
$return = preg_match($pattern, $string, $MATCH);
if ($return !== 1) {
	trigger_error("Can't parse 'Authorization' header", E_USER_ERROR);
	exit(255);
}
$type = $MATCH[1];
$credentials = $MATCH[2];
	
if (strcmp($type, 'Basic') !== 0) {
	trigger_error("Type of 'Authorization' is not 'Basic'", E_USER_ERROR);
	exit(255);
}
	
$return = base64_encode("{$user}:{$password}");
if(strcmp($return, $credentials) !== 0) {
	http_response_code(401);
	header('WWW-Authenticate: Basic realm="Access to the php built-in web server", charset="UTF-8"');
	exit(0);
}

unset($return, $user, $password, $apache_request_headers, $pattern, $string, $MATCH, $type, $credentials);

