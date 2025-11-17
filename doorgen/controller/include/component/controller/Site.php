<?php

class Site
{
	static $url = NULL;
	static $user = NULL;
	static $password = NULL;
	
	static $csrf_token = NULL;
	static $PHPSESSID = NULL;

	static function get_authorization_data(string $url)
	{//{{{//
		
		if(!eval(Check::$string.='self::$user')) return(false);
		$user = self::$user;
		
		if(!eval(Check::$string.='self::$password')) return(false);
		$password = self::$password;
		
		HTTP::$headers = [
			'Authorization: Basic '.base64_encode("{$user}:{$password}"),
			"Sec-Fetch-Site: same-origin",
		];
		
		$return = HTTP::GET($url);
		if(!is_array($return)) {
			trigger_error("Can't perform HTTP GET request", E_USER_WARNING);
			return(false);
		}
		
		$wrapper_data = $return["meta_data"]["wrapper_data"];
		$http_status = @strval($return["meta_data"]["wrapper_data"][0]);
		$http_body = @strval($return['contents']);
		
		if($http_status != 'HTTP/1.1 200 OK') {
			if (defined('DEBUG') && DEBUG) var_dump([
				'$http_status' => $http_status,
				'$http_body' => $http_body,
				
			]);
			trigger_error("HTTP response status is not 200", E_USER_WARNING);
			return(false);
		}
		
		$parse_PHPSESSID = function(array $wrapper_data) {
			foreach($wrapper_data as $string) {
				$pattern = '/^.+PHPSESSID\=([^;]+);.+$/';
				if(preg_match($pattern, $string, $MATCH) == 1) {
					return($MATCH[1]);
				}
			}
			return(false);
		};
		
		$return = $parse_PHPSESSID($wrapper_data);
		if(!is_string($return)) {
			trigger_error("Can't parse 'PHPSESSID'", E_USER_WARNING);
			return(false);
		}
		self::$PHPSESSID = $return;
		
		$parse_csrf_token = function(string $http_body) {
			$INPUT = getElementsByTagName($http_body, 'input');
			foreach($INPUT as $input) {
				if($input["attributes"]["name"] == 'csrf_token') {
					return($input["attributes"]["value"]);
				}
			}
			return(false);
		};
		
		$return = $parse_csrf_token($http_body);
		if(!is_string($return)) {
			trigger_error("Can't parse 'csrf_token'", E_USER_WARNING);
			return(false);
		}
		self::$csrf_token = $return;
		
		return(true);
		
	}//}}}//
	
