<?php

class Action
{
	static $site_id = NULL;
	static $request_id = NULL;
	static $lng = NULL;
	static $sport = NULL;
	static $tournament = NULL;

// Site ////////////////////////////////////////////////////////////////////////

	static function add_site()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["url"]')) return(false);
		$url = $_POST["url"];
		
		if(!eval(Check::$string.='$_POST["user"]')) return(false);
		$user = $_POST["user"];
		
		if(!eval(Check::$string.='$_POST["password"]')) return(false);
		$password = $_POST["password"];
		
		if(!eval(Check::$string.='$_POST["title"]')) return(false);
		$title = $_POST["title"];
		
		if(!eval(Check::$string.='$_POST["description"]')) return(false);
		$description = $_POST["description"];
		
		if(!eval(Check::$string.='$_POST["keywords"]')) return(false);
		$keywords = $_POST["keywords"];
		
		$id = Data::insert_site(
			$url,
			$user,
			$password,
			$title,
			$description,
			$keywords
		);
		if(!is_int($id)) {
			trigger_error("Can't insert 'site'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php?page=edit_site&id={$id}");
		
		return(true);
		
	}//}}}//

	static function change_site()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		if(!eval(Check::$string.='$_POST["url"]')) return(false);
		$url = $_POST["url"];
		
		if(!eval(Check::$string.='$_POST["user"]')) return(false);
		$user = $_POST["user"];
		
		if(!eval(Check::$string.='$_POST["password"]')) return(false);
		$password = $_POST["password"];
		
		if(!eval(Check::$string.='$_POST["title"]')) return(false);
		$title = $_POST["title"];
		
		if(!eval(Check::$string.='$_POST["description"]')) return(false);
		$description = $_POST["description"];
		
		if(!eval(Check::$string.='$_POST["keywords"]')) return(false);
		$keywords = $_POST["keywords"];
		
		$return = Data::update_site(
			$id,
			$url,
			$user,
			$password,
			$title,
			$description,
			$keywords
		);
		if(!$return) {
			trigger_error("Can't update 'site'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php?page=edit_site&id={$id}");
		
		return(true);
		
	}//}}}//

	static function delete_site()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		$return = Data::delete_site($id);
		if(!$return) {
			trigger_error("Can't delete 'site'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php");
		
		return(true);
		
	}//}}}//

	static function delete_sites()
	{//{{{//
	
		if(!isset($_POST["id"])) {
			header("Location: index.php?page=sites_list");
			return(true);
		}
	
		if(!eval(Check::$array.='$_POST["id"]')) return(false);
		$ID = $_POST["id"];
		
		foreach($ID as $id) {
			if(!eval(Check::$string.='$id')) return(false);
			$id = intval($id);
		
			$return = Data::delete_site($id);
			if(!$return) {
				trigger_error("Can't delete 'site'", E_USER_WARNING);
				return(false);
			}
		}
		
		header("Location: index.php?page=sites_list");
		
		return(true);
		
	}//}}}//

// Tournament //////////////////////////////////////////////////////////////////

	static function get_languages()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		cpservm::$site = intval($_POST["id"]);
		
		$id = cpservm::get_languages();
		if(!is_int($id)) {
			trigger_error("Can't get 'languages'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php?page=select_language&id={$id}");
		
		return(true);
		
	}//}}}//

	static function get_sports()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		$data = cpservm::get_languages($id);
		if(!is_array($data)) {
			trigger_error("Can't get 'languages'", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$int.='$data["site"]')) return(false);
		cpservm::$site = $data["site"];
		
		if(!eval(Check::$string.='$_POST["lng"]')) return(false);
		cpservm::$lng = $_POST["lng"];
		
		$id = cpservm::get_sports();
		if(!is_int($id)) {
			trigger_error("Can't get 'sports'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php?page=select_sport&id={$id}");
		
		return(true);
		
	}//}}}//

	static function get_tournaments()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		$data = cpservm::get_sports($id);
		if(!is_array($data)) {
			trigger_error("Can't get 'sports'", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$int.='$data["site"]')) return(false);
		cpservm::$site = $data["site"];
		
