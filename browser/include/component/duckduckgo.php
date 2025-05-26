<?php

class duckduckgo
{
	var $TABLE = [
		"query" => NULL,
		"result" => NULL,
	];
	
	function __construct(array $config, string $action, string $data)
	{//{{{//
	
		$return = $this->set_config($config);
		if(!$return) {
			$error_string = "Set config failed";
			goto error_label;
		}
	
		if($action == 'reset') {
			$return = $this->reset();
			if(!$return) {
				$error_string = "'reset' action failed";
				goto error_label;
			}
			return(NULL);
		}
	
		$return = $this->main($action, $data);
		if(!$return) {
			$error_string = "'main' call failed";
			goto error_label;
		}
	
		return(NULL);
		
		error_label:
		trigger_error($error_string, E_USER_WARNING);
		throw new Exception("Component 'duckduckgo' failed");
		
	}//}}}//
	
	function set_config(array $config)
	{//{{{//
		
		if(!eval(C::$S.='$config["database"]["table"]["query"]')) return(false);
		if(!Data::check_table_name($config["database"]["table"]["query"])) {
			trigger_error("Incorrect 'query' table name", E_USER_WARNING);
			return(false);
		}
		$this->TABLE["query"] = $config["database"]["table"]["query"];
		
		if(!eval(C::$S.='$config["database"]["table"]["result"]')) return(false);
		if(!Data::check_table_name($config["database"]["table"]["result"])) {
			trigger_error("Incorrect 'result' table name", E_USER_WARNING);
			return(false);
		}
		$this->TABLE["result"] = $config["database"]["table"]["result"];
		
		return(true);
		
	}//}}}//

	function main(string $action, string $data)
	{//{{{//
	
		$return = $this->create_tables();
		if(!$return) {
			trigger_error("Can't create tables", E_USER_WARNING);
			return(false);
		}
	
		switch($action) {
		
			case('put_query_results'):
				$return = $this->put_query_results($data);
				if(!is_array($return)) {
					trigger_error("Can't put query results to database", E_USER_WARNING);
					return(false);
				}
				return($return);
				
			case('get_next_query'):
				$return = $this->get_next_query();
				if($return === false) {
					trigger_error("Can't get next search query", E_USER_WARNING);
					return(false);
				}
				return($return);
				
			default:
				if (defined('DEBUG') && DEBUG) var_dump(['action' => $action]);
				trigger_error("Unsupported action", E_USER_WARNING);
				return(false);
		}
		
	}//}}}//

/// ACTIONS

