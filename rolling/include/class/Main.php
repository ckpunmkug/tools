<?php

class Main
{
	function __construct()
	{//{{{//
		
		if(isset($_GET["debug"])) {
			define('DEBUG', true);
		}
		
		Main::define_URL_PATH();
		
		Main::define_CSRF_TOKEN();
		
		Main::switch_request_method();
		
	}//}}}//

	static function switch_request_method()
	{//{{{
		
		$request_method = @strval($_SERVER["REQUEST_METHOD"]);
		switch($request_method) {
			case('GET'):
				$return = self::handle_get_request();
				if($return !== true) {
					http_response_code(500);
					trigger_error("Handle get request failed", E_USER_ERROR);
					exit(255);
				}
				exit(0);
			case('POST'):
				$return = self::handle_post_request();
				if($return !== true) {
					http_response_code(500);
					trigger_error("Handle post request failed", E_USER_ERROR);
					exit(255);
				}
				exit(0);
			default:
				http_response_code(500);
				trigger_error("Unsupported http request method", E_USER_ERROR);
				exit(255);
		}
		
	}//}}}
	
	static function handle_get_request()
	{//{{{
		
		$PAGE = get_class_methods('Page');
		
		$page = 'index';
		if(isset($_GET["page"]) && is_string($_GET["page"])) {
			$page = $_GET["page"];
		}
		
		if(!in_array($page, $PAGE)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$page' => $page]);
			self::http_response_code_404();
			return(true);
		}
		 
		$call = "Page::{$page}";
		$return = $call();
		if($return !== true) {
			if(defined('DEBUG') && DEBUG) var_dump(['$page' => $page]);
			trigger_error("Can't generate page", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	
	}//}}}
	
	static function handle_post_request()
	{//{{{
	
		$csrf_token = @strval($_POST["csrf_token"]);
		if($csrf_token !== CSRF_TOKEN) {
			self::http_response_code_403();
			return(true);
		}
		
		if(!eval(Check::$string.='$_POST["action"]')) return(false);
		$action = $_POST["action"];
		
		$ACTION = get_class_methods('Action');
		
		if(!in_array($action, $ACTION)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$action' => $action]);
			self::http_response_code_404();
			return(true);
		}
		 
		$call = "Action::{$action}";
		$return = $call();
		if($return !== true) {
			if(defined('DEBUG') && DEBUG) var_dump(['$action' => $action]);
			trigger_error("Action failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}

	static function define_URL_PATH()
	{//{{{//
		
		if(@is_string($_SERVER["REQUEST_URI"]) != true) {
			trigger_error('Incorrect string $_SERVER["REQUEST_URI"]', E_USER_ERROR);
			exit(255);
		}
		
		$return = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		if(!is_string($return)) {
			trigger_error('Parse url from $_SERVER["REQUEST_URI"] failed', E_USER_ERROR);
			exit(255);
		}
		$URL_PATH = $return;
		
		$return = basename($URL_PATH);
		if($return != 'index.php') {
			$URL_PATH .= 'index.php';
		}
		
		if(defined('DEBUG') && DEBUG) {
			define('URL_PATH', "{$URL_PATH}?debug&");
		}
		else {
			define('URL_PATH', "{$URL_PATH}?");
		}
		
		return(NULL);

	}//}}}//

	static function define_CSRF_TOKEN()
	{//{{{//
		
		$lifetime = 86400;
		
		session_set_cookie_params([
			'lifetime' => $lifetime,
			'path' => "/",
			'domain' => null,
			'secure' => false,
			'httponly' => true,
			'samesite' => 'Strict'
		]);
		session_start(['gc_maxlifetime' => $lifetime]);
		
		if(!(
			isset($_SESSION["csrf_token"]) 
			&& is_string($_SESSION["csrf_token"])
		)) {
			$string = session_id() . uniqid('', true);
			$_SESSION["csrf_token"] = hash('sha256', $string);
		}
		define('CSRF_TOKEN', $_SESSION["csrf_token"]);
		
		return(NULL);
		
	}//}}}//

	static function http_response_code_403()
	{//{{{//
		
		http_response_code(403);
		HTML::$title = '403 Access forbidden!';
		HTML::$style = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
body {
	background: white;
	color: black;
}
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		HTML::$body = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<h1>Access forbidden!</h1>
<p>You don't have permission to access the requested object.</p>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		HTML::echo();
		return(true);
		
	}//}}}//
	
	static function http_response_code_404()
	{//{{{//
		
		http_response_code(404);
		HTML::$title = '404 Not Found';
		HTML::$style = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
body {
	background: white;
	color: black;
}
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		HTML::$body = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		HTML::echo();
		return(true);
		
	}//}}}//
}