		if(!eval(Check::$string.='$data["lng"]')) return(false);
		cpservm::$lng = $data["lng"];
		
		if(!eval(Check::$string.='$_POST["sportId"]')) return(false);
		cpservm::$sportId = intval($_POST["sportId"]);
		
		$id = cpservm::get_tournaments();		
		if(!is_int($id)) {
			trigger_error("Can't get 'tournaments'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php?page=select_tournament&id={$id}");
		
		return(true);
		
	}//}}}//

	static function get_events()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		$data = cpservm::get_tournaments($id);
		if(!is_array($data)) {
			trigger_error("Can't get 'sports'", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$int.='$data["site"]')) return(false);
		cpservm::$site = $data["site"];
		
		if(!eval(Check::$string.='$data["lng"]')) return(false);
		cpservm::$lng = $data["lng"];
		
		if(!eval(Check::$int.='$data["sport"]')) return(false);
		cpservm::$sportId = $data["sport"];
		
		if(!eval(Check::$string.='$_POST["tournamentId"]')) return(false);
		cpservm::$tournamentId = intval($_POST["tournamentId"]);
		
		foreach($data["tournaments"] as $tournament) {
			if($tournament["id"] == cpservm::$tournamentId)
				cpservm::$tournamentName = $tournament["name"];
		}
		if(!eval(Check::$string.='cpservm::$tournamentName')) return(false);
		
		$id = cpservm::get_events();		
		if(!is_int($id)) {
			trigger_error("Can't get 'events'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php?page=save_events&id={$id}&tournamentId=".cpservm::$tournamentId."&lng=".cpservm::$lng);
		
		return(true);
		
	}//}}}//

	static function update_events()
	{//{{{//
		
		$update_events = function(int $id)
		{//{{{//
			
			$tournament = Data::select_tournament($id);
			if(!is_array($tournament)) {
				trigger_error("Can't select 'tournament'", E_USER_WARNING);
				return(false);
			}
			
			cpservm::$tournamentId = $tournament["tournamentId"];
			cpservm::$lng = $tournament["lng"];
			
			$id = cpservm::get_events();		
			if(!is_int($id)) {
				trigger_error("Can't get 'events'", E_USER_WARNING);
				return(false);
			}
			
			$remote_events = cpservm::get_events($id);
			if(!is_array($remote_events)) {
				trigger_error("Can't get 'event'", E_USER_WARNING);
				return(false);
			}
			
			$local_events = Data::select_events($tournament["id"]);
			if(!is_array($local_events)) {
				trigger_error("Can't select 'events'", E_USER_WARNING);
				return(false);
			}
			
			foreach($remote_events["events"] as $remote_event) {
				$remote_id = $remote_event["event"];
				$flag = false;
				foreach($local_events as $id) {
					$local_event = Data::select_event($id);
					if(!is_array($local_event)) {
						trigger_error("Can't select 'event'", E_USER_WARNING);
						return(false);
					}
					$local_id = $local_event["remote"];
					if($local_id == $remote_id) {
						$flag = true;
						break;
					}
				}
				if($flag) continue;
				
				$array = $remote_event;
			
				if(!eval(Check::$int.='$array["event"]')) return(false);
				$event = $array["event"];
				
				if(!eval(Check::$string.='$array["location"]')) return(false);
				$location = $array["location"];
				
				$opponents = [];			
				if(!eval(Check::$string.='$array["opponents"][0]')) return(false);
				$opponents[0] = $array["opponents"][0];
				if(!eval(Check::$string.='$array["opponents"][1]')) return(false);
				$opponents[1] = $array["opponents"][1];
				
				if(!eval(Check::$int.='$array["date"]')) return(false);
				$date = $array["date"];
				
				$id = Data::insert_event($tournament["id"], $event, $location, $opponents, $date, '', '');
				if(!is_int($id)) {
					trigger_error("Can't insert 'event'", E_USER_WARNING);
					return(false);
				}
				
				$return = Action::get_prediction($id);
				if(!$return) {
					trigger_error("Can't get 'prediction'", E_USER_WARNING);
					return(false);
				}
			}
			
			return(true);					
			
		};//}}}//
		
		$sites = Data::select_sites();
		if(!is_array($sites)) {
			trigger_error("Can't select 'sites'", E_USER_WARNING);
			return(false);
		}
		
		foreach($sites as $site) {
			$tournaments = Data::select_tournaments($site);
			if(!is_array($tournaments)) {
				trigger_error("Can't select 'tournaments'", E_USER_WARNING);
				return(false);
			}
			foreach($tournaments as $tournament) {
				$return = $update_events($tournament);
				if(!$return) {
					trigger_error("Can't update 'events'", E_USER_WARNING);
					return(false);
				}
			}
		}
		
		return(true);
		
	}//}}}//

