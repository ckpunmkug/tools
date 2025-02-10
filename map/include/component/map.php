<?php

class map
{
/*
  ["id"]=>
  int(1)
  ["domain"]=>
  string(15) "www.alavert.com"
  ["count_in_list"]=>
  int(89)
  ["status"]=>
  int(0)
  ["nslookup_output"]=>
  string(0) ""
  ["count_of_addresses"]=>
  int(0)
*/
	
	static $domains_table = 'domains';
	
	static function import_urls(string $input_file_path)
	{//{{{//
	
		if(defined('VERBOSE') && VERBOSE) {
			$get_count_of_strings = function(string $file_path)
			{//{{{//
				
				$return = fopen($file_path, 'r');
				if(!is_resource($return)) {
					if (defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
					trigger_error("Can't open file for read", E_USER_WARNING);
					return(false);
				}
				$file_resource = $return;
				
				$count_of_string = 0;
				while(true) {
					$string = fgets($file_resource);
					if(!is_string($string)) break;
					$count_of_string += 1;
				}
				
				fclose($file_resource);
				
				return($count_of_string);
				
			};//}}}//
			$return = $get_count_of_strings($input_file_path);
			if(!is_int($return)) {
				trigger_error("Can't get count of strings in file", E_USER_WARNING);
				return(false);
			}
			$count_of_strings = $return;
		}
		
		$return = self::domains_create_table();
		if(!$return) {
			trigger_error("Can't create domains table", E_USER_WARNING);
			return(false);
		}
		
		$return = fopen($input_file_path, 'r');
		if(!is_resource($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$input_file_path' => $input_file_path]);
			trigger_error("Can't open urls list file for read", E_USER_WARNING);
			return(false);
		}
		$input_file_resource = $return;
		
		while(true) { //
			if(defined('VERBOSE') && VERBOSE) {
				echo("\rLeft: {$count_of_strings}\t");
				$count_of_strings -= 1;
			}
			
			$string = fgets($input_file_resource);
			if(!is_string($string)) break;
			
			$string = trim($string);
			if(empty($string)) continue;
			
			$domain = parse_url($string, PHP_URL_HOST);
			if(!is_string($domain)) {
				trigger_error("Can't parse url", E_USER_WARNING);
				continue;
			}
			
			$return = self::domain_get_data($domain);
			if($return === false) {
				trigger_error("Can't get domain data", E_USER_WARNING);
				return(false);
			}
			
			if($return === NULL) {
				$return = self::domain_add($domain);
				if(!$return) {
					trigger_error("Can't add domain", E_USER_WARNING);
					return(false);
				}
				continue;
			}
			
			if(is_array($return)) {
				$data = $return;
				$data["count_in_list"] += 1;
				$return = self::domain_set_data($data);
				if(!$return) {
					trigger_error("Can't set domain data", E_USER_WARNING);
					return(false);
				}
				continue;
			}
		} // while(true)
		
		return(true);
		
	}//}}}//
	
	static function domains_create_table()
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$domains_table),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$_["table"]}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't perform database query: drop domains table", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE TABLE '{$_["table"]}' (
	id INTEGER PRIMARY KEY
	,domain TEXT
	,count_in_list INTEGER DEFAULT 1
	,status INTEGER DEFAULT 0
	,nslookup_output TEXT DEFAULT ''
	,count_of_addresses INTEGER DEFAULT 0
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't perform database query: create domains table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function domains_get_count(string $where = '')
	{//{{{//
		
		$_ = [];
		$_["table"] = Data::name(self::$domains_table);
		$appendix = '';
		
		if(!empty($where)) 
			$appendix .= " WHERE {$where}";
		
		$sql =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(id) FROM '{$_["table"]}'{$appendix};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::query($sql);
		if(!is_array($return)) 
			return warning("Can't perform query: select count");
			
		$domains_count = $return[0]["COUNT(id)"];	
		return($domains_count);
		
	}//}}}//
	
	static function domain_get_data(string $what = '*', string $where = '', int $limit = -1, int $offset = -1)
	{//{{{//
		
		$_ = [];
		$_["table"] = Data::name(self::$domains_table);
		
		$appendix = '';
		if(!empty($where)) 
			$appendix .= ' WHERE '.$where;
		if($limit > 0) {
			$appendix .= ' LIMIT '.Data::integer($limit);
			if($offset > 0)
				$appendix .= ' OFFSET '.Data::integer($offset);
		}
		
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '{$_["table"]}'{$appendix};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform query: select domain data", E_USER_WARNING);
			return(false);
		}
		
		if(empty($return))
			return(NULL);
		
		if($limit == 1)
			return($return[0]);
		
