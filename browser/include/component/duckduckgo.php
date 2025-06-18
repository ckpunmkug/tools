<?php

class duckduckgo
{
	static $input = NULL;
	static $output = NULL;
	
	static $TABLE = [
		"query" => NULL,
		"result" => NULL,
	];
	
	static function main(array $config, string $action, array $data)
	{//{{{//
		
		$return = self::set_config($config);
		if(!$return) {
			trigger_error("Set config failed", E_USER_WARNING);
			return(false);
		}
	
		$return = self::perform_action($action, $data);
		if(!$return) {
			trigger_error("Can't perform action", E_USER_WARNING);
			return(false);
		}
	
		return(true);
		
	}//}}}//
	
	static function set_config(array $config)
	{//{{{//
		
		if(!eval(C::$S.='$config["table"]["query"]')) return(false);
		if(!Data::check_table_name($config["table"]["query"])) {
			trigger_error("Incorrect 'query' table name", E_USER_WARNING);
			return(false);
		}
		self::$TABLE["query"] = $config["table"]["query"];
		
		if(!eval(C::$S.='$config["table"]["result"]')) return(false);
		if(!Data::check_table_name($config["table"]["result"])) {
			trigger_error("Incorrect 'result' table name", E_USER_WARNING);
			return(false);
		}
		self::$TABLE["result"] = $config["table"]["result"];
		
		return(true);
		
	}//}}}//

