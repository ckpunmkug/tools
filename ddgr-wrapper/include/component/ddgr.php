<?php

class ddgr
{
	static $ddgr_bin = '/usr/bin/ddgr';
	static $REGION = [];
	static $timeout = 1;
	
	static function get_REGION()
	{//{{{//
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'REGION';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$REGION = Data::query($sql);
		if(!is_array($REGION)){
			trigger_error("Can't get `REGION` array", E_USER_WARNING);
			return(false);
		}
		
		return($REGION);
		
	}//}}}//
	
	static function create_queries_table(string $name)
	{//{{{//
		
		$_["name"] = Data::name($name);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$_["name"]}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$_["name"]' => $_["name"]]);
			trigger_error("Can't drop `queries` table", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE TABLE '{$_["name"]}' (
	'id' INTEGER PRIMARY KEY
	,'query' TEXT
	,'region' INTEGER
	,'status' INTEGER DEFAULT 0
	,'result' TEXT DEFAULT ''
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$_["name"]' => $_["name"]]);
			trigger_error("Can't create `queries` table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function insert_query(string $table, string $query, int $region)
	{//{{{//
		
		$_ = [
			"table" => Data::name($table),
			"query" => Data::escape($query),
			"region" => Data::integer($region),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$_["table"]}'
 WHERE query='{$_["query"]}' AND region={$_["region"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = Data::query($sql);
		if(!is_array($array)) {
			if (defined('DEBUG') && DEBUG) var_dump([
				'$_["table"]' => $_["table"],
				'$_["query"]' => $_["query"],
			]);
			trigger_error("Can't select `query` from `queries` table", E_USER_WARNING);
			return(false);
		}
		
		$count = count($array);
		if($count > 0) {
			if (defined('DEBUG') && DEBUG) var_dump([
				'$_["table"]' => $_["table"],
				'$_["query"]' => $_["query"],
			]);
			trigger_error("`query` already exists in `queries` table", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$_["table"]}' (
	'query', 'region'
) VALUES (
	'{$_["query"]}', {$_["region"]}
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump([
				'$_["table"]' => $_["table"],
				'$_["query"]' => $_["query"],
			]);
			trigger_error("Can't insert `query` in to `queries` table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function process(string $table)
	{//{{{//
		
		$_ = [
			"name" => Data::name($table),
		];
		while(true) {
			
			// get query with status 0
			
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$_["name"]}' WHERE status=0 LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$array = Data::query($sql);
			if(!is_array($array)) {
				trigger_error("Can't select query with status 0", E_USER_WARNING);
				return(false);
			}
			if(empty($array)) {
				if(defined('VERBOSE') && VERBOSE) {
					user_error("No queries with status 0");
				}
				return(true);
			}
			$query = $array[0];
			foreach(self::$REGION as $region) {
				if($region["id"] == $query["region"]) {
					$region = $region["region"];
					break;
				}
			}
			
			// get count of queries where status zero
			
			if(defined('VERBOSE') && VERBOSE) {
				$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(*) FROM '{$_["name"]}' WHERE status=0;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$array = Data::query($sql);
				if(!is_array($array)) {
					trigger_error("Can't get count of queries where status zero", E_USER_WARNING);
					return(false);
				}
				$left = @intval($array[0]["COUNT(*)"]);
				
				echo("Left: {$left}  Query: {$query["query"]}  Region: {$region}\n");
			}
			
			// update query status to 1
			
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE '{$_["name"]}' SET status=1 WHERE id={$query["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$return = Data::exec($sql);
			if(!$return) {
				trigger_error("Can't update query status to 1", E_USER_WARNING);
				return(false);
			}
			
			$json = self::exec($query["query"], $region);
			//var_dump($json);
			/*
			return(true);
			if(is_string($json)) {
				self::update_status($query["id"], 2);
				self::update_result($query["id"], $json);
			}
			else {
				self::update_status($query["id"], 3);				
			}
			*/
			
			sleep(4);
			
		} // while(true)
		
	}//}}}//
	
	static function exec(string $keywords, string $region, int $number = 24)
	{//{{{//
		
		$man = 
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'

       -n, --num=N
              Show N results per page (default 10). N must be between 0 and 25. N=0 disables fixed paging and  shows  actual
              number of results fetched per page.

       -r, --reg=REG
              Region-specific search e.g. 'us-en' for US (default); visit https://duckduckgo.com/params.

       -C, --nocolor
              Disable color output.

       --unsafe
              Disable safe search.

       --noua Disable user agent. Results are fetched faster.

       --json Output in JSON format; implies --noprompt.


HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$_ = [
			"keywords" => escapeshellarg($keywords),
			"region" => escapeshellarg($region),
		];
		$command = self::$ddgr_bin
			." --num={$number}"
			." --reg={$_['region']}"
			." --nocolor"
			." --unsafe"
			//." --noua"
			." --json"
			." {$_['keywords']}"
		;
		$output = [];
		$status = 0;
		$return = exec($command, $output, $status);
		if(!is_string($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
			trigger_error("Exec ddgr command failed", E_USER_WARNING);
			return(false);
		}
		if($status != 0) {
			if (defined('DEBUG') && DEBUG) var_dump([
				'$command' => $command,
				'$status' => $status,
			]);
			trigger_error("ddgr command execution status is not zero", E_USER_WARNING);
			return(false);
		}
		
		$result = implode("\n", $output);
		return($result);
		
	}//}}}//
	
}

