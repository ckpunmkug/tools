<?php

class anthropic
{
	static $url = CONFIG["anthropic"]["url"];
	static $api_key = CONFIG["anthropic"]["api_key"];
	
	static function send_message(string $message) // array
	{//{{{//
		
		HTTP::$headers = [
			"x-api-key: ".self::$api_key,
			"anthropic-version: 2023-06-01",
			"Content-Type: application/json",
		];
		
		$messages = [["role" => "user", "content" => $message]];
		
		$data = json_encode([
			"model" => "claude-sonnet-4-20250514",
			"max_tokens" => 1024,
			
			/* Ha:E6ka - Be6 nouck oTcyTcTByeT
			"tools" => [
				[
					"name" => "web_search",
					"description" => "Search the web",
					"input_schema" => [
						"type" => "object",
						"properties" => [
							"query" => [
								"type" => "string",
								"description" => "Search query",
							],
						],
						"required" => ["query"],
					],
				],
			],
			*/
			
			"messages" => $messages,
		], JSON_PRETTY_PRINT);
		
		$return = HTTP::POST(self::$url, $data);
		if(!is_array($return)) {
			trigger_error("Can't perform HTTP POST request", E_USER_WARNING);
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
		
		$answer = json_decode($http_body, true);
		if(!is_string(@$answer["content"][0]["text"])){
			if (defined('DEBUG') && DEBUG) var_dump(['$http_body' => $http_body]);
			trigger_error("Incorrect AI answer", E_USER_WARNING);
			return(false);
		}
		
		return($answer["content"][0]["text"]);
		
	}//}}}//
	
}
