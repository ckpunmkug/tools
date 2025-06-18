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
					trigger_error("Handle get request failed", E_USER_ERROR);
					exit(255);
				}
				exit(0);
			case('POST'):
				$return = $this->handle_post_request();
				if($return !== true) {
					trigger_error("Handle post request failed", E_USER_ERROR);
					exit(255);
				}
				exit(0);
			default:
				trigger_error("Unsupported http request method", E_USER_ERROR);
				exit(255);
		}
	}//}}}
	
	function handle_get_request()
	{//{{{
		echo(CSRF_TOKEN);
		return(true);
	}//}}}
	
	function handle_post_request()
	{//{{{
		if(!(
			eval(C::$S.='$_POST["csrf_token"]') === true
			&& strcmp($_POST["csrf_token"], CSRF_TOKEN) === 0
		)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$_POST["csrf_token"]' => $_POST["csrf_token"]]);
			trigger_error("Incorrect 'csrf token' string", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(C::$S.='$_POST["component"]')) return(false);
		if(!eval(C::$S.='$_POST["action"]')) return(false);
		if(!eval(C::$S.='$_POST["data"]')) return(false);
		
		$data = base64_decode($_POST["data"], true);
		if(!is_string($data)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$_POST["data"]' => $_POST["data"]]);
			trigger_error("Can't decode incomig data from base64", E_USER_WARNING);
			return(false);
		}
		
		switch($_POST["component"]) {
			case('duckduckgo'):
				
				require_once('component/duckduckgo.php');
				if(!eval(C::$A.='CONFIG["component"]["duckduckgo"]')) {
					trigger_error("Incorrect 'duckduckgo' configuration array", E_USER_WARNING);
					return(false);
				}
				try {
					$object = new duckduckgo(CONFIG["component"]["duckduckgo"], $_POST["action"], $data);
				}
				catch(Exception $Exception) {
					trigger_error($Exception->getMessage(), E_USER_WARNING);
					return(false);
				}
				return(true);
				
			default:
				if (defined('DEBUG') && DEBUG) var_dump(['$_POST["component"]' => $_POST["component"]]);
				trigger_error("Unsupported 'component'", E_USER_WARNING);
				return(false);
		}
	}//}}}
}

