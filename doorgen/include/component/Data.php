<?php

class Data
{
	static function create_tables()
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		// Create 'site' table
		$_["table"] = "/main/site";
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`title` TEXT
	,`description` TEXT
	,`keywords` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}

		// Create 'tournaments' table
		$_["table"] = "/main/tournaments";
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`id` INT
	,`translit` TEXT
	,`title` TEXT
	,`description` TEXT
	,`keywords` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		// Create 'events' table
		$_["table"] = "/main/events";
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`id` INT
	,`tournament` INT
	,`location` TEXT
	,`opponents` TEXT
	,`date` INT
	,`translit` TEXT
	,`title` TEXT
	,`description` TEXT
	,`keywords` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		// Create 'predictions' table
		$_["table"] = "/main/predictions";
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`event` INT
	,`content` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

// Site ////////////////////////////////////////////////////////////////////////

	static function insert_site(
		string $title,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/site';
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM `{$_["table"]}`;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database 'query'", E_USER_WARNING);
			return(false);
		}
		
		$_["id"] = '0';
		$_["title"] = $DB->escape($title);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
	
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`title`, `description`, `keywords`
) VALUES (
	'{$_["title"]}', '{$_["description"]}', '{$_["keywords"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		
		return($id);
		
	}//}}}//

	static function select_site(
	) {//{{{//
		
		$DB = new DB();
		$_ = [];
		
		$_["table"] = '/main/site';
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}`;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}

		if(count($array) == 0) {
			$array = ["title" => '', "description" => '', "keywords" => ''];
		}
		else {
			$array = $array[0];
		}
	
		return($array);
		
	}//}}}//

// Tournament //////////////////////////////////////////////////////////////////

	static function insert_tournament(
		int $id,
		string $translit,
		string $title,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		$_["id"] = $DB->int($id);
		$_["translit"] = $DB->escape($translit);
		$_["title"] = $DB->escape($title);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`id`, `translit`, `title`, `description`, `keywords`
) VALUES (
	{$_["id"]}, '{$_["translit"]}', '{$_["title"]}', '{$_["description"]}', '{$_["keywords"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return($id);
		
	}//}}}//
	
	static function update_tournament(
		int $id,
		string $translit,
		string $title,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		$_["id"] = $DB->int($id);
		$_["translit"] = $DB->escape($translit);
		$_["title"] = $DB->escape($title);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`translit`='{$_["translit"]}'
	,`title`='{$_["title"]}'
	,`description`='{$_["description"]}'
	,`keywords`='{$_["keywords"]}'
 WHERE `id`={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);

	}//}}}//
	
	static function delete_tournament(
		int $id
	) {//{{{//
		
		$events = self::select_events($id);
		if(!is_array($events)) {
			trigger_error("Can't select 'events'", E_USER_WARNING);
			return(false);
		}
		
		foreach($events as $event) {
			$return = self::delete_event($event);
			if(!$return) {
				trigger_error("Can't delete 'event'", E_USER_WARNING);
				return(false);
			}
		}
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		$_["id"] = $DB->int($id);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM `{$_["table"]}` WHERE `id`={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function select_tournament(
		int $id
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		$_["id"] = $DB->int($id);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}` WHERE `id`={$_["id"]} LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		return($array[0]);
		
	}//}}}//
	
	static function select_tournament_by_translit(
		string $translit
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		$_["translit"] = $DB->escape($translit);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}` WHERE `translit`='{$_["translit"]}' LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		return($array[0]);
		
	}//}}}//
	
	static function select_tournaments(
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT `id` FROM `{$_["table"]}`;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$result = [];
		foreach($array as $item) {
			array_push($result, $item['id']);
		}
		
		return($result);
		
	}//}}}//
	
// Event ///////////////////////////////////////////////////////////////////////

	static function insert_event(
		int $id,
		int $tournament,
		string $location,
		array $opponents,
		int $date,
		string $translit,
		string $title,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["id"] = $DB->int($id);
		$_["tournament"] = $DB->int($tournament);
		$_["location"] = $DB->escape($location);
		$_["opponents"] = $DB->encode($opponents);
		$_["date"] = $DB->int($date);
		$_["translit"] = $DB->escape($translit);
		$_["title"] = $DB->escape($title);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`id`, `tournament`, `location`, `opponents`, `date`, `translit`, `title`, `description`, `keywords`
) VALUES (
	{$_["id"]}, {$_["tournament"]}, '{$_["location"]}', '{$_["opponents"]}', {$_["date"]}, '{$_["translit"]}', '{$_["title"]}', '{$_["description"]}', '{$_["keywords"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$return = self::insert_prediction($id, []);
		if(!$return) {
			trigger_error("Can't insert 'prediction'", E_USER_WARNING);
			return(false);
		}
		
		return($id);
		
	}//}}}//

	static function update_event(
		int $id,
		int $tournament,
		string $location,
		array $opponents,
		int $date,
		string $translit,
		string $title,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["id"] = $DB->int($id);
		$_["tournament"] = $DB->int($tournament);
		$_["location"] = $DB->escape($location);
		$_["opponents"] = $DB->encode($opponents);
		$_["date"] = $DB->int($date);
		$_["translit"] = $DB->escape($translit);
		$_["title"] = $DB->escape($title);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`tournament`={$_["tournament"]}
	,`location`='{$_["location"]}'
	,`opponents`='{$_["opponents"]}'
	,`date`={$_["date"]}
	,`translit`='{$_["translit"]}'
	,`title`='{$_["title"]}'
	,`description`='{$_["description"]}'
	,`keywords`='{$_["keywords"]}'
 WHERE `id`={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function delete_event(
		int $id,
	) {//{{{//
		
		$return = self::delete_prediction($id);
		if(!$return) {
			trigger_error("Can't delete 'prediction'", E_USER_WARNING);
			return(false);
		}
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["id"] = $DB->int($id);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM `{$_["table"]}` WHERE `id`={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function select_event(
		int $id,
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["id"] = $DB->int($id);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}` WHERE `id`={$_["id"]} LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		$event = $array[0];
		
		$array = json_decode($event["opponents"], true);
		if(!is_array($array)) {
			trigger_error("Can't decode 'opponents' json", E_USER_WARNING);
			return(false);
		}
		
		$event["opponents"] = $array;
		
		return($event);
		
	}//}}}//

	static function select_event_by_translit(
		int $tournament,
		string $translit
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["tournament"] = $DB->int($tournament);
		$_["translit"] = $DB->escape($translit);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}` WHERE `tournament`={$_["tournament"]} AND `translit`='{$_["translit"]}' LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		$event = $array[0];
		
		$array = json_decode($event["opponents"], true);
		if(!is_array($array)) {
			trigger_error("Can't decode 'opponents' json", E_USER_WARNING);
			return(false);
		}
		
		$event["opponents"] = $array;
		
		return($event);
		
	}//}}}//

	static function select_events(
		int $tournament
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["tournament"] = $DB->int($tournament);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT `id` FROM `{$_["table"]}` WHERE `tournament`={$_["tournament"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		$result = [];
		foreach($array as $item) {
			array_push($result, $item["id"]);
		}
		
		return($result);
		
	}//}}}//

	static function select_all_events_count()
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(*) FROM `{$_["table"]}`;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);	
		}
		
		return($array[0]["COUNT(*)"]);
		
	}//}}}//

	static function select_tournament_events_count(int $tournament)
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["tournament"] = $DB->int($tournament);
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT COUNT(*) FROM `{$_["table"]}` WHERE `tournament`={$_["tournament"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);	
		}
		
		return($array[0]["COUNT(*)"]);
		
	}//}}}//

	static function select_all_events()
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["timestamp"] = $DB->int(time());
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT `id` FROM `{$_["table"]}` ORDER BY `date` ASC;
HEREDOC;
//SELECT `id` FROM `{$_["table"]}` WHERE `date`>{$_["timestamp"]} ORDER BY `date` ASC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);	
		}
		
		$result = [];
		foreach($array as $item) {
			array_push($result, $item["id"]);
		}
		
		return($result);
		
	}//}}}//

// Prediction //////////////////////////////////////////////////////////////////

	static function insert_prediction(
		int $event,
		array $content,
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/predictions';
		$_["event"] = $DB->int($event);
		$_["content"] = $DB->encode($content);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`event`, `content`
) VALUES (
	{$_["event"]}, '{$_["content"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function update_prediction(
		int $event,
		array $content,
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/predictions';
		$_["event"] = $DB->int($event);
		$_["content"] = $DB->encode($content);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`content`='{$_["content"]}'
WHERE `event`={$_["event"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function delete_prediction(
		int $event
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/predictions';
		$_["event"] = $DB->int($event);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM `{$_["table"]}` WHERE `event`={$_["event"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function select_prediction(
		int $event
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/predictions';
		$_["event"] = $DB->int($event);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT `content` FROM `{$_["table"]}` WHERE `event`={$_["event"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		$result = $DB->decode($array[0]["content"]);
		if(!is_array($result)) {
			trigger_error("Can't decode 'content'", E_USER_WARNING);
			return(false);
		}
		
		return($result);
		
	}//}}}//

}


