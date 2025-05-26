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
	
		if(!eval(C::$S.='$_GET["component"]')) return(false);
		$component = $_GET["component"];
		
		if(!eval(C::$S.='$_GET["action"]')) return(false);
		$action = $_GET["action"];
		
		if(!eval(C::$S.='$_POST["data"]')) return(false);
		$data = $_POST["data"];
		
		switch($component) {
			case('duckduckgo'):
				require_once('component/duckduckgo.php');
				
				if(!eval(C::$A.='CONFIG["component"]["duckduckgo"]')) {
					trigger_error("Incorrect config for 'duckduckgo' component", E_USER_WARNING);
					return(false);
				}
				$config = CONFIG["component"]["duckduckgo"];
				
				try {
					$object = new duckduckgo($config, $action, $data);
				}
				catch(Exception $Exception) {
					trigger_error($Exception->getMessage(), E_USER_WARNING);
					return(false);
				}
				
				return(true);
			default:
				if (defined('DEBUG') && DEBUG) var_dump(['$component' => $component]);
				trigger_error("Unsupported 'component'", E_USER_WARNING);
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
}

