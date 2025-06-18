<?php // network.proxy.no_proxies_on = 127.0.0.1:8080

define('VENDOR', 'ckpunmkug');
define('PROJECT', 'browser');

if(true) // Security
{//{{{//

	if(!(isset($_SERVER["HTTP_SEC_FETCH_SITE"]) && is_string($_SERVER["HTTP_SEC_FETCH_SITE"]))) {
		trigger_error('Incorrect $_SERVER["HTTP_SEC_FETCH_SITE"] string', E_USER_ERROR);
		exit(255);
	}
	if(!($_SERVER["HTTP_SEC_FETCH_SITE"] == 'same-origin' || $_SERVER["HTTP_SEC_FETCH_SITE"] == 'none')) {
		http_response_code(403);
		trigger_error("Disallowed 'Sec-Fetch-Site' value", E_USER_ERROR);
		exit(255);
	}
	
	$PHP_AUTH_USER = getenv('PHP_AUTH_USER', true);
	$PHP_AUTH_PW = getenv('PHP_AUTH_PW', true);
	if(!(is_string($PHP_AUTH_USER) && is_string($PHP_AUTH_PW))) {
		trigger_error("Can't get 'PHP_AUTH_USER', 'PHP_AUTH_PW' environments", E_USER_ERROR);
		exit(255);
	}
	if(!(
		is_string($_SERVER["PHP_AUTH_USER"]) && is_string($_SERVER["PHP_AUTH_PW"])
		&& $_SERVER["PHP_AUTH_USER"] == $PHP_AUTH_USER && $_SERVER["PHP_AUTH_PW"] == $PHP_AUTH_PW
	)) {
		http_response_code(401);
		header('WWW-Authenticate: Basic realm="Access to the php built-in web server", charset="UTF-8"');
		exit(0);
	}
	
	header("Content-Security-Policy: frame-ancestors 'self';");
	
	session_start(['cookie_samesite' => 'Strict']);
	if(@is_string($_SESSION["csrf_token"]) != true) {
		$string = session_id() . uniqid(); 
		$_SESSION["csrf_token"] = md5($string);
	}
	define('CSRF_TOKEN', $_SESSION["csrf_token"]);
	
}//}}}//

if(true) // Request method
{//{{{//

	if(!(isset($_SERVER["REQUEST_METHOD"]) && is_string($_SERVER["REQUEST_METHOD"]))) {
		trigger_error('Incorrect $_SERVER["REQUEST_METHOD"] string', E_USER_ERROR);
		exit(255);
	}
	
	if(!($_SERVER["REQUEST_METHOD"] == 'POST' || $_SERVER["REQUEST_METHOD"] == 'GET')) {
		trigger_error('Incorrect $_SERVER["REQUEST_METHOD"] value', E_USER_ERROR);
		exit(255);
	}
	
}//}}}//

if(true) // Basic initialization
{//{{{//
	
	if(PHP_SAPI == 'cli-server') {
		file_put_contents('php://stderr', "\n");
	}
	
	if(defined('QUIET') && QUIET === true) {
		ini_set('error_reporting', 0);
		ini_set('display_errors', false);
	}
	else {
		ini_set('error_reporting', E_ALL );
		
		if(PHP_SAPI == 'cli') ini_set('display_errors', false);
		else ini_set('display_errors', true);
		
		ini_set('html_errors', false);
	}

	if(@is_string($_SERVER["REQUEST_URI"]) !== true) {
		trigger_error('Incorrect string $_SERVER["REQUEST_URI"]', E_USER_ERROR);
	}
	$return = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	if(!is_string($return)) {
		trigger_error('Parse url from $_SERVER["REQUEST_URI"] failed', E_USER_ERROR);
	}
	define('URL_PATH', $return);
	
}//}}}//

if(true) // Load config
{//{{{
	
}//}}}

if(true) // Basic includes
{//{{{

	set_include_path(__DIR__.'/../include');
	require_once('class/C.php');

	require_once('class/Data.php');
	$data_file = HOME.'/.cache/'.VENDOR.'/'.PROJECT.'/data.sqlite';
	$return = Data::open($data_file);
	if(!$return) {
		trigger_error("Can't open sqlite database file", E_USER_ERROR);
		exit(255);
	}

}//}}}

if($_SERVER["REQUEST_METHOD"] == 'GET')
{
	
	if(URL_PATH == '/favicon.ico')
	{//{{{//
		
		$contents = file_get_contents(__DIR__.'/../share/image/favicon.ico');
		if(!is_string($contents)) {
			trigger_error("Can't get contents from 'favicon.ico' file", E_USER_ERROR);
			exit(255);
		}
		
		header('Content-Type: image/vnd.microsoft.icon');
		echo($contents);
		
		exit(0);
		
	}//}}}//
	
	var_dump(URL_PATH);
	exit(0);
}

if($_SERVER["REQUEST_METHOD"] == 'POST')
{
	if(true) // Get parameters
	{//{{{//
		
		if(!eval(C::$S.='$_POST["csrf_token"]')) return(false);
		if($_POST["csrf_token"] !== CSRF_TOKEN) {
			trigger_error('Incorrect $_POST["csrf_token"] value', E_USER_ERROR);
			exit(255);
		}
		
		if(!eval(C::$S.='$_POST["component"]')) return(false);
		$component = $_POST["component"];
		
		if(!eval(C::$S.='$_POST["action"]')) return(false);
		$action = $_POST["action"];
		
		if(!eval(C::$S.='$_POST["data"]')) return(false);
		$data = $_POST["data"];
		
	}//}}}//
	
	if($component == 'duckduckgo') 
	{//{{{//
	
		require_once('component/duckduckgo.php');
		
		if(!eval(C::$A.='CONFIG["component"]["duckduckgo"]')) {
			trigger_error("Incorrect 'duckduckgo' configuration array", E_USER_WARNING);
			return(false);
		}
		
		try {
			$object = new duckduckgo(CONFIG["component"]["duckduckgo"], $action, $data);
		}
		catch(Exception $Exception) {
			trigger_error($Exception->getMessage(), E_USER_ERROR);
			exit(255);
		}
		
		exit(0);
		
	}//}}}//
	
	exit(0);
}