		return($return);
		
	}//}}}//
	
	static function domain_add(string $domain)
	{//{{{//
		
		$_ = [
			"table" => Data::name(self::$domains_table),
			"domain" => Data::escape($domain),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$_["table"]}' (domain) VALUES ('{$_["domain"]}');
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't insert domain into table", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function domain_set_data(array $data)
	{//{{{//
		
		$check_data = function (array $data)
		{//{{{//
			
			$_ = [];
			
			$return = @Data::get_int($data["id"]);
			if(!is_int($return)) {
				trigger_error("Can't get data id", E_USER_WARNING);
				return(false);
			}
			$_["id"] = Data::integer($return);
			
			$return = @Data::get_string($data["domain"]);
			if(!is_string($return)) {
				trigger_error("Can't get data domain", E_USER_WARNING);
				return(false);
			}
			$_["domain"] = Data::escape($return);
			
			$return = @Data::get_int($data["count_in_list"]);
			if(!is_int($return)) {
				trigger_error("Can't get data count_in_list", E_USER_WARNING);
				return(false);
			}
			$_["count_in_list"] = Data::integer($return);
			
			$return = @Data::get_int($data["status"]);
			if(!is_int($return)) {
				trigger_error("Can't get data status", E_USER_WARNING);
				return(false);
			}
			$_["status"] = Data::integer($return);
			
			$return = @Data::get_string($data["nslookup_output"]);
			if(!is_string($return)) {
				trigger_error("Can't get data nslookup_output", E_USER_WARNING);
				return(false);
			}
			$_["nslookup_output"] = Data::escape($return);
			
			$return = @Data::get_int($data["count_of_addresses"]);
			if(!is_int($return)) {
				trigger_error("Can't get data count_of_addresses", E_USER_WARNING);
				return(false);
			}
			$_["count_of_addresses"] = Data::integer($return);
			
			return($_);
			
		};//}}}//
		$return = $check_data($data);
		if(!is_array($return)) {
			trigger_error("Check data failed", E_USER_WARNING);
			return(false);
		}
		$_ = $return;
		
		$_["table"] = Data::name(self::$domains_table);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE '{$_["table"]}'
 SET
	domain='{$_["domain"]}'
	,count_in_list={$_["count_in_list"]}
	,status={$_["status"]}
	,nslookup_output='{$_["nslookup_output"]}'
	,count_of_addresses={$_["count_of_addresses"]}
 WHERE id={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("Can't perform query: update domain", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function domains_nslookup()
	{//{{{//
		
		$domains_count = self::domains_get_count('status=0');
		if(!is_int($domains_count))
			return warning("Can't get domains count where status is 0");
		
		while(true) {//
			if(defined('VERBOSE') && VERBOSE)
				echo(sprintf("\r Left: %06d", $domains_count));
			$domains_count -= 1;
		
			$domain_data = self::domain_get_data('*', 'status=0', 1);
			if($domain_data === NULL)
				return(true);
			if(!is_array($domain_data))
				return warning("Can't get domain data where status is 0");
			
			$domain_data["status"] = 1;
			$return = self::domain_set_data($domain_data);
			if(!$return)
				return warning("Can't set domain data with status 1");
			
			$_ = [];
			$_["domain"] = escapeshellarg($domain_data["domain"]);
			$command = "/usr/bin/nslookup {$_['domain']} 127.0.0.1";
			$nslookup_output = launch($command);
			
			if(is_string($nslookup_output)) {
				$domain_data["status"] = 3;
				$domain_data["nslookup_output"] = $nslookup_output;
			}
			else {
				warning("Can't launch nslookup");
				$domain_data["status"] = 2;
			}
			
			$return = self::domain_set_data($domain_data);
			if(!$return)
				return warning("Can't set domain data with new status after launch nslookup");
			
			sleep(2);
			
		}// while(true)
		
	}//}}}//
	
	static function test()
	{//{{{//
		
		//ec/ho("\nTECT\n");
		
		$return = self::domains_get_count('status=3');
		$count = $return;
		$result = [];
		for($offset = 0; $offset < $count; $offset += 1) {
			$return = self::domain_get_data('*', 'status=3', 1, $offset);
			$text = $return["nslookup_output"];
			$STRING = explode("\n", $text);
			$index = count($STRING);
			if(!key_exists($index, $result))
				$result[$index] = 0;
			$result[$index] += 1;
			if($index == 7) {
				echo("{$STRING[4]}\n{$STRING[5]}\n");
				
			}
		}
		
	}//}}}//
	
	// S00 BblTackuBa+O goMeHbl c ogHuM agpecoM coxpaHR+O B Ta6JLuLLy S00 - domain ip
	
	static function S00_create_table()
	{//{{{//
		
		$_ = [
			"table" => 'S00',
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$_["table"]}';
CREATE TABLE '{$_["table"]}' (
	id INTEGER PRIMARY KEY,
	domain TEXT,
	ip TEXT
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return){
			trigger_error("", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function S00_get_nslookup_output(int &$offset)
	{//{{{//
		
		label_begin:
		
		$_ =[
			"table" => 'S00',
			"offset" => Data::integer($offset),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'domains' WHERE status=3 LIMIT 1 OFFSET {$_["offset"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::query($sql);
		if(!is_array($return)) {
			trigger_error("", E_USER_WARNING);
			return(false);
		}
		
		if(empty($return)) return(NULL);
		
		$text = $return[0]["nslookup_output"];
		$STRING = explode("\n", $text);
		$count = count($STRING);
		
		if($count != 7) {
			$offset += 1;
			goto label_begin;
		}
		
		$data = [
			$STRING[4],
			$STRING[5],
		];
		return($data);
		
	}//}}}//
	
	static function S00_put_data(string $domain, string $ip)
	{//{{{//
		
		$table = 'S00';
		
		$_ = [
			"table" => Data::name($table),
			"domain" => Data::escape($domain),
			"ip" => Data::escape($ip),
		];
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '{$_["table"]}' (
	domain, ip
) VALUES (
	'{$_["domain"]}', '{$_["ip"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = Data::exec($sql);
		if(!$return) {
			trigger_error("", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
}

