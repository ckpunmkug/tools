<?php

class map
{

	static function launcher(string $table, callable $command_generator, int $launch_timeout)
	{//{{{//
		
		$return = Data::get_rows_count($table);
		if(!is_int($return)) {
			trigger_error("Can't get rows count for passed table", E_USER_WARNING);
			return(false);
		}
		$count = $return;
		
		for($offset = 0; $offset < $count; $offset += 1) {//
			$return = Data::get_item($offset);
			if(!is_array($return)) {
				trigger_error("Can't get item from passed table", E_USER_WARNING);
				return(false);
			}
			if($return === NULL) return(true);
			$item = $return;
			
			if($item["state"] > 0) continue;
			
			$command = $command_generator($item);
			if(!is_string($command)) {
				trigger_error("Can't create command with command generator", E_USER_WARNING);
				return(false);
			}
			
			$return = Data::set_item($table, $item["id"], ["state" => 1]);
			
			$result = launch($command, $launch_timeout);
			
		}// for($offset = 0; $offset < $count; $offset += 1)
		
	}//}}}//
	
	static function rolling()
	{
		if(!true) // loop for get every domain (template)
		{//{{{//
			
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(id) FROM '/map/DOMAIN';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$return = Data::query($sql);
			if(!is_array($return)) {
				trigger_error("Can't get count of domains", E_USER_WARNING);
				return(false);
			}
			$count = $return[0]['COUNT(id)'];
			
			for($offset = 0; $offset < $count; $offset += 1) {//
				$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '/map/DOMAIN' LIMIT 1 OFFSET {$offset};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$return = Data::query($sql);
				if(!is_array($return)) {
					trigger_error("Can't select domain with offset", E_USER_WARNING);
					return(false);
				}
				if(empty($return)) break;
				$id = $return[0]["id"];
				$domain = $return[0]["domain"];
				
				if(defined('VERBOSE') && VERBOSE) {
					echo("\roffset = {$offset}; count = {$count}; domain = {$domain}\n");
				}
			}// for($offset = 0; $offset < $count; $offset += 1)
			
		}//}}}//

		if(true) // create and fill '/map/index'
		{//{{{//

			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '/map/index';
CREATE TABLE '/map/index' (
	id INTEGER,
	domain TEXT,
	state INTEGER DEFAULT 0,
	time INTEGER DEFAULT 0,
	status INTEGER DEFAULT 0,
	stdout TEXT DEFAULT '',
	stderr TEXT DEFAULT ''
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$return = Data::exec($sql);
			if(!$return) {
				trigger_error("Can't create table for index data", E_USER_WARNING);
				return(false);
			}
			
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(id) FROM '/map/DOMAIN';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$return = Data::query($sql);
			if(!is_array($return)) {
				trigger_error("Can't get count of domains", E_USER_WARNING);
				return(false);
			}
			$count = $return[0]['COUNT(id)'];
			
			for($offset = 0; $offset < $count; $offset += 1) {//
				$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM '/map/DOMAIN' LIMIT 1 OFFSET {$offset};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$return = Data::query($sql);
				if(!is_array($return)) {
					trigger_error("Can't select domain with offset", E_USER_WARNING);
					return(false);
				}
				if(empty($return)) break;
				$id = $return[0]["id"];
				$domain = $return[0]["domain"];
				
				if(defined('VERBOSE') && VERBOSE) {
					echo("\roffset = {$offset}; count = {$count}; domain = {$domain}\n");
				}
				
				$_ = [
					"id" => Data::integer($id),
					"domain" => Data::escape($domain),
				];
				$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO '/map/index' (
	id, domain
) VALUES (
	{$_["id"]}, '{$_["domain"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$return = Data::exec($sql);
				if(!$return) {
					trigger_error("Can't insert domain into index table", E_USER_WARNING);
					return(false);
				}
				
			}// for($offset = 0; $offset < $count; $offset += 1)
			
		}//}}}//

		if(!true) // create and fill '/map/dig' table
		{//{{{//
			
			$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(id) FROM '/map/DOMAIN';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$return = Data::query($sql);
			if(!is_array($return)) {
				trigger_error("Can't get count of domains", E_USER_WARNING);
				return(false);
			}
			$count = $return[0]['COUNT(id)'];
			
			for($offset = 0; $offset < $count; $offset += 1) {//
				if(defined('VERBOSE') && VERBOSE) {
					echo("\roffset = {$offset}; count = {$count};");
				}
			
			}// for($offset = 0; $offset < $count; $offset += 1)
			
		}//}}}//
		
	}
	
	static function create_table(string $table)
	{//{{{//
		
		$table = Data::name($table);
		switch($table) {
			case('/map/DOMAIN'):
				$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS '{$table}';
CREATE TABLE '{$table}' (
	id INTEGER PRIMARY KEY,
	domain TEXT
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				break;
			default: 
				trigger_error("Unsupported table", E_USER_WARNING);
				return(false);
		}
		$return = Data::exec($sql);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$table' => $table]);
			trigger_error("Can't create table with passed name", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	
// load and analyze index from domains via tor
/*{{{

/map/DOMAIN
id, domain

}}}*/

	static function index_load()
	{//{{{//
		
	}//}}}//
	
}

