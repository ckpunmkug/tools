<?php

if(PHP_SAPI == 'cli') return(true);

if(!(
	isset($_SERVER["HTTP_SEC_FETCH_SITE"])
	&& is_string($_SERVER["HTTP_SEC_FETCH_SITE"])
)) {
	trigger_error('Incorrect $_SERVER["HTTP_SEC_FETCH_SITE"] string', E_USER_ERROR);
	exit(255);
}

if(!(
	$_SERVER["HTTP_SEC_FETCH_SITE"] == 'same-origin'
	|| $_SERVER["HTTP_SEC_FETCH_SITE"] == 'none'
)) {
	http_response_code(403);
	//trigger_error("Disallowed 'Sec-Fetch-Site' value", E_USER_ERROR);
	$html =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<!DOCTYPE html>
<html lang="{$language}">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
	</head>
	<body>
Automatic opening of the page was prevented. <a href=""><button autofocus>Refresh</button></a>
	</body>
</html>
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	echo($html);
	exit(255);
}

header("Content-Security-Policy: frame-ancestors 'self';");
	
session_start([
	'cookie_samesite' => 'Strict'
]);

if(@is_string($_SESSION["csrf_token"]) != true) {
	$string = session_id() . uniqid(); 
	$_SESSION["csrf_token"] = md5($string);
}

define('CSRF_TOKEN', $_SESSION["csrf_token"]);

