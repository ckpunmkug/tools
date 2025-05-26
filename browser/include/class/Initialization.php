<?php 

class Initialization
{

	function __construct()
	{//{{{//
	
		if(PHP_SAPI == 'cli-server') {
			file_put_contents('php://stderr', "\n");
		}
		
		if(PHP_SAPI == 'cli') {
			$_SERVER["REQUEST_URI"] = '/index.php';
			$_SERVER["REQUEST_METHOD"] = 'POST';
			$this->args();
		}
		
		$this->security();
		
		$this->define();
		
		$this->ini_set();
		
	}//}}}//
	
	function security()
	{//{{{//
	
		header("Content-Security-Policy: frame-ancestors 'self';");

		session_start([
			'cookie_samesite' => 'Strict',
		]);
		if(@is_string($_SESSION["csrf_token"]) != true) {
			$string = session_id() . uniqid(); 
			$_SESSION["csrf_token"] = md5($string);
		}
		define('CSRF_TOKEN', $_SESSION["csrf_token"]);
		
	}//}}}//
	
	function define()
	{//{{{//
	
		if(!defined('DEBUG')) define('DEBUG', true);
		if(!defined('VERBOSE')) define('VERBOSE', true);
		if(!defined('QUIET')) define('QUIET', false);
		
		if(@is_string($_SERVER["REQUEST_URI"]) !== true) {
			trigger_error('Incorrect string $_SERVER["REQUEST_URI"]', E_USER_ERROR);
		}
		$return = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		if(!is_string($return)) {
			trigger_error('Parse url from $_SERVER["REQUEST_URI"] failed', E_USER_ERROR);
		}
		define('URL_PATH', $return);
		
	}//}}}//
	
	function ini_set()
	{//{{{//
		
		if(defined('QUIET') && QUIET === true) {
			ini_set('error_reporting', 0);
			ini_set('display_errors', false);
		}
		else {
			ini_set('error_reporting', E_ALL );
			if(PHP_SAPI == 'cli') 
				ini_set('display_errors', false);
			else
				ini_set('display_errors', true);
			ini_set('html_errors', false);
		}
		
	}//}}}//

	function args()
	{//{{{//
	
		Args::$description = "Console version of browser backend";
		
		Args::add([
			"-C", "--component", "<component>", "Component",
			function ($string) {
				$_GET["component"] = $string;
			}, true
		]);
		
		Args::add([
			"-A", "--action", "action", "Action",
			function ($string) {
				$_GET["action"] = $string;
			}, true
		]);
		
		Args::add([
			"-D", "--data", "<file|''>", "Data",
			function ($string) {
				if(strlen($string) == 0) {
					$_POST['data'] = '';
				}
				else {
					$contents = file_get_contents($string);
					if(!is_string($contents)) {
						trigger_error("Can't get data from file", E_USER_ERROR);
						exit(255);
					}
					$_POST['data'] = $contents;
				}
			}, true
		]);
		
		Args::apply();
		
		return(NULL);
		
	}//}}}//
}

