<?php 

class Initialization
{//{{{//

	function __construct()
	{//{{{//
	
		if(PHP_SAPI == 'cli-server') {
			file_put_contents('php://stderr', "\n");
		}
		
		$this->security();
		
		$this->define();
		
		$this->ini_set();
		
	}//}}}//
	
	function __destruct()
	{//{{{//
	
		if(PHP_SAPI == 'cli-server') {
			file_put_contents('php://stderr', "\n");
		}
		
	}//}}}//
	
	function security()
	{//{{{//
	
		header("Content-Security-Policy: frame-ancestors none;");

		session_start();
		if(@is_string($_SESSION["csrf_token"]) != true) {
			$string = session_id() . uniqid(); 
			$_SESSION["csrf_token"] = md5($string);
		}
		define('CSRF_TOKEN', $_SESSION["csrf_token"]);
		
	}//}}}//
	
	function define()
	{//{{{//
	
		define('DEBUG', true);
		define('VERBOSE', true);
		
		if(@is_string($_SERVER["REQUEST_URI"]) !== true) {
			trigger_error('Incorrect string $_SERVER["REQUEST_URI"]', E_USER_ERROR);
			exit(255);
		}
		$return = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		if(!is_string($return)) {
			trigger_error('Parse url from $_SERVER["REQUEST_URI"] failed', E_USER_ERROR);
			exit(255);
		}
		define('URL_PATH', $return);
		
	}//}}}//
	
	function ini_set()
	{//{{{//
	
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', true);
		ini_set('html_errors', false);
		
	}//}}}//

}//}}}//

class HTML
{//{{{

	static $head = "";
	static $title = "";
	static $styles = [];
	static $style = "";
	static $scripts = [];
	static $script = "";
	static $body = "";
	
	function __construct()
	{//{{{
		ob_start(function($buffer) {
			$buffer = htmlentities($buffer);
			$buffer = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
	<head>
	<body><pre>{$buffer}</pre></body>
</html>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
			return($buffer);
		});
	}//}}}
	
	function __wakeup()
	{//{{{
		trigger_error("Can't unserialize this class", E_USER_ERROR);
		exit(255);
	}//}}}
	
	function __destruct()
	{//{{{
		$buffer = ob_get_contents();
		ob_end_clean();
		$buffer = htmlentities($buffer);
		
		if(!empty($buffer)) {
			HTML::$body = "<pre>{$buffer}</pre>".HTML::$body;
		}
		
		$html = HTML::generate_html();
		echo($html);
	}//}}}
	
	static function generate_stylesheets(array $styles)
	{//{{{
		$result = "";
		foreach($styles as $style) {
			if(!is_string($style)) continue;
			$result .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<link rel="stylesheet" href="{$style}" />

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}
		return($result);
	}//}}}
	
	static function generate_scripts(array $scripts)
	{//{{{
		$result = "";
		foreach($scripts as $script) {
			if(!is_string($script)) continue;
			$result .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<script src="{$script}"></script>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}
		return($result);
	}//}}}

	static function generate_html()
	{//{{{
		$head = &self::$head;
		$title = &self::$title;
		$stylesheets = self::generate_stylesheets(self::$styles);
		$style = &self::$style;
		$scripts = self::generate_scripts(self::$scripts);
		$script = &self::$script;
		$body = &self::$body;
		$html = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
{$head}
		<title>{$title}</title>
{$stylesheets}
		<style>
{$style}
		</style>
{$scripts}
		<script>
{$script}
		</script>
	</head>
	<body>
{$body}
	</body>
</html>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		return($html);
	}//}}}
	
}//}}}

class Main
{//{{{

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
		$page = '';
		if(@is_string($_GET["page"]) == true) {
			$page = $_GET["page"];
		}
		switch($page) {
			case(''):
				$return = $this->main();
				if($return !== true) {
					trigger_error("Can't create 'main' page", E_USER_WARNING);
					return(false);
				}
				return(true);
			default:
				trigger_error("Unsupported 'page'", E_USER_WARNING);
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
		
		HTML::$style .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
div[name='container'] {
	position: absolute;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
}
div[name='header'] {
	position: absolute;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 30px;
	background: none;
}
button.tab {
	height: 30px;
	border-top: solid 1px #4C0;
	border-bottom: solid 1px #000;
	border-left: solid 3px #4C0;
	border-right: solid 3px #4C0;
	border-radius: 2px;
}
div[name='body'] {
	position: absolute;
	left: 0px;
	top: 29px;
	width: 100%;
	height: calc(100% - 29px);
	background: none;
	border-top: solid 1px #4C0;
}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div name="container">
	<div name="body">
EDITOR
	</div>
	<div name="header">
		&nbsp;
		<button class="tab">editor</button>
		<button name="fullscreen" class="button">F</button>
		<button name="console" class="button">C</button>
	</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		HTML::$script .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
var div, button;

function windowOnLoad()
{//{{{//

	div = document.querySelector("div[name='container']");
	button = document.querySelector("button[name='fullscreen']");
	
	button.addEventListener("click", function() {
		if(div.requestFullscreen) {
			div.requestFullscreen();
		}
		else {
			alert("gepbMO!");
		}
	});
	
}//}}}//

window.addListener("load", windowOnLoad);

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		return(true);
	}//}}}

}//}}}

