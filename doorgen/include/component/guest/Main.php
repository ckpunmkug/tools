<?php

class Main
{

	function __construct()
	{//{{{
	
		if(PHP_SAPI == 'cli') {
			$return = $this->handle_cli();
			if($return !== true) {
				trigger_error("Handle cli failed", E_USER_ERROR);
				exit(255);
			}
			exit(0);
		}
		
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
				http_response_code(500);
				trigger_error("Unsupported http request method", E_USER_ERROR);
				exit(255);
		}
		
	}//}}}

	function handle_cli()
	{//{{{//
			
		Args::$description = "Door admin console";
		Args::add([
			"-T", "--test", NULL, "Tests",
			function ()
			{//{{{//

				$return = Page::index(3); var_dump($return);

			}//}}}//
			, false]);
		Args::apply();
		
		return(true);
		
	}//}}}//
	
	function handle_get_request()
	{//{{{
		
		/*
		$pattern = '/^.*\/index\.php$/';
		if(preg_match($pattern, URL_PATH) != 1) {
			header('Location: index.php');
			exit(0);
		}
		*/
		
		if(
			is_string(@$_GET['tournament'])
			&& is_string(@$_GET['event'])
		) {
			
// event ///////////////////////////////////////////////////////////////////////
			
			$return = Page::event($_GET['tournament'], $_GET['event']);
			
			array_push(HTML::$scripts, '/share/theme/event.js');
		}
		else {
			$page = 1;
			if(isset($_GET['page'])) $page = intval($_GET['page']);
			
			if(is_string(@$_GET['tournament'])) {
			
// tournament //////////////////////////////////////////////////////////////////
			
				$return = Page::tournament($_GET['tournament'], $page);
			}
			else {
			
// index ///////////////////////////////////////////////////////////////////////
				
				$return = Page::index($page);
			}
			
			array_push(HTML::$scripts, '/share/theme/tournament.js');
		}
		
		if($return === false) goto label_error;
		if($return === NULL) goto label_404;
		
		return(true);
		
		label_error:
		trigger_error("Can't create page", E_USER_WARNING);
		return(false);
		
		label_404:
		http_response_code(404);
		die("404 Not found");
	
	}//}}}
	
	function handle_post_request()
	{//{{{
			
		http_response_code(404);
		die("404 Not found");
		
	}//}}}

}