	static function add_tournament()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		if(!eval(Check::$string.='$_POST["tournamentId"]')) return(false);
		$tournamentId = intval($_POST["tournamentId"]);
		
		if(!eval(Check::$string.='$_POST["lng"]')) return(false);
		$lng = $_POST["lng"];
		
		$data = cpservm::get_events($id);
		if(!is_array($data)) {
			trigger_error("Can't get 'events'", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$int.='$data["site"]')) return(false);
		$site = $data["site"];
		
		if(!eval(Check::$string.='$data["tournament"]')) return(false);
		$tournament = $data["tournament"];
		
		$id = Data::insert_tournament($site, $tournament, '', '', $tournamentId, $lng);
		if(!is_int($id)) {
			trigger_error("Can't insert 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$array.='$data["events"]')) return(false);
		$events = $data["events"];
		
		foreach($events as $array) {
			
			if(!eval(Check::$int.='$array["event"]')) return(false);
			$event = $array["event"];
			
			if(!eval(Check::$string.='$array["location"]')) return(false);
			$location = $array["location"];
			
			$opponents = [];			
			if(!eval(Check::$string.='$array["opponents"][0]')) return(false);
			$opponents[0] = $array["opponents"][0];
			if(!eval(Check::$string.='$array["opponents"][1]')) return(false);
			$opponents[1] = $array["opponents"][1];
			
			if(!eval(Check::$int.='$array["date"]')) return(false);
			$date = $array["date"];
			
			$return = Data::insert_event($id, $event, $location, $opponents, $date, '', '');
			if(!is_int($return)) {
				trigger_error("Can't insert 'event'", E_USER_WARNING);
				return(false);
			}
		}
		
		header("Location: index.php?page=view_tournament&id={$id}");
		
		return(true);
		
	}//}}}//

	static function change_tournament()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		if(!eval(Check::$string.='$_POST["description"]')) return(false);
		$description = $_POST["description"];
		
		if(!eval(Check::$string.='$_POST["keywords"]')) return(false);
		$keywords = $_POST["keywords"];
		
		$tournament = Data::select_tournament($id);
		if(!is_array($tournament)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		$return = Data::update_tournament(
			$id,
			$tournament["site"],
			$tournament["name"],
			$description,
			$keywords
		);
		if(!$return) {
			trigger_error("Can't update 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php?page=view_tournament&id={$id}");
		
		return(true);
		
	}//}}}//
	
	static function delete_tournament()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		if(!eval(Check::$string.='$_POST["site"]')) return(false);
		$site = $_POST["site"];
		
		$return = Data::delete_tournament($id);
		if(!$return) {
			trigger_error("Can't delete 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: index.php?page=tournaments_list&site={$site}");
		
		return(true);
		
	}//}}}//
	
	static function delete_tournaments()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["site"]')) return(false);
		$site = $_POST["site"];
		
		if(!isset($_POST["id"])) {
			header("Location: index.php?page=tournaments_list&site={$site}");
			return(true);
		}
		
		if(!eval(Check::$array.='$_POST["id"]')) return(false);
		$ID = $_POST["id"];
		
		foreach($ID as $id) {
			if(!eval(Check::$string.='$id')) return(false);
			$id = intval($id);
			
			$return = Data::delete_tournament($id);
			if(!$return) {
				trigger_error("Can't delete 'tournament'", E_USER_WARNING);
				return(false);
			}
		}
		
		header("Location: index.php?page=tournaments_list&site={$site}");
		
		return(true);
		
	}//}}}//

	// AI
	
	static function ai_request()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["request"]')) return(false);
		$request = $_POST["request"];
		
		$answer = anthropic::send_message($request);
		if(!is_string($answer)) {
			trigger_error("Can't sendmessage to 'anthropic' AI", E_USER_WARNING);
			return(false);
		}
		
		$html = [];
		$_ = [];
		
		$_['answer'] = htmlentities($answer);
		
		$html[0] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<pre>{$_['answer']}</pre>
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body .= $html[0];
		
		return(true);
		
	}//}}}//

