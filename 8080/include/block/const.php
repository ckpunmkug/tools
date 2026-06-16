<?php

/// CSRF_TOKEN /////////////////////////////////////////////////////////////////

if(!(
	isset($_SESSION["csrf_token"]) 
	&& is_string($_SESSION["csrf_token"])
)) {
	$string = session_id() . uniqid('', true);
	$_SESSION["csrf_token"] = hash('sha256', $string);
}
define('CSRF_TOKEN', $_SESSION["csrf_token"]);

/// URL_PATH ///////////////////////////////////////////////////////////////////

if(@is_string($_SERVER["REQUEST_URI"]) != true) {
	trigger_error('Incorrect string $_SERVER["REQUEST_URI"]', E_USER_ERROR);
	exit(255);
}
$return = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if(!is_string($return)) {
	trigger_error('Parse url from $_SERVER["REQUEST_URI"] failed', E_USER_ERROR);
	exit(255);
}
define('URL_PATH', $return);

/// DEBUG //////////////////////////////////////////////////////////////////////

if(!(
	isset($_SESSION["debug"]) 
	&& is_string($_SESSION["debug"])
)) {
	$_SESSION["debug"] = "0";
}
if(
	isset($_GET["debug"]) 
	&& is_string($_GET["debug"])
) {
	if($_GET["debug"] == '1') {
		$_SESSION["debug"] = "1";
	}
	if($_GET["debug"] == '0') {
		$_SESSION["debug"] = "0";
	}
}
if($_SESSION["debug"] == '1') {
	define('DEBUG', true);
}

/// VERBOSE ////////////////////////////////////////////////////////////////////

if(!(
	isset($_SESSION["verbose"]) 
	&& is_string($_SESSION["verbose"])
)) {
	$_SESSION["verbose"] = "0";
}
if(
	isset($_GET["verbose"]) 
	&& is_string($_GET["verbose"])
) {
	if($_GET["verbose"] == '1') {
		$_SESSION["verbose"] = "1";
	}
	if($_GET["verbose"] == '0') {
		$_SESSION["verbose"] = "0";
	}
}
if($_SESSION["verbose"] == '1') {
	define('VERBOSE', true);
}

/// QUIET //////////////////////////////////////////////////////////////////////

if(!(
	isset($_SESSION["quiet"]) 
	&& is_string($_SESSION["quiet"])
)) {
	$_SESSION["quiet"] = "0";
}

if(
	isset($_GET["quiet"]) 
	&& is_string($_GET["quiet"])
) {
	if($_GET["quiet"] == '1') {
		$_SESSION["quiet"] = "1";
	}
	if($_GET["quiet"] == '0') {
		$_SESSION["quiet"] = "0";
	}
}

if($_SESSION["quiet"] === '1') {
	ini_set('error_reporting', NULL);
	ini_set('display_errors', '0');
	define("QUIET", true);
}
else {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', '1');
}

