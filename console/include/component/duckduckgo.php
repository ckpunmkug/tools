<?php

class duckduckgo
{
	static function main()
	{//{{{//
	
		$return = self::initialization();
		if(!$return) {
			trigger_error("Initialization failed", E_USER_WARNING);
			return(false);
		}
		
		$action = @strval($_GET["action"]);
		switch($action) {
			case('add_results'):
				$return = self::add_results();
				if(!$return) {
					trigger_error("Can't add results to database", E_USER_WARNING);
					return(false);
				}
				return(true);
			case('get_next_query'):
				$return = self::get_next_query();
				if(!$return) {
					trigger_error("Can't get next search query", E_USER_WARNING);
					return(false);
				}
				return(true);
			default:
				if (defined('DEBUG') && DEBUG) var_dump(['$action' => $action]);
				trigger_error("Unsupported action", E_USER_WARNING);
				return(false);
		}
		
	}//}}}//
	
	static function initialization()
	{//{{{//
	
		if(!defined('TABLES')) {
			trigger_error("'TABLES' constant not defined", E_USER_WARNING);
			return(false);
		}
		
		$SCHEMA = [
			'/duckduckgo/queries' => [
				'text' => '',
				'state' => 1,
			],
			'/duckduckgo/results' => [
				'query_id' => 0,
				'url' => '',
				'title' => '',
				'description' => '',
			],
		];
		
		foreach($SCHEMA as $table => $columns) {
		
			$return = @Data::get_string(TABLES[$table]);
			if(!is_string($return)) {
				trigger_error("Table '{$table}' is not set in config file", E_USER_WARNING);
				return(false);
			}
			$table = $return;
			
			$return = Data::table_exists($table);
			if(!$return) {
				$return = Data::create_table($table, $columns);
				if(!$return) {
					if (defined('DEBUG') && DEBUG) var_dump(['$table' => $table]);
					trigger_error("Can't create table in database", E_USER_WARNING);
					return(false);
				}
			}
			
		} // foreach($SCHEMA as $table => $columns)
		
		return(true);
		
	}//}}}//
	
