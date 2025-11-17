<?php

class cpservm
{
	static $ClientId = CONFIG["cpservm"]["ClientId"];
	static $ClientSecret = CONFIG["cpservm"]["ClientSecret"];
	static $ref = CONFIG["cpservm"]["ref"];
	
	static $site = NULL;
	static $lng = NULL;
	static $sportId = NULL;
	static $tournamentId = NULL;
	static $tournamentName = NULL;
	
	static function create_tables()
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/cpservm/access_tokens';
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`access_token` TEXT
	,`expires_in` INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't create '{$_["table"]}' table", E_USER_WARNING);
			return(false);
		}
		
		$_["table"] = '/cpservm/requests';
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`id` INT PRIMARY KEY AUTO_INCREMENT
	,`data` MEDIUMTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't create '{$_["table"]}' table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	// Access token

	static function insert_access_token(
		string $access_token,
		int $expires_in
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/cpservm/access_tokens';
		$_["access_token"] = $DB->escape($access_token);
		$_["expires_in"] = $DB->int($expires_in);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`access_token`, `expires_in`
) VALUES (
	'{$_["access_token"]}', {$_["expires_in"]}
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't insert 'access_token' into database", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function select_access_token(
	){//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/cpservm/access_tokens';
		$_["expires_in"] = $DB->int(time());
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT `access_token` FROM `{$_["table"]}` WHERE `expires_in`>{$_["expires_in"]} LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't select 'access_token' from database", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		return($array[0]["access_token"]);
		
	}//}}}//

	static function get_access_token()
	{//{{{//
	
		$access_token = self::select_access_token();
		if(is_string($access_token)) {
			return($access_token);
		}
		if($access_token !== NULL) {
			trigger_error("Can't select 'access_token'", E_USER_WARNING);
			return(false);
		}
		
		$url = 'https://cpservm.com/gateway/token';
		
		$client_id = urlencode(self::$ClientId);
		$client_secret = urlencode(self::$ClientSecret);
		
		HTTP::$headers = ['Content-Type: application/x-www-form-urlencoded'];
		
		$data = "client_id={$client_id}&client_secret={$client_secret}";

		$return = HTTP::POST($url, $data);
		
		if(!is_array($return)) {
			trigger_error("Can't perform HTTP POST for get 'access_token'", E_USER_WARNING);
			return(false);
		}
		
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
		
		$array = json_decode($http_body, true);
		if(!is_array($array)) {
			if (defined('DEBUG') && DEBUG) var_dump(['HTTP body' => $http_body]);
			trigger_error("Can't decode json from HTTP body", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$string.='$array["access_token"]')) return(false);
		$access_token = $array["access_token"];
		
		if(!eval(Check::$int.='$array["expires_in"]')) return(false);
		$expires_in = time() + $array["expires_in"];
		
		$return = self::insert_access_token($access_token, $expires_in);
		if(!$return) {
			trigger_error("Can't insert 'access token' to database", E_USER_WARNING);
			return(false);
		}
		
		return($access_token);
		
	}//}}}//

	// Request

	static function perform_request(string $url)
	{//{{{//
		
		$access_token = self::get_access_token();
		if(!is_string($access_token)) {
			trigger_error("Can't get acccess token", E_USER_WARNING);
			return(false);
		}
		
		$pattern = '/^([^\x20-\xFF]+)$/';
		$access_token = preg_replace($pattern, '', $access_token);
		
		$header = "Authorization: Bearer {$access_token}";
		HTTP::$headers = [$header];
		
		$return = HTTP::GET($url);
		if(!is_array($return)) {
			trigger_error("Can't perform HTTP GET", E_USER_WARNING);
			return(false);
		}
		
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
	
		$array = json_decode($http_body, true);
		if(!is_array($array)) {
			if (defined('DEBUG') && DEBUG) var_dump(['HTTP body' => $http_body]);
			trigger_error("Can't decode json from HTTP body", E_USER_WARNING);
			return(false);
		}
		
		return($array);
		
	}//}}}//
	
	static function insert_request(array $data)
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/cpservm/requests';
		$_["data"] = $DB->encode($data);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`data`
) VALUES (
	'{$_["data"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform insert 'request data' query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		
		return($id);
		
	}//}}}//

	static function select_request(int $id)
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/cpservm/requests';
		$_["id"] = $DB->int($id);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}` WHERE `id`={$_["id"]} LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform select 'request' query", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		$result= json_decode($array[0]["data"], true);
		
		return($result);
		
	}//}}}//

	// Request handlers
	
	static function get_languages(int $id = 0)
	{//{{{//
		
		if($id > 0) {
			$array = self::select_request($id);
			if(!is_array($array)) {
				trigger_error("Can't get HTTP request data from database", E_USER_WARNING);
				return(false);
			}
			return($array);
		}
		
		$url = "https://cpservm.com/gateway/marketing/datafeed/directories/api/v1/language";
		$array = self::perform_request($url);
		if(!is_array($array)) {
			trigger_error("Can't perform HTTP request to remote database", E_USER_WARNING);
			return(false);
		}
		
		$result = [];
		
		$result['languages'] = [];
		foreach($array as $language) {
			if(!eval(Check::$string.='$language["uriName"]')) return(false);
			if(!eval(Check::$string.='$language["localized"]')) return(false);
			
			array_push($result['languages'], [
				"lng" => $language["uriName"],
				"name" => $language["localized"],
			]);
		}
		
		if(!eval(Check::$int.='self::$site')) return(false);
		$result['site'] = self::$site;
		
		$id = self::insert_request($result);
		if(!is_int($id)) {
			trigger_error("Can't insert HTTP request to database", E_USER_WARNING);
			return(false);
		}
		
		return($id);
		
	}//}}}//

	static function get_sports(int $id = 0)
	{//{{{//
		
		if($id > 0) {
			$array = self::select_request($id);
			if(!is_array($array)) {
				trigger_error("Can't get HTTP request data from database", E_USER_WARNING);
				return(false);
			}
			return($array);
		}
		
		if(!eval(Check::$string.='self::$ref')) return(false);
		if(!eval(Check::$string.='self::$lng')) return(false);
		
		$url = 
			"https://cpservm.com/gateway/marketing/datafeed/directories/api/v2/sports"
			."?ref=".urlencode(self::$ref)
			."&lng=".urlencode(self::$lng)
		;
		$array = self::perform_request($url);
		if(!is_array($array)) {
			trigger_error("Can't perform HTTP request to remote database", E_USER_WARNING);
			return(false);
		}
		
		$result = ["sports" => []];
		
		foreach($array["items"] as $item) {
			if(!eval(Check::$int.='$item["id"]')) return(false);
			if(!eval(Check::$string.='$item["name"]')) return(false);
		
			array_push($result["sports"], [
				"id" => $item["id"],
				"name" => $item["name"],
			]);
		}
		
		$result["site"] = self::$site;
		$result["lng"] = self::$lng;
		
		$id = self::insert_request($result);
		if(!is_int($id)) {
			trigger_error("Can't insert HTTP request to database", E_USER_WARNING);
			return(false);
		}
		
		return($id);
		
	}//}}}//

	static function get_tournaments(int $id = 0)
	{//{{{//
		
		if($id > 0) {
			$array = self::select_request($id);
			if(!is_array($array)) {
				trigger_error("Can't get HTTP request answer from database", E_USER_WARNING);
				return(false);
			}
			return($array);
		}
		
		if(!eval(Check::$string.='self::$ref')) return(false);
		if(!eval(Check::$string.='self::$lng')) return(false);
		if(!eval(Check::$int.='self::$sportId')) return(false);
		
		$url = 
			"https://cpservm.com/gateway/marketing/datafeed/loadtree/prematch/api/v1/tournaments"
			."?ref=".urlencode(self::$ref)
			."&lng=".urlencode(self::$lng)
			."&sportId=".urlencode(self::$sportId)
		;
		$array = self::perform_request($url);
		if(!is_array($array)) {
			trigger_error("Can't perform HTTP request to remote database", E_USER_WARNING);
			return(false);
		}
		
		$result = ["tournaments" => []];
		
		foreach($array["items"] as $item) {
			if(!eval(Check::$int.='$item["tournamentId"]')) return(false);
			if(!eval(Check::$string.='$item["tournamentNameLocalization"]')) return(false);
		
			array_push($result["tournaments"], [
				"id" => $item["tournamentId"],
				"name" => $item["tournamentNameLocalization"],
			]);
		}
		
		$result["site"] = self::$site;
		$result["lng"] = self::$lng;
		$result["sport"] = self::$sportId;
		
		$id = self::insert_request($result);
		if(!is_int($id)) {
			trigger_error("Can't insert HTTP request to database", E_USER_WARNING);
			return(false);
		}
		
		return($id);
		
	}//}}}//

	static function get_events(int $id = 0)
	{//{{{//
		
		if($id > 0) {
			$array = self::select_request($id);
			if(!is_array($array)) {
				trigger_error("Can't get HTTP request answer from database", E_USER_WARNING);
				return(false);
			}
			
			return($array);
		}
		
		if(!eval(Check::$string.='self::$ref')) return(false);
		if(!eval(Check::$string.='self::$lng')) return(false);
		if(!eval(Check::$int.='self::$tournamentId')) return(false);
		
		$url = 
			"https://cpservm.com/gateway/marketing/datafeed/loadtree/prematch/api/v1/sportEventIds"
			."?ref=".urlencode(self::$ref)
			."&tournamentId=".urlencode(self::$tournamentId)
		;
		$array = self::perform_request($url);
		if(!is_array($array)) {
			trigger_error("Can't perform HTTP request to remote database", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$array.='$array["items"]')) return(false);
		$sportEventIds = $array["items"];
		
		// Get sport events by id
		
		$result = ["events" => []];
		foreach($sportEventIds as $sportEventId) {
			$url = 
				"https://cpservm.com/gateway/marketing/datafeed/loadtree/prematch/api/v1/sporteventDetail"
				."?ref=".urlencode(self::$ref)
				."&lng=".urlencode(self::$lng)
				."&sportEventId=".urlencode($sportEventId)
			;
			$item = self::perform_request($url);
			if(!is_array($item)) {
				trigger_error("Can't perform HTTP request to remote database", E_USER_WARNING);
				return(false);
			}
			
			$array = [];
			
			$array["event"] = intval($sportEventId);
			
			$array["location"] = '';
			if(isset($item["matchInfoObject"]["location"]))
				$array["location"] = $item["matchInfoObject"]["location"];
			
			$array["opponents"] = ['', ''];
			if(isset($item["opponent1NameLocalization"])) 
				$array["opponents"][0] = $item["opponent1NameLocalization"];
			if(isset($item["opponent2NameLocalization"]) && strlen($item["opponent2NameLocalization"]) > 0)
				$array["opponents"][1] = $item["opponent2NameLocalization"];
			
			$array["date"] = 0;
			if(isset($item["startDate"]))
				$array["date"] = $item["startDate"];
			
			
			array_push($result["events"], $array);
		}
		
		$result["site"] = self::$site;
		$result["tournament"] = self::$tournamentName;
		
		// Save result to database
		
		$id = self::insert_request($result);
		if(!is_int($id)) {
			trigger_error("Can't insert HTTP request to database", E_USER_WARNING);
			return(false);
		}
		
		return($id);

	}//}}}//

}

