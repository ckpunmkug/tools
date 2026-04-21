<?php

class Main
{
	static function initialization()
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
		define('URL_PATH', $return);
		
		////////////////////////////////////////////////////////////////
		
		session_set_cookie_params([
			'lifetime' => 3600,
			'path' => "/",
			'domain' => null,
			'secure' => false,
			'httponly' => true,
			'samesite' => 'Strict'
		]);
		session_start(['gc_maxlifetime' => 3600]);

		if(!(
			isset($_SESSION["csrf_token"]) 
			&& is_string($_SESSION["csrf_token"])
		)) {
			$string = session_id() . uniqid('', true);
			$_SESSION["csrf_token"] = hash('sha256', $string);
			unset($string);
		}
		define('CSRF_TOKEN', $_SESSION["csrf_token"]);
		
	}//}}}//

	static function switch_request_method(string $component)
	{//{{{
		
		$request_method = @strval($_SERVER["REQUEST_METHOD"]);
		switch($request_method) {
			case('GET'):
				$return = self::handle_get_request($component);
				if($return !== true) {
					http_response_code(500);
					trigger_error("Handle get request failed", E_USER_ERROR);
					exit(255);
				}
				exit(0);
			case('POST'):
				$return = self::handle_post_request($component);
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
	
	static function handle_get_request(string $component)
	{//{{{
		
		$page = 'index';
		$called_class = $component;
		$class_methods = get_class_methods($called_class);
		
		$PAGE = [];
		$pattern = '/^page_.+$/';
		foreach($class_methods as $class_method) {
			$return = preg_match($pattern, $class_method);
			if($return != 1) continue;
			array_push($PAGE, $class_method);
		}
		
		if(isset($_GET["page"]) && is_string($_GET["page"])) {
			$page = $_GET["page"];
		}
		
		$page = "page_{$page}";
		
		if(!in_array($page, $PAGE)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$page' => $page]);
			self::http_response_code_404();
			return(true);
		}
		 
		$call = "{$component}::{$page}";
		$return = $call();
		if($return !== true) {
			if(defined('DEBUG') && DEBUG) var_dump(['$page' => $page]);
			trigger_error("Can't generate page", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	
	}//}}}
	
	static function handle_post_request(string $component)
	{//{{{
	
		$csrf_token = @strval($_POST["csrf_token"]);
		if($csrf_token !== CSRF_TOKEN) {
			self::http_response_code_403();
			return(true);
		}
		
		if(!eval(Check::$string.='$_POST["action"]')) return(false);
		$action = $_POST["action"];
		
		$called_class = $component;
		$class_methods = get_class_methods($called_class);
		
		$ACTION = [];
		$pattern = '/^action_.+$/';
		foreach($class_methods as $class_method) {
			$return = preg_match($pattern, $class_method);
			if($return != 1) continue;
			array_push($ACTION, $class_method);
		}
		
		$action = "action_{$action}";		
		if(!in_array($action, $ACTION)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$action' => $action]);
			self::http_response_code_404();
			return(true);
		}
		 
		$call = "{$component}::{$action}";
		$return = $call();
		if($return !== true) {
			if(defined('DEBUG') && DEBUG) var_dump(['$action' => $action]);
			trigger_error("Action failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}
	
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

