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
		$component = '';
		if(@is_string($_GET["component"])) {
			$component = $_GET["component"];
		}
		switch($component) {
			case(''):
				$return = $this->main();
				if($return !== true) {
					trigger_error("Can't create 'main' page", E_USER_WARNING);
					return(false);
				}
				return(true);
			case('Calibration'):
				require_once('component/Calibration.php');
				$return = Calibration::main();
				if($return !== true) {
					trigger_error("Main call in 'Calibration' failed", E_USER_WARNING);
					return(false);
				}
				return(true);
			default:
				trigger_error("Unsupported 'component'", E_USER_WARNING);
				return(false);
		}
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
	
	function main()
	{//{{{
	
		HTML::$styles = [
			'share/style/bootstrap.css',
			'share/style/bootstrap-theme.css',
		];
	
		HTML::$scripts = [
			'share/script/jquery.js',
			'share/script/bootstrap.js',
		];
		
		HTML::$style .= 
/////////////////////////////////////////////////////////////////
<<<HEREDOC
@font-face
	{
		font-family: 'hack';
		src: url("share/font/hack.woff2");
	}
*
	{
		font-family: 'hack';
		font-size: 16px;
	}
.container
	{
		border: solid 1px black;
		width: 800px;
	}
HEREDOC;
/////////////////////////////////////////////////////////////////
	
		HTML::$body .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<div class="container">
	<form>
		<div class="form-group">
			<label for="input-00">URL</label>
			<input name="url" value="http://" type="text" id="input-00" />
		</div>
	</form>
</div>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

		return(true);
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