	static function add_results()
	{//{{{//
		
		$_POST["data"] = file_get_contents('php://input');
		
		/* results data scheme
		{
			"queries": [ string_0 ... string_N ]
			,"query": {
				"text": string
				,"results": [
					{
						"url": string
						,"title": string
						,"description: string
					}
					...
				]
			}
		}
		*/
		
		$check_input = function()
		{//{{{//
			
			$result = [];
			
			// data
			
			$json = @Data::get_string($_POST["data"]);
			if(!is_string($json)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$_POST["data"]' => $_POST["data"]]);
				trigger_error("Can't get 'data' from POST", E_USER_WARNING);
				return(false);
			}
			
			$data = Data::decode($json);
			if(!is_array($data)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$json' => $json]);
				trigger_error("Can't decode 'data' from json", E_USER_WARNING);
				return(false);
			}
			
			// queries
			
			$queries = @Data::get_array($data["queries"]);
			if(!is_array($queries)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$data' => $data]);
				trigger_error("Can't get array 'queries' from data", E_USER_WARNING);
				return(false);
			}
			$result["queries"] = [];
			
			foreach($queries as $string) {
				if(!is_string($string)) {
					if (defined('DEBUG') && DEBUG) var_dump(['$queries' => $queries]);
					trigger_error("Item in 'queries' is not string", E_USER_WARNING);
					return(false);
				}
				array_push($result["queries"], $string);
			}
			
			// query
			
			$query = @Data::get_array($data["query"]);
			if(!is_array($query)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$data' => $data]);
				trigger_error("Can't get 'query' array from 'data'", E_USER_WARNING);
				return(false);
			}
			$result["query"] = [];
			
			$text = @Data::get_string($query["text"]);
			if(!is_string($text)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$query' => $query]);
				trigger_error("Can't get 'text' string from 'query'", E_USER_WARNING);
				return(false);
			}
			$result["query"]["text"] = $text;
			
			// query["results"]
			
			$results = @Data::get_array($query["results"]);
			if(!is_array($results)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$query' => $query]);
				trigger_error("Can't get 'results' array from 'query'", E_USER_WARNING);
				return(false);
			}
			$result["query"]["results"] = [];
			
			foreach($results as $array) {
				if(!is_array($array)) {
					if (defined('DEBUG') && DEBUG) var_dump(['$results' => $results]);
					trigger_error("Item in 'results' is not array", E_USER_WARNING);
					return(false);
				}
				
				$url = @Data::get_string($array["url"]);
				if(!is_string($url)) {
					if (defined('DEBUG') && DEBUG) var_dump(['$results' => $results]);
					trigger_error("Can't get string 'url' from 'results' item", E_USER_WARNING);
					return(false);
				}
				
				$title = @Data::get_string($array["title"]);
				if(!is_string($title)) {
					if (defined('DEBUG') && DEBUG) var_dump(['$results' => $results]);
					trigger_error("Can't get string 'title' from 'results' item", E_USER_WARNING);
					return(false);
				}
				
				$description = @Data::get_string($array["description"]);
				if(!is_string($description)) {
					if (defined('DEBUG') && DEBUG) var_dump(['$results' => $results]);
					trigger_error("Can't get string 'description' from 'results' item", E_USER_WARNING);
					return(false);
				}
				
				array_push($result["query"]["results"], [
					"url" => $url,
					"title" => $title,
					"description" => $description,
				]);
				
			} // foreach($results as $array)
			
			return($result);
			
		};//}}}//
		
		$data = $check_input();
		if(!is_array($data)) {
			trigger_error("Check input data failed", E_USER_WARNING);
			return(false);
		}
		
		$insert_query = function(string $text)
		{//{{{//
		
			$table = TABLES["/duckduckgo/queries"];
			$text = Data::text($text);
			
			$item = Data::select_item($table, "text='{$text}'");
			if($item === false) {
				trigger_error("Can't select 'query' by 'text'", E_USER_WARNING);
				return(false);
			}
			
			if($item === NULL) {
				$data = [
					'text' => $text,
				];
				$return = Data::insert_item($table, $data);
				if(!$return) {
					trigger_error("Can't insert 'text' to 'queries'", E_USER_WARNING);
					return(false);
				}
			}
			
			return(true);
			
		};//}}}//
		
		foreach($data["queries"] as $text) {
			$return = $insert_query($text);
			if(!$return) {
				trigger_error("Insert query failed", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = $insert_query($data["query"]["text"]);
		if(!$return) {
			trigger_error("Insert query failed", E_USER_WARNING);
			return(false);
		}
				
		$text = Data::text($data["query"]["text"]);
		$item = Data::select_item(TABLES["/duckduckgo/queries"], "text='{$text}'");
		if(!is_array($item)) {
			trigger_error("Can't select 'query' with passed 'text'", E_USER_WARNING);
			return(false);
		}
		$query_id = $item["id"];
		
		$table = Data::table(TABLES["/duckduckgo/results"]);
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM '{$table}' WHERE query_id=$query_id;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't delete past results", E_USER_WARNING);
			return(false);
		}
		
		foreach($data["query"]["results"] as $result) {
			$data = [
				'query_id' => $query_id,
				'url' => $result["url"],
				'title' => $result["title"],
				'description' => $result["description"],
			];
			$return = Data::insert_item(TABLES["/duckduckgo/results"], $data);
			if(!$return) {
				trigger_error("Can't insert 'result' into database", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = Data::update_item(TABLES["/duckduckgo/queries"], ["state" => 1], $query_id);
		if(!$return) {
			trigger_error("Can't update 'state' for 'query'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function get_next_query()
	{//{{{//
		
		$item = Data::select_item(TABLES["/duckduckgo/queries"], "state=0");
		if($item === false) {
			trigger_error("Can't get get 'query' with 'state=0'", E_USER_WARNING);
			return(false);
		}
		
		if($item === NULL) return(NULL);
		
		return($item["text"]);
		
	}//}}}//
}

