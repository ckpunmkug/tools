<?php

class ddgr
{
	static $regions_table = '/ddgr/regions';
	static $results_table = 'results';
	static $queries_table_prefix = '/ddgr/queries/';
	static $results_table_prefix = '/ddgr/results/';
	
	static $ddgr_bin = '/usr/bin/ddgr';
	static $timeout = 10;
	
	static function import_regions(string $regions_file_path)
	{//{{{//
		
		$contents = file_get_contents($regions_file_path);
		if(!is_string($contents)) {
			if (defined('DEBUG') && DEBUG) var_dump(['path to regions file' => $regions_file_path]);
			trigger_error("Can't get contents from regions file", E_USER_WARNING);
			return(false);
		}
		
		$return = self::create_regions_table();
		if(!$return) {
			trigger_error("Can't create regions table", E_USER_WARNING);
			return(false);
		}
		
		$array = explode("\n", $contents);
		$count = 0;
		foreach($array as $string) {
			$string = trim($string);
			if(empty($string)) continue;
			
			$return = self::insert_country_language_abbreviation($string);
			if(!$return) {
				trigger_error("Can't insert country language abbreviation into regions table", E_USER_WARNING);
				return(false);
			}
			
			$count += 1;
		}// foreach($array as $string)
		
		if(defined('VERBOSE') && VERBOSE) {
			echo("Added to regions table {$count} country language abbreviations\n");
		}
		
		return(true);
		
	}//}}}//
	
	static function create_regions_table()
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$regions_table),
		];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$_["table"]}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec drop table query failed", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE TABLE '{$_["table"]}' (
	id INTEGER PRIMARY KEY
	,country_language_abbreviation TEXT
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec create table query failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function insert_country_language_abbreviation(string $country_language_abbreviation)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$regions_table),
			"country_language_abbreviation" => Data::escape($country_language_abbreviation),
		];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$_["table"]}' (
	country_language_abbreviation
) VALUES (
	'{$_["country_language_abbreviation"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql); 
		if(!$return) {
			trigger_error("Exec insert query failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	
	/// methods for import queries
	
	static function import_queries(string $queries_file_path, string $queries_table_name)
	{//{{{//
		
		$contents = file_get_contents($queries_file_path);
		if(!is_string($contents)) {
			if (defined('DEBUG') && DEBUG) var_dump(['queries file path' => $queries_file_path]);
			trigger_error("Can't get contents from queries list file", E_USER_WARNING);
			return(false);
		}
		
		$array = explode("\n", $contents);
		$KEYWORDS = [];
		foreach($array as $string) {
			$string = trim($string);
			if(empty($string)) continue;
			array_push($KEYWORDS, $string);
		}
		
		$return = self::create_queries_table($queries_table_name);
		if(!$return) {
			trigger_error("Can't create queries table", E_USER_WARNING);
			return(false);
		}
		
		$regions = ddgr::get_regions();
		if(!is_array($regions)) {
			trigger_error("Can't get regions", E_USER_WARNING);
			return(false);
		}
		
		$count = 0;
		foreach($KEYWORDS as $keywords) {
			foreach($regions as $region) {
				$return = ddgr::insert_query($queries_table_name, $keywords, $region);
				if(!$return) {
					trigger_error("Can't insert query", E_USER_WARNING);
					return(false);
				}
				$count += 1;
			}
		}
		
		if(defined('VERBOSE') && VERBOSE) {
			echo("Added queries into database: {$count}\n");
		}
		
		return(true);
		
	}//}}}//
	
	static function create_queries_table(string $queries_table_name)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$queries_table_prefix.$queries_table_name),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$_["table"]}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec drop table query failed", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE TABLE '{$_["table"]}' (
	id INTEGER PRIMARY KEY
	,status INTEGER DEFAULT 0
	,keywords TEXT
	,region TEXT
	,json TEXT DEFAULT ''
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec create table query failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function get_regions()
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$regions_table),
		];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$_["table"]}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = Data::query($sql);
		if(!is_array($array)) {
			trigger_error("Exec select from table query failed", E_USER_WARNING);
			return(false);
		}
		
		$regions = [];
		foreach($array as $item) {
			$regions[$item["id"]] = $item["country_language_abbreviation"];
		}
		
		return($regions);
		
	}//}}}//
	
	static function insert_query(string $queries_table_name, string $keywords, string $region)
	{//{{{//
	
		$_ = [
			"table" => Data::name(self::$queries_table_prefix.$queries_table_name),
			"keywords" => Data::escape($keywords),
			"region" => Data::escape($region),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$_["table"]}' (
	keywords, region
) VALUES (
	'{$_["keywords"]}', '{$_["region"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec insert into query failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	
	/// methods for process
	
	static function process(string $queries_table_name)
	{//{{{//
		
		$statistics = ddgr::statistics($queries_table_name);
		if(!is_array($statistics)) {
			trigger_error("Can't get statistics", E_USER_WARNING);
			return(false);
		}
		$total = $statistics['total'];
		$left = $statistics['raw'];
	
		while(true) {
			
			if(defined('VERBOSE') && VERBOSE) {
				echo("\nTotal: {$total}  Left: {$left}");
			}
			
			$query = self::get_next_query($queries_table_name);
			if(!is_array($query)) {
				if(!$query) {
					trigger_error("Can't get next query", E_USER_WARNING);
					return(false);
				}
				return(true);
			}
			
			$return = self::update_query($queries_table_name, $query["id"], 1);
			if(!$return) {
				trigger_error("Can't update query status to 1", E_USER_WARNING);
				return(false);
			}
			
			$left -= 1;
			
			$json = ddgr::exec($query["keywords"], $query["region"]);
			if(!is_string($json)) {
				$return = self::update_query($queries_table_name, $query["id"], 2);
				if(!$return) {
					trigger_error("Can't update query status to 2", E_USER_WARNING);
				}
				trigger_error("Can't exec ddgr", E_USER_WARNING);
				return(false);
			}
			
			$return = self::update_query($queries_table_name, $query["id"], 3, $json);
			if(!$return) {
				trigger_error("Can't update query json", E_USER_WARNING);
				return(false);
			}
			
			sleep(self::$timeout);
		}// while(true)
	}//}}}//
	
	static function get_next_query(string $queries_table_name)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$queries_table_prefix.$queries_table_name),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$_["table"]}' WHERE status=0 LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = Data::query($sql);
		if(!is_array($array)) {
			trigger_error("Can't select query with status 0", E_USER_WARNING);
			return(false);
		}
		
		if(empty($array)) {
			return(true);
		}
		
		$query = $array[0];

		return($query);
		
	}//}}}//
	
	static function update_query(string $queries_table_name, int $id, int $status, string $json = '')
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$queries_table_prefix.$queries_table_name),
			"id" => Data::integer($id),
			"status" => Data::integer($status),
			"json" => Data::escape($json),
		];		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE '{$_["table"]}'
 SET status={$_["status"]}, json='{$_["json"]}'
 WHERE id={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec update query failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function exec(string $keywords, string $region, int $number = 24)
	{//{{{//
		if(defined('VERBOSE') && VERBOSE) {
			echo("\nkeywords: '{$keywords}'; region: '{$region}';\n"); }
		
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
	
	
	// get statistics
	
	static function statistics(string $queries_table_name)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$queries_table_prefix.$queries_table_name),
		];
		
		$result = [];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(*) FROM '{$_["table"]}'
 UNION ALL SELECT COUNT(*) FROM '{$_["table"]}' WHERE status=0
 UNION ALL SELECT COUNT(*) FROM '{$_["table"]}' WHERE status=1
 UNION ALL SELECT COUNT(*) FROM '{$_["table"]}' WHERE status=2
 UNION ALL SELECT COUNT(*) FROM '{$_["table"]}' WHERE status=3
