<?php

class Action
{
	static function set_status()
	{//{{{//
			
		if(!eval(Check::$string.='$_SERVER["HTTP_REFERER"]')) return(false);
		$http_referer = $_SERVER["HTTP_REFERER"];
	
		if(!eval(Check::$string.='$_POST["query"]')) return(false);
		$query = intval($_POST["query"]);
		
		if(!eval(Check::$string.='$_POST["result"]')) return(false);
		$result = intval($_POST["result"]);
		
		if(!eval(Check::$string.='$_POST["status"]')) return(false);
		$status = $_POST["status"];
		
		$return = data::$get["test_status"]($query, $result);
		if($return === false) {
			trigger_error("Can;t get 'test_status'", E_USER_WARNING);
			return(false);
		}
		if($return === NULL) {
			$return = data::$add["test_status"]($query, $result, $status);
			if(!$return) {
				trigger_error("Can't add 'test_status'", E_USER_WARNING);
				return(false);
			}
		}
		if(is_array($return)) {
			$return = data::$update["test_status"]($query, $result, $status);
			if(!$return) {
				trigger_error("Can't update 'test_status'", E_USER_WARNING);
				return(false);
			}
		}
		
		header("Location: {$http_referer}");
		
		return(true);
		
	}//}}}//
		
}

