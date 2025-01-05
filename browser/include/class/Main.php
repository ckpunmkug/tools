<?php

class Main
{//{{{//

	function __construct()
	{//{{{
		$request_method = @strval($_SERVER["REQUEST_METHOD"]);
		switch($request_method) {
			case('GET'):
				$return = $this->handle_get_request();
				if($return !== true) {
					trigger_error("Handle get request failed", E_USER_ERROR);
				}
				exit(0);
			case('POST'):
				$return = $this->handle_post_request();
				if($return !== true) {
					trigger_error("Handle post request failed", E_USER_ERROR);
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
		$action = @strval($_POST["action"]);
		switch($action) {
			case('test'):
				$return = $this->test();
				if($return !== true) {
					trigger_error("Can't perform 'test' action", E_USER_WARNING);
					return(false);					
				}
				return(true);
			default:
				trigger_error("Unsupported 'action'", E_USER_WARNING);
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

}//}}}//