;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = Data::query($sql);
		if(!is_array($array)) {
			trigger_error("Exec select with union query failed", E_USER_WARNING);
			return(false);
		}
		
		$result["total"] = $array[0]["COUNT(*)"];
		$result["raw"] = $array[1]["COUNT(*)"];
		$result["try"] = $array[2]["COUNT(*)"];
		$result["error"] = $array[3]["COUNT(*)"];
		$result["complete"] = $array[4]["COUNT(*)"];
		
		return($result);
		
	}//}}}//
	
	
	// create results
	
	static function create_results(string $queries_table_name)
	{//{{{//
		
		$QUERY_ID = self::get_QUERY_ID_with_status($queries_table_name, 3);
		if(!is_array($QUERY_ID)) {
			trigger_error("Can't get queries ids", E_USER_WARNING);
			return(false);
		}
		
		$return = self::create_results_table($queries_table_name);
		if(!$return) {
			trigger_error("Can't create results table", E_USER_WARNING);
			return(false);
		}
		
		foreach($QUERY_ID as $query_id) {
			$query = self::get_query_by_id($queries_table_name, $query_id);
			if(!is_array($query)) {
				trigger_error("Can't get query by id", E_USER_WARNING);
				return(false);
			}
			
			$RESULT = Data::decode($query["json"]);
			if(!is_array($RESULT)) {
				trigger_error("Can't decode query json to results", E_USER_WARNING);
				return(false);
			}
			
			foreach($RESULT as $result) {
				$result["query_id"] = $query_id;
				$return = self::insert_result($queries_table_name, $result);
				if(!$return) {
					trigger_error("Can't insert result", E_USER_WARNING);
					return(false);
				}
			}
		}// foreach($QUERY_ID as $query_id)
		
		return(true);
		
	}//}}}//
	
	static function get_QUERY_ID_with_status(string $queries_table_name, int $status)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$queries_table_prefix.$queries_table_name),
			"status" => Data::integer($status),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT id FROM '{$_["table"]}' WHERE status={$_["status"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = Data::query($sql);
		if(!is_array($array)) {
			trigger_error("Exec select query failed", E_USER_WARNING);
			return(false);
		}
		
		$QUERY_ID = [];
		foreach($array as $item) {
			array_push($QUERY_ID, $item["id"]);
		}
		
		return($QUERY_ID);
		
	}//}}}//
	
	static function create_results_table(string $results_table_name)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$results_table_prefix.$results_table_name),
		];
		
		$sql = 		
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$_["table"]}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec drop table query failed", E_USER_WARNING);
			return(false);
		}
		
		$sql = 		
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE TABLE '{$_["table"]}' (
	id INTEGER PRIMARY KEY
	,query_id INTEGER
	,url TEXT
	,title TEXT
	,abstract TEXT
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec create table query failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function get_query_by_id(string $queries_table_name, int $query_id)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$queries_table_prefix.$queries_table_name),
			"query_id" => Data::integer($query_id),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$_["table"]}' WHERE id={$_["query_id"]} LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = Data::query($sql);
		if(!is_array($array)) {
			trigger_error("Exec select query failed", E_USER_WARNING);
			return(false);
		}
		
		if(empty($array)) {
			trigger_error("Exec select query return empty", E_USER_WARNING);
			return(false);
		}
		
		return($array[0]);
		
	}//}}}//
	
	static function insert_result(string $results_table_name, array $result)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$results_table_prefix.$results_table_name),
		];
		
		$number = @Data::get_int($result["query_id"]);
		if(!is_int($number)) {
			trigger_error("Can't get query_id integer from result array", E_USER_WARNING);
			return(false);
		}
		$_["query_id"] = Data::integer($number);
		
		$string = @Data::get_string($result["url"]);
		if(!is_string($string)) {
			trigger_error("Can't get url string from result array", E_USER_WARNING);
			return(false);
		}
		$_["url"] = Data::escape($string);
		
		$string = @Data::get_string($result["title"]);
		if(!is_string($string)) {
			trigger_error("Can't get title string from result array", E_USER_WARNING);
			return(false);
		}
		$_["title"] = Data::escape($string);
		
		$string = @Data::get_string($result["abstract"]);
		if(!is_string($string)) {
			trigger_error("Can't get abstract string from result array", E_USER_WARNING);
			return(false);
		}
		$_["abstract"] = Data::escape($string);
		
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$_["table"]}' (
	query_id, url, title, abstract
) VALUES (
	{$_["query_id"]}, '{$_["url"]}', '{$_["title"]}', '{$_["abstract"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Exec insert query failed", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	
	// export urls to file
	
	static function export_urls(string $output_file_path)
	{//{{{//
		
		$output_file_resource = fopen($output_file_path, 'w');
		if(!is_resource($output_file_resource)) {
			trigger_error("Can't `fopen` `output_file_path` for write", E_USER_WARNING);
			return(false);
		}
		
		$results_count = self::get_results_count();
		if(!is_int($results_count)) {
			trigger_error("Can't get results count", E_USER_WARNING);
			return(false);
		}
		
		for($offset = 0; $offset < $results_count; $offset += 1) {
			if(defined('VERBOSE') && VERBOSE) {
				echo("\rOffset: {$offset}  Results count: {$results_count}\t\r");
			}
		
			$url = self::get_url_with_offset($offset);
			if(!is_string($url)) {
				trigger_error("Can't get url with offset", E_USER_WARNING);
				return(false);
			}
			
			$url = trim($url);
			if(empty($url)) continue;
			
			$return = fwrite($output_file_resource, "{$url}\n");
			if(!is_int($return)) {
				trigger_error("Can't write url to output file", E_USER_WARNING);
				return(false);
			}
		}
		
		fclose($output_file_resource);
		
		return(true);
		
	}//}}}//
	
	static function get_results_count()
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$results_table),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(*) FROM '{$_["table"]}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = Data::query($sql);
		if(!is_array($array)) {
			trigger_error("Can't select count from results table", E_USER_WARNING);
			return(false);
		}
		
		$results_count = $array[0]["COUNT(*)"];
		return($results_count);
		
	}//}}}//
	
	static function get_url_with_offset(int $offset)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$results_table),
			"offset" => Data::integer($offset),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT url FROM '{$_["table"]}' LIMIT 1 OFFSET {$_["offset"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = Data::query($sql);
		if(!is_array($array)) {
			trigger_error("Can't select url with offset from results table", E_USER_WARNING);
			return(false);
		}
		
		if(empty($array)) {
			trigger_error("Result of select url with offset is empty", E_USER_WARNING);
			return(false);
		}
		
		$url = $array[0]["url"];
		return($url);
		
	}//}}}//
	
}