	function reset()
	{//{{{//
	
		$return = $this->drop_tables();
		if(!$return) {
			trigger_error("Can't drop tables", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	function put_query_results(string $data)
	{//{{{//
		
		$get_data_array_from_json = function(string $data)
		{//{{{//
			
			$R = [];
			
			$data = Data::json_decode($data);
			if(!is_array($data)) {
				trigger_error("Can't decode input data from json to array", E_USER_WARNING);
				return(false);
			}
			
			if(!eval(C::$S.='$data["query"]')) return(false);
			$R["query"] = $data["query"];
			
			if(!eval(C::$A.='$data["queries"]')) return(false);
			$R["queries"] = [];
			foreach($data["queries"] as $key => $value) {
				if(!eval(C::$S.='$data["queries"][$key]')) return(false);
				array_push($R["queries"], $data["queries"][$key]);
			}
			
			if(!eval(C::$A.='$data["results"]')) return(false);
			$R["results"] = [];
			foreach($data["results"] as $key => $value) {
				if(!eval(C::$A.='$data["results"][$key]')) return(false);
				if(!eval(C::$S.='$data["results"][$key]["url"]')) return(false);
				if(!eval(C::$S.='$data["results"][$key]["title"]')) return(false);
				if(!eval(C::$S.='$data["results"][$key]["description"]')) return(false);
				array_push($R["results"], [
					"url" => $data["results"][$key]["url"],
					"title" => $data["results"][$key]["title"],
					"description" => $data["results"][$key]["description"],
				]);
			}
			
			return($R);
			
		};//}}}//
	
		$data = $get_data_array_from_json($data);
		if(!is_array($data)) {
			trigger_error("Can't get data array from json", E_USER_WARNING);
			return(false);
		}
		
		$put_query = function (string $text)
		{//{{{//
			
			$query = $this->select_query_by_text($text);
			if($query === false) {
				trigger_error("Can't select 'query' by text from database", E_USER_WARNING);
				return(false);
			}
			if(is_array($query)) {
				return($query);
			}
			
			$query = [
				"parent" => 0,
				"level" => 0,
				"text" => $text,
				"state" => 0,
			];
			
			$query = $this->insert_query($query);
			if(!is_array($query)) {
				trigger_error("Can't insert 'query' to database", E_USER_WARNING);
				return(false);
			}
			
			return($query);
			
		};//}}}//
		$query = $put_query($data["query"]);
		if(!is_array($query)) {
			trigger_error("Can't put 'query' to database", E_USER_WARNING);
			return(false);
		}
		
		$put_queries = function(array $query, array $queries)
		{//{{{//
			
			if(!eval(C::$I.='$query["id"]')) return(false);
			$parent = $query["id"];
			
			if(!eval(C::$I.='$query["level"]')) return(false);
			$level = $query["level"]+1;
			
			$R = [];
			
			foreach($queries as $text) {
				$query = $this->select_query_by_text($text);
				if($query === false) {
					trigger_error("Can't select query by text", E_USER_WARNING);
					return(false);
				}
				
				if($query === NULL) {
					$query = [
						"parent" => $parent,
						"level" => $level,
						"text" => $text,
						"state" => 0,
					];
					$query = $this->insert_query($query);
					if(!is_array($query)) {
						trigger_error("Can't insert 'query' into database", E_USER_WARNING);
						return(false);
					}
				}
				
				array_push($R, $query);
			}
			
			return($R);
			
		};//}}}//
		$queries = $put_queries($query, $data["queries"]);
		if(!is_array($queries)) {
			trigger_error("Can't put 'queries' to database", E_USER_WARNING);
			return(false);
		}
		
		$put_results = function(array $query, array $results)
		{//{{{//
			
			if(!eval(C::$I.='$query["id"]')) return(false);
			$query = $query["id"];
			
			$r = $this->delete_results_by_query($query);
			if(!$r) {
				trigger_error("Can't delete 'results' by 'query'", E_USER_WARNING);
				return(false);
			}
			
			foreach($results as $key => $result) {
				$result["query"] = $query;
				$r = $this->insert_result($result);
				if(!is_array($r)) {
					trigger_error("Can't insert 'result' into database", E_USER_WARNING);
					return(false);
				}
				$results[$key] = $r;
			}
			
			return($results);
			
		};//}}}//
		$results = $put_results($query, $data["results"]);
		
		$query["state"] = 1;
		$r = $this->update_query_by_id($query);
		if(!$r) {
			trigger_error("Can't update 'query' by 'id' in database", E_USER_WARNING);
			return(false);
		}
		
		$R = [
			"query" => $query,
			"queries" => $queries,
			"results" => $results,
		];
		
		return($R);
	
	}//}}}//

	function get_next_query()
	{//{{{//
		
		$query = $this->select_query_by_state(0);
		if($query === false) {
			trigger_error("Can't select 'query' by 'state' from database", E_USER_WARNING);
			return(false);
		}
		
		return($query);
		
	}//}}}//

/// DATABASE
	
	function create_tables()
	{//{{{//
		
		$table = $this->TABLE["query"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE TABLE IF NOT EXISTS '{$table}' (
	id INTEGER PRIMARY KEY
	,parent INTEGER
	,level INTEGER
	,text TEXT
	,state INTEGER
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't create 'query' table", E_USER_WARNING);
			return(false);
		}
		
		$table = $this->TABLE["result"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE TABLE IF NOT EXISTS '{$table}' (
	id INTEGER PRIMARY KEY
	,query INTEGER
	,url TEXT
	,title TEXT
	,description TEXT
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't create 'result' table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	
	}//}}}//
	
	function drop_tables()
	{//{{{//
		
		$table = $this->TABLE["query"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$table}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't drop 'query' table", E_USER_WARNING);
			return(false);
		}
		
		$table = $this->TABLE["result"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$table}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't drop 'result' table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	
	}//}}}//
	
	function select_query_by_text(string $text)
	{//{{{//
		
		$text = base64_encode($text);
		
		$table = $this->TABLE["query"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}' WHERE text='{$text}' LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$query = Data::query($sql);
		if(!is_array($query)) {
			trigger_error("Can't perform select query", E_USER_WARNING);
			return(false);
		}
		
		if(count($query) == 0) return(NULL);
		
		$R = $query[0];
		$R["text"] = base64_decode($R["text"]);
		
		return($R);
		
	}//}}}//

	function select_query_by_state(int $state)
	{//{{{//
		
		$table = $this->TABLE["query"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}' WHERE state={$state} LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$r = Data::query($sql);
		if(!is_array($r)) {
			trigger_error("Can't perform select query", E_USER_WARNING);
			return(false);
		}
		
		if(count($r) == 0) return(NULL);
		
		$R = $r[0];
		$R["text"] = base64_decode($R["text"]);
		
		return($R);
		
	}//}}}//
	
	function update_query_by_id(array $query)
	{//{{{//
		
		if(!eval(C::$I.='$query["id"]')) return(false);
		$id = $query["id"];
		
		if(!eval(C::$I.='$query["parent"]')) return(false);
		$parent = $query["parent"];
		
		if(!eval(C::$I.='$query["level"]')) return(false);
		$level = $query["level"];
		
		if(!eval(C::$S.='$query["text"]')) return(false);
		$text = $query["text"];
		
		if(!eval(C::$I.='$query["state"]')) return(false);
		$state = $query["state"];

		$table = $this->TABLE["query"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE '{$table}' SET
	parent={$parent}
	,level={$level}
	,text='{$text}'
	,state={$state}
 WHERE id={$id};	
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$r = Data::exec($sql);
		if(!$r) {
			trigger_error("Can't perform update query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	function delete_results_by_query(int $query)
	{//{{{//
		
		$table = $this->TABLE["result"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM '{$table}' WHERE query={$query};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$r = Data::exec($sql);
		if(!$r) {
			trigger_error("Can't perform delete query", E_USER_WARNING);
			return(false);
		}
		return(true);
		
	}//}}}//
	
	function insert_query(array $query)
	{//{{{//
		
		if(!eval(C::$I.='$query["parent"]')) return(false);
		$parent = $query["parent"];
		
		if(!eval(C::$I.='$query["level"]')) return(false);
		$level = $query["level"];
		
		if(!eval(C::$S.='$query["text"]')) return(false);
		$text = base64_encode($query["text"]);
		
		if(!eval(C::$I.='$query["state"]')) return(false);
		$state = strval($query["state"]);
		
		$table = $this->TABLE["query"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$table}' 
	(parent, level, text, state)
 VALUES
	({$parent}, {$level}, '{$text}', {$state})
 RETURNING id;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform insert query", E_USER_WARNING);
			return(false);
		}
		$query["id"] = $return[0]["id"];
		
		return($query);
		
	}//}}}//
	
	function insert_result(array $result)
	{//{{{//
		
		if(!eval(C::$I.='$result["query"]')) return(false);
		$query = strval($result["query"]);
		
		if(!eval(C::$S.='$result["url"]')) return(false);
		$url = base64_encode($result["url"]);
		
		if(!eval(C::$S.='$result["title"]')) return(false);
		$title = base64_encode($result["title"]);
		
		if(!eval(C::$S.='$result["description"]')) return(false);
		$description = base64_encode($result["description"]);
		
		$table = $this->TABLE["result"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$table}' 
	(query, url, title, description)
 VALUES
	({$query}, '{$url}', '{$title}', '{$description}')
 RETURNING id;	
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform insert query", E_USER_WARNING);
			return(false);
		}
		$result["id"] = $return[0]["id"];
		
		return($result);
		
	}//}}}//

	static function get_input_data()
	{//{{{//
	
		/* data scheme
		{
			 "query": string
			,"queries": [ string_0 ... string_N ]
			,"results": [
				{
					"url": string
					,"title": string
					,"description: string
				}
				...
			]
		}
		*/
	
		$result = [];
		
		if(!isset($_POST["data"])) {
			$_POST["data"] = file_get_contents('php://input');
			if(!is_string($_POST["data"])) {
				trigger_error("Can't get contents from php://input", E_USER_WARNING);
				return(false);
			}
		}
	
		$data = Data::json_decode($_POST["data"]);
		if(!is_array($data)) {
			if (defined('DEBUG') && DEBUG) var_dump(['json' => $data]);
			trigger_error("Can't decode json data into array", E_USER_WARNING);
			return(false);
		}
		
		if(true) // $data["query"]
		{//{{{//
		
			if(!isset($data["query"])) {
				trigger_error('$data["query"] is not set', E_USER_WARNING);
				return(false);
			}
			if(!is_string($data["query"])) {
				trigger_error('$data["query"] is not string', E_USER_WARNING);
				return(false);
			}
			$result["query"] = $data["query"];
			
		}//}}}//
		
		if(true) // $data["queries"]
		{//{{{//
		
			if(!isset($data["queries"])) {
				trigger_error('$data["queries"] is not set', E_USER_WARNING);
				return(false);
			}
			if(!is_array($data["queries"])) {
				trigger_error('$data["queries"] is not array', E_USER_WARNING);
				return(false);
			}
			$result["queries"] = [];
			foreach($data["queries"] as $item) {
				if(!is_string($item)) {
					trigger_error('$data["queries"] item is not string', E_USER_WARNING);
					return(false);
				}
				array_push($result["queries"], $item);
			}
			
		}//}}}//
		
		if(true) // $data["results"]
		{//{{{//
			
			if(!isset($data["results"])) {
				trigger_error('$data["results"] is not set', E_USER_WARNING);
				return(false);
			}
			if(!is_array($data["results"])) {
				trigger_error('$data["results"] is not array', E_USER_WARNING);
				return(false);
			}
			$result["results"] = [];
			
			foreach($data["results"] as $item) {
				if(!is_array($item)) {
					trigger_error('$data["results"] item is not array', E_USER_WARNING);
					return(false);
				}
				
				if(!isset($item["url"])) {
					trigger_error('$item["url"] of $data["results"] is not set', E_USER_WARNING);
					return(false);
				}
				if(!is_string($item["url"])) {
					trigger_error('$item["url"] of $data["results"] is not string', E_USER_WARNING);
					return(false);
				}
				
				if(!isset($item["title"])) {
					trigger_error('$item["title"] of $data["results"] is not set', E_USER_WARNING);
					return(false);
				}
				if(!is_string($item["title"])) {
					trigger_error('$item["title"] of $data["results"] is not string', E_USER_WARNING);
					return(false);
				}
				
				if(!isset($item["description"])) {
					trigger_error('$item["description"] of $data["results"] is not set', E_USER_WARNING);
					return(false);
				}
				if(!is_string($item["description"])) {
					trigger_error('$item["description"] of $data["results"] is not string', E_USER_WARNING);
					return(false);
				}
				
				array_push($result["results"], [
					"url" => $item["url"],
					"title" => $item["title"],
					"description" => $item["description"],
				]);
			}
			
		}//}}}//
		
		return($result);
		
	}//}}}//

	static function put_query(string $query, int $parent_id, int $level)
	{//{{{//
		
		$table = self::$TABLE["queries"];
		$query = base64_encode($query);
		
		begin_label:
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}' WHERE query='{$query}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$result = Data::query($sql);
		if(!is_array($result)) {
			trigger_error("Can't perform database query - 'select from queries'", E_USER_WARNING);
			return(false);
		}
		
		if(count($result) > 0) {
			$result = $result[0];
			return($result);
		}
		
		$state = 0;
		$level = 0;
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$table}' (query, state, level) VALUES ('{$query}', {$state}, {$level});
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't perform database query - 'insert into queries'", E_USER_WARNING);
			return(false);
		}
		
		goto begin_label;
		
	}//}}}//
}

