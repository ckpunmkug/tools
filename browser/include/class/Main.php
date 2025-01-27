<?php

class Main
{

	function __construct()
	{//{{{
		$request_method = @strval($_SERVER["REQUEST_METHOD"]);
		switch($request_method) {
			case('GET'):
				$return = $this->handle_get_request();
				if($return !== true) {
					http_response_code(500);
					trigger_error("Handle get request failed", E_USER_ERROR);
					exit(255);
				}
				exit(0);
			case('POST'):
				$return = $this->handle_post_request();
				if($return !== true) {
					http_response_code(500);
					trigger_error("Handle post request failed", E_USER_ERROR);
					exit(255);
				}
				exit(0);
			default:
				trigger_error("Unsupported http request method", E_USER_ERROR);
		}
	}//}}}
	
	function handle_get_request()
	{//{{{
		
		echo(CSRF_TOKEN);
		return(true);
		
	}//}}}
	
	function handle_post_request()
	{//{{{
		$json = file_get_contents('php://input');
		if(!is_string($json)) {
			trigger_error("Can't get contents from `php input`", E_USER_WARNING);
			return(false);
		}
		
		$array = json_decode($json, true);
		if(!is_array($array)) {
			trigger_error("Can't decode json into POST array", E_USER_WARNING);
			return(false);
		}
		$_POST = $array; unset($array);
		
		$token = @strval($_POST["token"]);
		if($token !== CSRF_TOKEN) {
			trigger_error("Incorrect `csrf_token` passed", E_USER_WARNING);
			return(false);
		}
	
		$component = @strval($_POST["component"]);
		switch($component) {
			case('parser'):
				require_once('component/Parser.php');
				$return = Parser::action();
				if($return !== true) {
					trigger_error("Can't `parser` action failed", E_USER_WARNING);
					return(false);					
				}
				return(true);
			default:
				trigger_error("Unsupported `component`", E_USER_WARNING);
				return(false);
		}
	}//}}}

	function test()
	{//{{{
		$csrf_token = @strval($_POST['csrf_token']);
		if(strcmp($this->csrf_token, $csrf_token) !== 0) {
			trigger_error("Compare csrf_tokens failed", E_USER_ERROR);
		}
		
		$url_path = htmlentities($this->url_path);
		
		HTML::$body .=
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<fieldset>
	<legend>Post test action</legend>
	<a href="{$url_path}"><button>To main</button></a>
</fieldset>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		
		return(true);
	}//}}}

}

