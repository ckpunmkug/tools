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
		$component = '';
		if(@is_string($_GET["component"])) {
			$component = $_GET["component"];
		}
		switch($component) {
			case(''):
				$return = $this->index();
				if($return !== true) {
					trigger_error("Can't create 'index' page", E_USER_WARNING);
					return(false);
				}
				return(true);
			case('fs'):
				require_once('component/fs.php');
				$return = fs::page();
				if($return !== true) {
					trigger_error("The 'fs' component can't create page", E_USER_WARNING);
					return(false);
				}
				return(true);
			/*
			case('test'):
				require_once('component/test.php');
				$return = test::page();
				if($return !== true) {
					trigger_error("The 'test' component can't create page", E_USER_WARNING);
					return(false);
				}
				return(true);
			*/
			default:
				trigger_error("Unsupported 'component'", E_USER_WARNING);
				return(false);
		}
	}//}}}
	
	function handle_post_request()
	{//{{{
		$component = '';
		if(@is_string($_POST["component"])) {
			$component = $_POST["component"];
		}
		switch($component) {
			case(''):
				$return = $this->ajax();
				if($return !== true) {
					trigger_error("Can't create 'ajax' page", E_USER_WARNING);
					return(false);
				}
				return(true);
			/*
			case('test'):
				require_once('component/test.php');
				$return = test::action();
				if($return !== true) {
					trigger_error("The 'test' component can't perform action", E_USER_WARNING);
					return(false);
				}
				return(true);
			*/
			default:
				trigger_error("Unsupported 'component'", E_USER_WARNING);
				return(false);
		}
	}//}}}
	
	function index()
	{//{{{	
		$url_path = URL_PATH;
		$csrf_token = CSRF_TOKEN;
		
		HTML::$body .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<a href="{$url_path}?component=fs"><button>FS</button></a>
<!--
<form src="{$url_path}" method="post">
	<input name="csrf_token" value="{$csrf_token}" type="hidden" />
	<label>String
		<input name="string" value="" type="text" />
	</label>
	<button type="submit">Send</button>
</form>
-->

HEREDOC;
////////////////////////////////////////////////////////////////////////////////

		return(true);
	}//}}}

	function ajax()
	{//{{{
		$csrf_token = @strval($_POST["csrf_token"]);
		if(strcmp($csrf_token, CSRF_TOKEN) !== 0) {
			trigger_error("Compare csrf_tokens failed", E_USER_ERROR);
			exit(255);
		}
		
		$string = @strval($_POST["string"]);
		HTML::$body .=
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<h1>{$string}</h1>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		
		return(true);
	}//}}}

}

