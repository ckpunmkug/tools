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
			}//}}}//
			, false]);
		Args::add([
			"-M", "--create-main-tables", NULL, "Create main tables in database",
			function ()
			{//{{{//
				
				$return = Data::create_tables();
				if(!$return) {
					trigger_error("Can't create 'main' tables in database", E_USER_ERROR);
					exit(255);
				}
				user_error("Create 'main' tables in database - complete");
				exit(0);
				
			}//}}}//
		, false]);
		Args::apply();
		
		return(true);
		
	}//}}}//
	
	function handle_get_request()
	{//{{{
		
		$pattern = '/^.*\/index\.php$/';
		if(preg_match($pattern, URL_PATH) != 1) {
			header('Location: index.php');
			exit(0);
		}
		
		$page = 'index';
		if(@is_string($_GET["page"]) == true) {
			$page = $_GET["page"];
		}
		
		$Page = new Page();
		
		switch($page) {
			case('index'):
				$return = $Page->index();
				break;
			case('edit_site'):
				$return = $Page->edit_site();
				break;
			case('new_category'):
				$return = $Page->new_category();
				break;
			case('edit_category'):
				$return = $Page->edit_category();
				break;
			case('categories_list'):
				$return = $Page->categories_list();
				break;
			case('new_article'):
				$return = $Page->new_article();
				break;
			case('edit_article'):
				$return = $Page->edit_article();
				break;
			case('articles_list'):
				$return = $Page->articles_list();
				break;
			case('create_tables'):
				$return = $Page->create_tables();
				break;
			default:
				if (defined('DEBUG') && DEBUG) var_dump(['page' => $page]);
				trigger_error("Unsupported page", E_USER_WARNING);
				return(false);
		}
		
		if($return === false) goto label_error;
		if($return === NULL) goto label_404;
		
		return(true);
		
		label_error:
		if (defined('DEBUG') && DEBUG) var_dump(['page' => $page]);
		trigger_error("Can't create page", E_USER_WARNING);
		return(false);
		
		label_404:
		http_response_code(404);
		die("404 Not found");
	
	}//}}}
	
	function handle_post_request()
	{//{{{
		
		if(!eval(Check::$string.='$_POST["action"]')) return(false);
		$action = $_POST["action"];
		
		try {
			$Action = new Action();
		}
		catch(Exception $Exception) {
			trigger_error($Exception->getMessage(), E_USER_WARNING);
			return(false);
		}
		
		switch($action) {
			case('set_site'):
				$return = $Action->set_site();
				break;
			case('add_tournament'):
				$return = $Action->add_tournament();
				break;
			case('change_tournament'):
				$return = $Action->change_tournament();
				break;
			case('delete_tournament'):
				$return = $Action->delete_tournament();
				break;
			case('add_event'):
				$return = $Action->add_event();
				break;
			case('update_event'):
				$return = $Action->update_event();
				break;
			case('delete_event'):
				$return = $Action->delete_event();
				break;
			case('change_prediction'):
				$return = $Action->change_prediction();
				break;
			default:
				if (defined('DEBUG') && DEBUG) var_dump(['action' => $action]);
				trigger_error("Unsupported action", E_USER_WARNING);
				return(false);
		}
		
		if($return === false) {
			if (defined('DEBUG') && DEBUG) var_dump(['action' => $action]);
			trigger_error("Can't perform passed action", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}

}