	static function post_data(string $url, array $data)
	{//{{{//
		
		if(!eval(Check::$string.='self::$user')) return(false);
		$user = self::$user;
		
		if(!eval(Check::$string.='self::$password')) return(false);
		$password = self::$password;
		
		if(!eval(Check::$string.='self::$PHPSESSID')) return(false);
		$PHPSESSID = self::$PHPSESSID;
		
		HTTP::$headers = [
			'Authorization: Basic '.base64_encode("{$user}:{$password}"),
			"Sec-Fetch-Site: same-origin",
			"Cookie: PHPSESSID={$PHPSESSID}",
			"Content-Type: application/x-www-form-urlencoded",
		];
		
		if(!eval(Check::$string.='self::$csrf_token')) return(false);
		$csrf_token = self::$csrf_token;
		
		$body = 'csrf_token='.urlencode($csrf_token);
		foreach($data as $key => $value) {
			$body .= '&'.urlencode($key);
			$body .= '='.urlencode($value);
		}
		
		$return = HTTP::POST($url, $body);
		if(!is_array($return)) {
			trigger_error("Can't perform HTTP GET request", E_USER_WARNING);
			return(false);
		}
		
		$http_status = @strval($return["meta_data"]["wrapper_data"][0]);
		$http_body = @strval($return['contents']);
		
		if(!(
			$http_status == 'HTTP/1.1 200 OK'
			|| $http_status == 'HTTP/1.1 302 Found'
		)) {
			if (defined('DEBUG') && DEBUG) var_dump([
				'$http_status' => $http_status,
				'$http_body' => $http_body,
				
			]);
			trigger_error("HTTP response status is not 200", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

// Site

	static function set_site(int $id)
	{//{{{//
		
		$site = Data::select_site($id);
		if(!is_array($site)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		self::$url = $site["url"];
		self::$user = $site["user"];
		self::$password = $site["password"];
		
		$url = self::$url;
		$return = self::get_authorization_data($url);
		if(!$return) {
			trigger_error("Can't get 'authorization data'", E_USER_WARNING);
			return(false);
		}
		
		$url = self::$url;
		$data = [
			"action" => 'set_site',
			"title" => $site["title"],
			"description" => $site["description"],
			"keywords" => $site["keywords"],
		];
		$return = self::post_data($url, $data);
		if(!$return) {
			trigger_error("Can't post data", E_USER_WARNING);
			return(false);
		}
		
		return($return);
		
	}//}}}//

// Tournament //////////////////////////////////////////////////////////////////

	static function add_tournament(
		int $id
	) {//{{{//
			
		$array = Data::select_tournament($id);
		if(!is_array($array)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		$tournament = $array;
		
		$array = Data::select_site($array["site"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		$url = $array["url"];
		self::$user = $array["user"];
		self::$password = $array["password"];
		
		$return = self::get_authorization_data($url);
		if(!$return) {
			trigger_error("Can't get 'authorization data'", E_USER_WARNING);
			return(false);
		}
		
		$data = $tournament;
		$data["title"] = $tournament["name"];
		$data["action"] = 'add_tournament';
		$return = self::post_data($url, $data);
		if(!$return) {
			trigger_error("Can't post data", E_USER_WARNING);
			return(false);
		}
	
		return(true);
		
	}//}}}//

	static function change_tournament(
		int $id
	) {//{{{//
			
		$array = Data::select_tournament($id);
		if(!is_array($array)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		$tournament = $array;
		
		$array = Data::select_site($array["site"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		$url = $array["url"];
		self::$user = $array["user"];
		self::$password = $array["password"];
		
		$return = self::get_authorization_data($url);
		if(!$return) {
			trigger_error("Can't get 'authorization data'", E_USER_WARNING);
			return(false);
		}
		
		$data = $tournament;
		$data["title"] = $tournament["name"];
		$data["action"] = 'change_tournament';
		$return = self::post_data($url, $data);
		if(!$return) {
			trigger_error("Can't post data", E_USER_WARNING);
			return(false);
		}
	
		return(true);
		
	}//}}}//
	
	static function delete_tournament(
		int $id
	) {//{{{//
			
		$array = Data::select_tournament($id);
		if(!is_array($array)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		$tournament = $array;
		
		$array = Data::select_site($array["site"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		$url = $array["url"];
		self::$user = $array["user"];
		self::$password = $array["password"];
		
		$return = self::get_authorization_data($url);
		if(!$return) {
			trigger_error("Can't get 'authorization data'", E_USER_WARNING);
			return(false);
		}
		
		$data = $tournament;
		$data["title"] = $tournament["name"];
		$data["action"] = 'delete_tournament';
		$return = self::post_data($url, $data);
		if(!$return) {
			trigger_error("Can't post data", E_USER_WARNING);
			return(false);
		}
	
		return(true);
		
	}//}}}//
	
// Event ///////////////////////////////////////////////////////////////////////

	static function add_event(
		int $id
	) {//{{{//
		
		$array = Data::select_event($id);
		if(!is_array($array)) {
			trigger_error("Can't select 'event'", E_USER_WARNING);
			return(false);
		}
		$event = $array;
			
		$array = Data::select_tournament($array["tournament"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		$array = Data::select_site($array["site"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		$url = $array["url"];
		self::$user = $array["user"];
		self::$password = $array["password"];
		
		$return = self::get_authorization_data($url);
		if(!$return) {
			trigger_error("Can't get 'authorization data'", E_USER_WARNING);
			return(false);
		}
		
		$data = $event;
		$data["opponents"] = json_encode($event["opponents"]);
		$data["action"] = 'add_event';
		$return = self::post_data($url, $data);
		if(!$return) {
			trigger_error("Can't post data", E_USER_WARNING);
			return(false);
		}
	
		return(true);
		
	}//}}}//
	
	static function update_event(
		int $id
	) {//{{{//
		
		$array = Data::select_event($id);
		if(!is_array($array)) {
			trigger_error("Can't select 'event'", E_USER_WARNING);
			return(false);
		}
		$event = $array;
			
		$array = Data::select_tournament($array["tournament"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		$array = Data::select_site($array["site"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		$url = $array["url"];
		self::$user = $array["user"];
		self::$password = $array["password"];
		
		$return = self::get_authorization_data($url);
		if(!$return) {
			trigger_error("Can't get 'authorization data'", E_USER_WARNING);
			return(false);
		}
		
		$data = $event;
		$data["opponents"] = json_encode($event["opponents"]);
		$data["action"] = 'update_event';
		$return = self::post_data($url, $data);
		if(!$return) {
			trigger_error("Can't post data", E_USER_WARNING);
			return(false);
		}
	
		return(true);
		
	}//}}}//
	
	static function delete_event(
		int $id
	) {//{{{//
		
		$array = Data::select_event($id);
		if(!is_array($array)) {
			trigger_error("Can't select 'event'", E_USER_WARNING);
			return(false);
		}
		$event = $array;
			
		$array = Data::select_tournament($array["tournament"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		$array = Data::select_site($array["site"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		$url = $array["url"];
		self::$user = $array["user"];
		self::$password = $array["password"];
		
		$return = self::get_authorization_data($url);
		if(!$return) {
			trigger_error("Can't get 'authorization data'", E_USER_WARNING);
			return(false);
		}
		
		$data = $event;
		$data["opponents"] = json_encode($event["opponents"]);
		$data["action"] = 'delete_event';
		$return = self::post_data($url, $data);
		if(!$return) {
			trigger_error("Can't post data", E_USER_WARNING);
			return(false);
		}
	
		return(true);
		
	}//}}}//

// Prediction //////////////////////////////////////////////////////////////////

	static function change_prediction(int $event)
	{//{{{//
		
		$array = Data::select_prediction($event);
		if(!is_array($array)) {
			trigger_error("Can't select 'prediction'", E_USER_WARNING);
			return(false);
		}
		$content = $array["prediction"];
		
		$array = Data::select_event($array["event"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'event'", E_USER_WARNING);
			return(false);
		}
		
		$array = Data::select_tournament($array["tournament"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		$array = Data::select_site($array["site"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		$url = $array["url"];
		self::$user = $array["user"];
		self::$password = $array["password"];
		
		$return = self::get_authorization_data($url);
		if(!$return) {
			trigger_error("Can't get 'authorization data'", E_USER_WARNING);
			return(false);
		}
		
		$data = [
			"action" => 'change_prediction',
			"event" => strval($event),
			"content" => $content,
		];
		$return = self::post_data($url, $data);
		if(!$return) {
			trigger_error("Can't post data", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

}