	static function perform_action(string $action, array $data)
	{//{{{//
	
		$return = self::create_tables();
		if(!$return) {
			trigger_error("Can't create tables", E_USER_WARNING);
			return(false);
		}
	
		switch($action) {
			case('reset'):
				$return = self::reset();
				if(!$return) {
					trigger_error("Reset action failed", E_USER_WARNING);
					return(false);
				}
				return(true);
	
			case('put_search_result'):
				$return = self::put_search_result($data);
				if(!$return) {
					trigger_error("Can't put search result to database", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('get_queries'):
				$return = self::get_queries();
				if(!$return) {
					trigger_error("Can't get queries from database", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('get_results'):
				$return = self::get_results();
				if(!$return) {
					trigger_error("Can't get results from database", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('get_next_query'):
				$return = $this->get_next_query();
				if($return === false) {
					trigger_error("Can't get next search query", E_USER_WARNING);
					return(false);
				}
				$this->R = $return;
				return($return);
				
			default:
				if (defined('DEBUG') && DEBUG) var_dump(['action' => $action]);
				trigger_error("Unsupported action", E_USER_WARNING);
				return(false);
		}
		
	}//}}}//

/// ACTIONS

	static function reset()
	{//{{{//
	
		$return = self::drop_tables();
		if(!$return) {
			trigger_error("Can't drop tables", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function put_search_result(array $data)
	{//{{{//
	
		$input = &self::$input;
		$output = &self::$output;
		
		if(true) // get input data
		{//{{{//
		
			if(@is_string($data["query"]) != true) {
				trigger_error('Incorrect $data["query"] string', E_USER_WARNING);
				return(false);
			}
			$input["query"] = $data["query"];
			
			if(@is_array($data["queries"]) != true) {
				trigger_error('Incorrect $data["queries"] array', E_USER_WARNING);
				return(false);
			}
			$input["queries"] = [];
			
			foreach($data["queries"] as $key => $value) {
				if(is_string($value) != true) {
					trigger_error('Incorrect $data["queries"][$key] string', E_USER_WARNING);
					return(false);
				}
				array_push($input["queries"], $value);
			}
			
			if(@is_array($data["results"]) != true) {
				trigger_error('Incorrect $data["results"] array', E_USER_WARNING);
				return(false);
			}
			$input["results"] = [];
		
			foreach($data["results"] as $key => $value) {
				if(is_array($value) != true) {
					trigger_error('Incorrect $data["results"][$key] array', E_USER_WARNING);
					return(false);
				}
				$array = [];
				
				if(is_string($value["url"]) != true) {
					trigger_error('Incorrect $data["results"][$key]["url"] string', E_USER_WARNING);
					return(false);
				}
				$array["url"] = $value["url"];
				
				if(is_string($value["title"]) != true) {
					trigger_error('Incorrect $data["results"][$key]["title"] string', E_USER_WARNING);
					return(false);
				}
				$array["title"] = $value["title"];
				
				if(is_string($value["description"]) != true) {
					trigger_error('Incorrect $data["results"][$key]["description"] string', E_USER_WARNING);
					return(false);
				}
				$array["description"] = $value["description"];
				
				array_push($input["results"], $array);
			}	
			
		}//}}}//
		
		if(true) // put query
		{//{{{//
			put_query_label:
			
			$table = self::$TABLE["query"];
			$text = base64_encode($input["query"]);
			$sql =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}' WHERE text='{$text}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$QUERY = Data::query($sql);
			if(!is_array($QUERY)) {
				trigger_error("Can't perform select database query", E_USER_WARNING);
				return(false);
			}
			
			if(count($QUERY) == 0) {
				$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$table}' 
	(parent, level, text, state)
 VALUES
	(0, 0, '{$text}', 0);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$return = Data::exec($sql);
				if(!$return) {
					trigger_error("Can't perform insert database query", E_USER_WARNING);
					return(false);
				}
				goto put_query_label;
			}
			else {
				$query = $QUERY[0];
				$query["text"] = base64_decode($query["text"]);
			}
			
			$output["query"] = $query;
		}//}}}//
		
		if(true) // put queries
		{//{{{//
			$table = self::$TABLE["query"];
			$parent = $output["query"]["id"];
			$level = $output["query"]["level"]+1;
		
			$output["queries"] = [];
			foreach($input["queries"] as $query) {
				$text = base64_encode($query);
				
				put_queries_label:
				$sql =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}' WHERE text='{$text}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$QUERY = Data::query($sql);
				if(!is_array($QUERY)) {
					trigger_error("Can't perform select database query", E_USER_WARNING);
					return(false);
				}
				if(count($QUERY) > 0) {
					array_push($output["queries"], $QUERY[0]);
					continue;
				}
				
				$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$table}' 
	(parent, level, text, state)
 VALUES
	({$parent}, {$level}, '{$text}', 0);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$return = Data::exec($sql);
				if(!$return) {
					trigger_error("Can't perform insert database query", E_USER_WARNING);
					return(false);
				}
				goto put_queries_label;
			}
		}//}}}//
		
		if(true) // put results
		{//{{{//
			$table = self::$TABLE["result"];
			$query = $output["query"]["id"];
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM '{$table}' WHERE query={$query};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$return = Data::exec($sql);
			if(!$return) {
				trigger_error("Can't perform delete database query", E_USER_WARNING);
				return(false);
			}
			
			foreach($input["results"] as $result) {
				$url = base64_encode($result["url"]);
				$title = base64_encode($result["title"]);
				$description = base64_encode($result["description"]);
				$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$table}' 
	(query, url, title, description)
 VALUES
	({$query}, '{$url}', '{$title}', '{$description}');
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$return = Data::exec($sql);
				if(!$return) {
					trigger_error("Can't perform insert database query", E_USER_WARNING);
					return(false);
				}
			}
			
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}' WHERE query={$query};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$RESULT = Data::query($sql);
			if(!is_array($RESULT)) {
				trigger_error("Can't perform select database query", E_USER_WARNING);
				return(false);
			}
			
			$output["results"] = [];
			foreach($RESULT as $result) {
				$result["url"] = base64_decode($result["url"]);
				$result["title"] = base64_decode($result["title"]);
				$result["description"] = base64_decode($result["description"]);
				
				array_push($output["results"], $result);
			}
		}//}}}//
		
		if(true) // update query status
		{//{{{//
			$table = self::$TABLE["query"];
			$id = $output["query"]["id"];
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE '{$table}' SET state=3 WHERE id={$id};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$return = Data::exec($sql);
			if(!$return) {
				trigger_error("Can't perform update database query", E_USER_WARNING);
				return(false);
			}
			
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}' WHERE id={$id};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$QUERY = Data::query($sql);
			if(!is_array($QUERY)) {
				trigger_error("Can't perform select database query", E_USER_WARNING);
				return(false);
			}
			$output["query"] = $QUERY[0];
			$output["query"]["text"] = base64_decode($QUERY[0]["text"]);
		}//}}}//
		
		return(true);
	
	}//}}}//

	static function get_queries()
	{//{{{//
		
		$table = self::$TABLE["query"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$queries = Data::query($sql);
		if(!is_array($queries)) {
			trigger_error("Can't perform select database query", E_USER_WARNING);
			return(false);
		}
		
		self::$output = [];
		foreach($queries as $query) {
			$query["text"] = base64_decode($query["text"]);
			array_push(self::$output, $query);
		}
		
		return(true);
		
	}//}}}//

	static function get_results()
	{//{{{//
		
		$table = self::$TABLE["result"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$table}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$RESULT = Data::query($sql);
		if(!is_array($RESULT)) {
			trigger_error("Can't perform select database query", E_USER_WARNING);
			return(false);
		}
		
		self::$output = [];
		foreach($RESULT as $result) {
			$result["url"] = base64_decode($result["url"]);
			$result["title"] = base64_decode($result["title"]);
			$result["description"] = base64_decode($result["description"]);
			array_push(self::$output, $result);
		}
		
		return(true);
		
	}//}}}//

/// DATABASE
	
	static function create_tables()
	{//{{{//
		
		$table = self::$TABLE["query"];
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
		
		$table = self::$TABLE["result"];
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
	
	static function drop_tables()
	{//{{{//
		
		$table = self::$TABLE["query"];
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
		
		$table = self::$TABLE["result"];
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
}

