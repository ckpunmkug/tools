<?php

class Main
{
	function __construct()
	{//{{{//
		
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

