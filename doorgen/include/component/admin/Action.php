<?php

class Action
{
	function __construct()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["csrf_token"]')) {
			throw new Exception("Incorrect post data");
		}
		
		if($_POST["csrf_token"] !== CSRF_TOKEN) {
			throw new Exception("Incorrect csrf token");
		}
		
	}//}}}//
	
	function set_site()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["title"]')) return(false);
		$title = $_POST["title"];
		
		if(!eval(Check::$string.='$_POST["description"]')) return(false);
		$description = $_POST["description"];
		
		if(!eval(Check::$string.='$_POST["keywords"]')) return(false);
		$keywords = $_POST["keywords"];
		
		$id = Data::insert_site($title, $description, $keywords);
		if(!is_int($id)) {
			trigger_error("Can't insert 'site'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

// Tournament //////////////////////////////////////////////////////////////////

	function add_tournament()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		if(!eval(Check::$string.='$_POST["title"]')) return(false);
		$title = $_POST["title"];
		
		if(!eval(Check::$string.='$_POST["description"]')) return(false);
		$description = $_POST["description"];
		
		if(!eval(Check::$string.='$_POST["keywords"]')) return(false);
		$keywords = $_POST["keywords"];
		
		$translit = cyrillic_to_translit($title);
		
		$id = Data::insert_tournament($id, $translit, $title, $description, $keywords);
		if(!is_int($id)) {
			trigger_error("Can't insert 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	function change_tournament()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		if(!eval(Check::$string.='$_POST["title"]')) return(false);
		$title = $_POST["title"];
		
		if(!eval(Check::$string.='$_POST["description"]')) return(false);
		$description = $_POST["description"];
		
		if(!eval(Check::$string.='$_POST["keywords"]')) return(false);
		$keywords = $_POST["keywords"];
		
		$translit = cyrillic_to_translit($title);
		
		$return = Data::update_tournament($id, $translit, $title, $description, $keywords);
		if(!$return) {
			trigger_error("Can't update 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	function delete_tournament()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		$return = Data::delete_tournament($id);
		if(!$return) {
			trigger_error("Can't delete 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	function delete_tournaments()
	{//{{{//
		
		if(!@eval(Check::$array.='$_POST["id"]')) return(true);
		$ID = $_POST["id"];
		
		foreach($ID as $id) {
			$id = intval($id);
			$return = Data::delete_tournament($id);
			if(!$return) {
				trigger_error("Can't delete 'tournament'", E_USER_WARNING);
				continue;
			}
		}
		
		return(true);
		
	}//}}}//

// Event ///////////////////////////////////////////////////////////////////////

	function add_event()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		if(!eval(Check::$string.='$_POST["tournament"]')) return(false);
		$tournament = intval($_POST["tournament"]);
		
		if(!eval(Check::$string.='$_POST["location"]')) return(false);
		$location = $_POST["location"];
		
		if(!eval(Check::$string.='$_POST["opponents"]')) return(false);
		$opponents = $_POST["opponents"];
		$opponents = json_decode($opponents, true);
		if(!is_array($opponents)) {
			trigger_error("Can't decode 'opponents' json to array", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$string.='$_POST["date"]')) return(false);
		$date = intval($_POST["date"]);
		
		$translit = '';
		$title = '';
		if(@is_string($opponents[0])) {
			$translit .= cyrillic_to_translit($opponents[0]);
			$title .= $opponents[0];
		}
		if(@is_string($opponents[1])) {
			$translit .= '-'.cyrillic_to_translit($opponents[1]);
			$title .= ' - '.$opponents[1];
		}
		
		$description = '';
		$keywords = '';
		
		$id = Data::insert_event($id, $tournament, $location, $opponents, $date, $translit, $title, $description, $keywords);
		if(!is_int($id)) {
			trigger_error("Can't insert 'event'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	function update_event()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		if(!eval(Check::$string.='$_POST["tournament"]')) return(false);
		$tournament = intval($_POST["tournament"]);
		
		if(!eval(Check::$string.='$_POST["location"]')) return(false);
		$location = $_POST["location"];
		
		if(!eval(Check::$string.='$_POST["opponents"]')) return(false);
		$opponents = $_POST["opponents"];
		$opponents = json_decode($opponents, true);
		if(!is_array($opponents)) {
			trigger_error("Can't decode 'opponents' json to array", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$string.='$_POST["date"]')) return(false);
		$date = intval($_POST["date"]);
		
		if(!eval(Check::$string.='$_POST["description"]')) return(false);
		$description = $_POST["description"];
		
		if(!eval(Check::$string.='$_POST["keywords"]')) return(false);
		$keywords = $_POST["keywords"];
		
		$translit = '';
		$title = '';
		if(@is_string($opponents[0])) {
			$translit .= cyrillic_to_translit($opponents[0]);
			$title .= $opponents[0];
		}
		if(@is_string($opponents[1])) {
			$translit .= '-'.cyrillic_to_translit($opponents[1]);
			$title .= ' - '.$opponents[1];
		}
		
		$return = Data::update_event($id, $tournament, $location, $opponents, $date, $translit, $title, $description, $keywords);
		if(!$return) {
			trigger_error("Can't update 'event'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	function delete_event()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		$return = Data::delete_event($id);
		if(!$return) {
			trigger_error("Can't delete 'event'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

// Prediction //////////////////////////////////////////////////////////////////

	function change_prediction()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["event"]')) return(false);
		$event = $_POST["event"];
		
		if(!eval(Check::$string.='$_POST["content"]')) return(false);
		$content = $_POST["content"];
		$content = json_decode($content, true);
		if(!is_array($content)) {
			trigger_error("Can't decode 'content' from json", E_USER_WARNING);
			return(false);
		}
		
		$return = Data::update_prediction($event, $content);
		if(!$return) {
			trigger_error("Can't update 'prediction'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

}