function main()
{//{{{//

	$CLASS = [];
	
	array_push($CLASS, new Initialization());
	
	array_push($CLASS, new HTML());
	
	$string = file_get_contents(__FILE__);
	$offset = __COMPILER_HALT_OFFSET__;
		
	$favicon = substr($string, $offset, 264);
	$terminus = substr($string, ($offset+264), 17980);
		
	HTML::$head .= // favicon 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<link rel="icon" href="data:image/x-icon;base64,{$favicon}">
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

	HTML::$style .= //terminus
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
@font-face {
	font-family: 'Terminus';
	src: url(data:font/truetype;base64,{$terminus});
}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//	
	
	HTML::$title = 'Shell';
	
	HTML::$style .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
* {
	font-family: 'Terminus';
	font-size: 20px;
	line-height: 20px;
	outline: none;
	border: none;
	background-color: #000;
	color: #4C0;
}
::selection	{
	background-color: #4C0;
	color: #000;
}
body {
	margin: 0px;
	padding: 0px;
}
button.button {
	margin: 2px;
	background: #000;
	color: #4C0;
	border-top: solid 1px #4C0;
	border-bottom: solid 1px #4C0;
	border-left: solid 3px #4C0;
	border-right: solid 3px #4C0;
	border-radius: 2px;
}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	HTML::$script .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
function windowOnLoad(event)
{//{{{//
	
}//}}}//
window.addEventListener("load", windowOnLoad);

function windowOnKeyDown(event)
{//{{{//
	/*
	if (event.ctrlKey === false && event.altKey === false && event.shiftKey === false) {
		event.preventDefault();
		console.log("keyCode: ", event.keyCode);
		return(null);
	}
	*/
}//}}}//
window.addEventListener("keydown", windowOnKeyDown);

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	array_push($CLASS, new Main());
	
	return(true);
	
}//}}}//

main();

// favicon.ico : 264
// terminus.ttf : 17980
__halt_compiler();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bGVkMSA6IDUtMi0yMDE5AABVAG4AdABpAHQAbABlAGQAMQAAVW50aXRsZWQxAABWAGUAcgBzAGkAbwBuACAAMAAwADEALgAwADAAMAAgAABWZXJzaW9uIDAwMS4wMDAgAABVAG4AdABpAHQAbABlAGQAMQAAVW50aXRsZWQxAAAAAAACAAAAAAAA/4UAMAAAAAEAAAAAAAAAAAAAAAAAAAAAAKgAAAABAAIBAgADAAQABQAGAAcACAAJAAoACwAMAA0ADgAPABAAEQASABMAFAAVABYAFwAYABkAGgAbABwAHQAeAB8AIAAhACIAIwAkACUAJgAnACgAKQAqACsALAAtAC4ALwAwADEAMgAzADQANQA2ADcAOAA5ADoAOwA8AD0APgA/AEAAQQBCAEMARABFAEYARwBIAEkASgBLAEwATQBOAE8AUABRAFIAUwBUAFUAVgBXAFgAWQBaAFsAXABdAF4AXwBgAGEAiwEDAQQBBQEGAQcBCAEJAQoBCwEMAQ0BDgEPARABEQESARMBFAEVARYBFwEYARkBGgEbARwBHQEeAR8BIAEhASIBIwEkASUBJgEnASgBKQEqASsBLAEtAS4BLwEwATEBMgEzATQBNQE2ATcBOAE5AToBOwE8AT0BPgE/AUABQQFCAUMBRACrAUUHdW5pMDAwOQd1bmkwNDAxB3VuaTA0MTAHdW5pMDQxMQd1bmkwNDEyB3VuaTA0MTMHdW5pMDQxNAd1bmkwNDE1B3VuaTA0MTYHdW5pMDQxNwd1bmkwNDE4B3VuaTA0MTkHdW5pMDQxQQd1bmkwNDFCB3VuaTA0MUMHdW5pMDQxRAd1bmkwNDFFB3VuaTA0MUYHdW5pMDQyMAd1bmkwNDIxB3VuaTA0MjIHdW5pMDQyMwd1bmkwNDI0B3VuaTA0MjUHdW5pMDQyNgd1bmkwNDI3B3VuaTA0MjgHdW5pMDQyOQd1bmkwNDJBB3VuaTA0MkIHdW5pMDQyQwd1bmkwNDJEB3VuaTA0MkUHdW5pMDQyRgd1bmkwNDMwB3VuaTA0MzEHdW5pMDQzMgd1bmkwNDMzB3VuaTA0MzQHdW5pMDQzNQd1bmkwNDM2B3VuaTA0MzcHdW5pMDQzOAd1bmkwNDM5B3VuaTA0M0EHdW5pMDQzQgd1bmkwNDNDB3VuaTA0M0QHdW5pMDQzRQd1bmkwNDNGB3VuaTA0NDAHdW5pMDQ0MQd1bmkwNDQyB3VuaTA0NDMHdW5pMDQ0NAd1bmkwNDQ1B3VuaTA0NDYHdW5pMDQ0Nwd1bmkwNDQ4B3VuaTA0NDkHdW5pMDQ0QQd1bmkwNDRCB3VuaTA0NEMHdW5pMDQ0RAd1bmkwNDRFB3VuaTA0NEYHdW5pMDQ1MQd1bmkyMTE2AAAAAAAB//8AAgAAAAEAAAAA1bQyuAAAAADYRq3HAAAAANh+Vrs=
