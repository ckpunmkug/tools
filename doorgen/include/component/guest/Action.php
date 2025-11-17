<?php

class Action
{
	static function admin()
	{//{{{//
		
		session_start(['cookie_samesite' => 'Strict']);
		if(@is_string($_SESSION["csrf_token"]) != true) {
			$string = session_id() . uniqid(); 
			$_SESSION["csrf_token"] = md5($string);
		}
		define('CSRF_TOKEN', $_SESSION["csrf_token"]);
		
		if(!eval(Check::$string.='$_POST["csrf_token"]')) return(false);
		
		if($_POST["csrf_token"] !== CSRF_TOKEN) {
			trigger_error("Incorrect csrf token", E_USER_WARNING);
			return(false);
		}
	
		//var_dump($_POST);
		
		$action = @strval($_POST["action"]);
		switch($action) {
			case('new_category'):
				$return = self::new_category();
				if(!$return) {
					trigger_error("Can't perform 'new category' action", E_USER_WARNING);
					return(false);
				}
				break;
			default:
				trigger_error("Unsupported action", E_USER_WARNING);
				return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function new_category()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["name"]')) return(false);
		if(!eval(Check::$string.='$_POST["title"]')) return(false);
		if(!eval(Check::$string.='$_POST["description"]')) return(false);
		if(!eval(Check::$string.='$_POST["keywords"]')) return(false);
		if(!eval(Check::$string.='$_POST["header"]')) return(false);
		
		$id = Data::insert_category(
			$_POST["name"]
			,$_POST["title"]
			,$_POST["description"]
			,$_POST["keywords"]
			,$_POST["header"]
		);
		if(!is_int($id)) {
			trigger_error("Can't insert 'category' into database", E_USER_WARNING);
			return(false);
		}
		
		var_dump($id);
		
		return(true);
		
	}//}}}//
}