	static function get_predictions()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["tournament"]')) return(false);
		$tournament = intval($_POST["tournament"]);
		
		if(!eval(Check::$string.='$_POST["prompt"]')) return(false);
		$prompt = $_POST["prompt"];
		
		$return = Data::insert_prompt($tournament, $prompt);
		if(!$return) {
			trigger_error("Can't insert prompt", E_USER_WARNING);
			return(false);
		}
		
		$job = Job::insert_job('get_predictions');
		if(!is_int($job)) {
			trigger_error("Can't create new job", E_USER_WARNING);
			return(false);
		}
		
		$events = Data::select_events($tournament);
		if(!is_array($events)) {
			trigger_error("Can't select 'events'", E_USER_WARNING);
			return(false);
		}
		
		$action = 'get_prediction';
		foreach($events as $id) {
			$event = Data::select_event($id);
			if(!is_array($event)) {
				trigger_error("Can't select 'event'", E_USER_WARNING);
				return(false);
			}
		
			if(!is_string($event["opponents"][0])) continue;
			
			$about = "Wait prediction for ".$event["opponents"][0];
			if(is_string($event["opponents"][1]))
				$about .= " - ".$event["opponents"][1];
			
			$parameters = [
				"event" => $event["id"],
				"action" => $action,
				"about" => $about,
			];
			$return = Job::insert_task($job, $parameters);
		}
		
		header("Location: index.php?page=job_controller&job={$job}");
		
		return(true);
		
	}//}}}//

	static function get_prediction(int $id)
	{//{{{//
		
		$event = Data::select_event($id);
		if(!is_array($event)) {
			trigger_error("Can't select 'event'", E_USER_WARNING);
			return(false);
		}
		
		$tournament = Data::select_tournament($event["tournament"]);
		if(!is_array($tournament)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		$prompt = Data::select_prompt($tournament["id"]);
		if(!is_array($prompt)) {
			trigger_error("Can't select 'prompt'", E_USER_WARNING);
			return(false);
		}
		
		$date = date('d.m.y H:i', $event["date"]);
		
		$prompt = prediction::replace_tags([
				"tournament" => $tournament["name"],
				"opponent1" => $event["opponents"][0],
				"opponent2" => $event["opponents"][1],
				"location" => $event["location"],
				"date" => $date,
			], $prompt["prompt"]);
		
		$answer = anthropic::send_message($prompt);
		if(!is_string($answer)) {
			trigger_error("Can't send message to AI", E_USER_WARNING);
			return(false);
		}
		
		$answer = prediction::prepare_answer($answer);
		if(!is_string($answer)) {
			trigger_error("Can't prepare 'answer'", E_USER_WARNING);
			return(false);
		}
		
		$array = json_decode($answer, true);
		if(!is_array($array)) {
			trigger_error("Can't decode from json 'AI answer' to array", E_USER_WARNING);
			return(false);
		}
		$AI_answer = $array;
		
		if(!eval(Check::$string.='$AI_answer["description"]')) return(false);
		$description = $AI_answer["description"];
		unset($AI_answer["description"]);
		
		if(!eval(Check::$string.='$AI_answer["keywords"]')) return(false);
		$keywords = $AI_answer["keywords"];
		unset($AI_answer["keywords"]);
		
		$answer = json_encode($AI_answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		
		$return = Data::insert_prediction($event["id"], $answer);
		if(!$return) {
			trigger_error("Can't insert prediction", E_USER_WARNING);
			return(false);
		}
		
		$return = Data::update_event($id, $description, $keywords);
		
		return(true);
		
	}//}}}//

}

