<?php

if(PHP_SAPI == 'cli') return(true);

$HTTP_SEC_FETCH_SITE = '';
if (isset($_SERVER["HTTP_SEC_FETCH_SITE"]) && is_string($_SERVER["HTTP_SEC_FETCH_SITE"])) {
	$HTTP_SEC_FETCH_SITE = $_SERVER["HTTP_SEC_FETCH_SITE"];
}

if (!($HTTP_SEC_FETCH_SITE === 'same-origin' || $HTTP_SEC_FETCH_SITE === 'none')) {
	http_response_code(403);
	exit(255);
}

header("Content-Security-Policy: frame-ancestors 'self';");

$is_localhost = (
	preg_match('/^(.+\.localhost|localhost)$/', $_SERVER['HTTP_HOST']) === 1 
	|| $_SERVER['HTTP_HOST'] === '127.0.0.1'
);

session_set_cookie_params([
	'lifetime' => 3600,
	'path' => "/",
	'domain' => null,
	'secure' => !$is_localhost,
	'httponly' => true,
	'samesite' => 'Strict'
]);

session_start();

if (!isset($_SESSION["csrf_token"]) || !is_string($_SESSION["csrf_token"])) {
	$string = session_id() . uniqid('', true);
	$_SESSION["csrf_token"] = hash('sha256', $string);
}

define('CSRF_TOKEN', $_SESSION["csrf_token"]);

