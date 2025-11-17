<?php

class Job
{

	static function create_tables()
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/job/names';
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`id` INT PRIMARY KEY AUTO_INCREMENT
	,`name` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't preform create table 'job names' query", E_USER_WARNING);
			return(false);
		}
		
		$_["table"] = '/job/tasks';
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`id` INT PRIMARY KEY AUTO_INCREMENT
	,`job` INT
	,`state` INT DEFAULT 0
	,`parameters` TEXT
	,`result` TEXT DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't preform create table 'job tasks' query", E_USER_WARNING);
			return(false);
		}
		
		return(true);

	}//}}}//
	
	static function insert_job(string $name)
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/job/names';
		$_["name"] = $DB->escape($name);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`name`
 ) VALUES (
	'{$_["name"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform insert 'job name' query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		
		return($id);
		
	}//}}}//

	static function insert_task(int $job, array $parameters)
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/job/tasks';
		$_["job"] = $DB->int($job);
		$_["parameters"] = $DB->encode($parameters);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`job`, `parameters`
 ) VALUES (
	{$_["job"]}, '{$_["parameters"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't insert 'job task' query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		
		return(true);
		
	}//}}}//
	
	static function select_task(int $job)
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/job/tasks';
		$_["job"] = $DB->int($job);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}` WHERE `job`={$_["job"]} AND state=0 LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform select 'task' query", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		$task = $array[0];
		$task["parameters"] = json_decode($task["parameters"], true);
		
		return($task);
		
	}//}}}//

	static function update_task_state(int $id, int $state)
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/job/tasks';
		$_["id"] = $DB->int($id);
		$_["state"] = $DB->int($state);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`state`={$_["state"]}
 WHERE `id`={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform update 'task state' query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

}

