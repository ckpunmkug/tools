<?php

class Data
{
	static function create_tables()
	{//{{{//
		
		$DB = new DB;
		$_ = [];
		
		// Create 'sites' table
		$_["table"] = "/main/sites";
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`id` INT PRIMARY KEY AUTO_INCREMENT
	,`url` TEXT
	,`user` TEXT
	,`password` TEXT
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
		
		// Create 'tournaments' table
		$_["table"] = "/main/tournaments";
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`id` INT PRIMARY KEY AUTO_INCREMENT
	,`site` INT
	,`name` TEXT
	,`description` TEXT
	,`keywords` TEXT
	,`tournamentId` INT
	,`lng` TEXT
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
	`id` INT PRIMARY KEY AUTO_INCREMENT
	,`tournament` INT
	,`remote` INT
	,`location` TEXT
	,`opponents` TEXT
	,`date` INT
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
		
		// Create 'prompts' table
		$_["table"] = "/main/prompts";
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["table"]}`;
CREATE TABLE `{$_["table"]}` (
	`id` INT PRIMARY KEY AUTO_INCREMENT
	,`tournament` INT
	,`prompt` TEXT
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
	`id` INT PRIMARY KEY AUTO_INCREMENT
	,`event` INT
	,`prediction` TEXT
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
		string $url,
		string $user,
		string $password,
		string $title,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/sites';
		$_["url"] = $DB->escape($url);
		$_["user"] = $DB->escape($user);
		$_["password"] = $DB->escape($password);
		$_["title"] = $DB->escape($title);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`url`, `user`, `password`, `title`, `description`, `keywords`
) VALUES (
	'{$_["url"]}', '{$_["user"]}', '{$_["password"]}', '{$_["title"]}', '{$_["description"]}', '{$_["keywords"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		
		Site::set_site($id);
		
		return($id);
		
	}//}}}//
	
	static function select_sites(
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/sites';
		
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
			array_push($result, $item["id"]);
		}

		return($result);
		
	}//}}}//
	
	static function select_site(
		int $id
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/sites';
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
	
	static function update_site(
		int $id,
		string $url,
		string $user,
		string $password,
		string $title,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/sites';
		$_["id"] = $DB->int($id);
		$_["url"] = $DB->escape($url);
		$_["user"] = $DB->escape($user);
		$_["password"] = $DB->escape($password);
		$_["title"] = $DB->escape($title);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
	
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`url`='{$_["url"]}'
	,`user`='{$_["user"]}'
	,`password`='{$_["password"]}'
	,`title`='{$_["title"]}'
	,`description`='{$_["description"]}'
	,`keywords`='{$_["keywords"]}'
 WHERE `id`={$_["id"]}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		Site::set_site($id);
		
		return(true);
		
	}//}}}//

	static function delete_site(
		int $id
	){//{{{//
		
		$return = Data::delete_tournaments($id);
		if(!$return) {
			trigger_error("Can't delete 'tournaments'", E_USER_WARNING);
			return(false);
		}
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/sites';
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

// Tournament //////////////////////////////////////////////////////////////////

	static function insert_tournament(
		int $site,
		string $name,
		string $description,
		string $keywords,
		int $tournamentId,
		string $lng
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		$_["site"] = $DB->int($site);
		$_["name"] = $DB->escape($name);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		$_["tournamentId"] = $DB->int($tournamentId);
		$_["lng"] = $DB->escape($lng);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`site`, `name`, `description`, `keywords`, `tournamentId`, `lng`
) VALUES (
	{$_["site"]}, '{$_["name"]}', '{$_["description"]}', '{$_["keywords"]}', {$_["tournamentId"]}, '{$_["lng"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		
		$return = Site::add_tournament($id);
		if(!$return) {
			trigger_error("Can't add 'tournament' to site", E_USER_WARNING);
		}
		
		return($id);
		
	}//}}}//
	
	static function select_tournaments(
		int $site
	){//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		$_["site"] = $DB->int($site);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT `id` FROM `{$_["table"]}` WHERE `site`={$_["site"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$result = [];
		foreach($array as $a) {
			array_push($result, $a["id"]);
		}
		
		return($result);
		
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
	
	static function update_tournament(
		int $id,
		int $site,
		string $name,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/tournaments';
		$_["id"] = $DB->int($id);
		$_["site"] = $DB->int($site);
		$_["name"] = $DB->escape($name);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`site`={$_["site"]}
	,`name`='{$_["name"]}'
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
		
		$return = Site::change_tournament($id);
		if(!$return) {
			trigger_error("Can't change 'tournament' on site", E_USER_WARNING);
		}
		
		return(true);

	}//}}}//
	
	static function delete_tournaments (
		int $site
	) {//{{{//
		
		$tournaments = Data::select_tournaments($site);
		if($tournaments === false) {
			trigger_error("Can't select 'tournaments'", E_USER_WARNING);
			return(false);
		}
		
		if(is_array($tournaments)) {
			foreach($tournaments as $id) {
				$return = Data::delete_tournament($id);
				if(!$return) {
					trigger_error("Can't delete 'tournament'", E_USER_WARNING);
					return(false);
				}
			}
		}
		
		return(true);
		
	}//}}}//
	
	static function delete_tournament(
		int $id
	){//{{{//
		
		$return = Site::delete_tournament($id);
		if(!$return) {
			trigger_error("Can't delete 'tournament' on site", E_USER_WARNING);
		}
		
		$prompt = Data::select_prompt($id);
		if($prompt === false) {
			trigger_error("Can't select 'prompt'", E_USER_WARNING);
			return(false);
		}
		if(is_array($prompt)) {
			$return = Data::delete_prompt($prompt["id"]);
			if(!$return) {
				trigger_error("Can't delete 'prompt'", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = Data::delete_events($id);		
		if(!$return) {
			trigger_error("Can't delete 'events'", E_USER_WARNING);
			return(false);
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
	
// Event ///////////////////////////////////////////////////////////////////////

	static function insert_event(
		int $tournament,
		int $remote,
		string $location,
		array $opponents,
		int $date,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["tournament"] = $DB->int($tournament);
		$_["remote"] = $DB->int($remote);
		$_["location"] = $DB->escape($location);
		$_["opponents"] = $DB->encode($opponents);
		$_["date"] = $DB->int($date);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`tournament`, `remote`, `location`, `opponents`, `date`, `description`, `keywords`
) VALUES (
	{$_["tournament"]}, {$_["remote"]}, '{$_["location"]}', '{$_["opponents"]}', {$_["date"]}, '{$_["description"]}', '{$_["keywords"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		
		$return = Site::add_event($id);
		if(!$return) {
			trigger_error("Can't add 'event' to site", E_USER_WARNING);
		}
		
		return($id);
		
	}//}}}//

	static function update_event(
		int $id,
		string $description,
		string $keywords
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["id"] = $DB->int($id);
		$_["description"] = $DB->escape($description);
		$_["keywords"] = $DB->escape($keywords);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`description`='{$_["description"]}'
	,`keywords`='{$_["keywords"]}'
 WHERE `id`={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$return = Site::update_event($id);
		if(!$return) {
			trigger_error("Can't update 'event' on site", E_USER_WARNING);
		}
		
		return(true);
		
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
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		$result = [];
		foreach($array as $a) {
			array_push($result, $a["id"]);
		}
		
		return($result);
		
	}//}}}//
	
	static function select_event(
		int $id
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/events';
		$_["id"] = $DB->int($id);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}` WHERE id={$_["id"]} LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		if(count($array) == 0) return(NULL);
		
		$array[0]["opponents"] = $DB->decode($array[0]["opponents"]);
		
		return($array[0]);
		
	}//}}}//
	
	static function delete_events(
		int $tournament
	) {//{{{//
		
		$events = Data::select_events($tournament);
		if($events === NULL) return(true);
		if(!is_array($events)) {
			trigger_error("Can't select 'events'", E_USER_WARNING);
			return(false);
		}
		
		foreach($events as $id) {
			$return = Data::delete_event($id);
			if(!$return) {
				trigger_error("Can't delete 'event'", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
		
	}//}}}//
	
	static function delete_event(
		int $id
	) {//{{{//
		
		$return = Site::delete_event($id);
		if(!$return) {
			trigger_error("Can't delete 'event' on site", E_USER_WARNING);
		}
		
		$prediction = Data::select_prediction($id);
		if($prediction === false) {
			trigger_error("Can't select 'prediction'", E_USER_WARNING);
			return(false);
		}
		if(is_array($prediction)) {
			$return = Data::delete_prediction($prediction["id"]);
			if(!$return) {
				trigger_error("Can't delete 'prediction'", E_USER_WARNING);
				return(false);
			}
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
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

// Prompt //////////////////////////////////////////////////////////////////////

	static function insert_prompt(
		int $tournament,
		string $prompt
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/prompts';
		$_["tournament"] = $DB->int($tournament);
		$_["prompt"] = $DB->escape($prompt);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`tournament`, `prompt`
 ) VALUES (
	{$_["tournament"]}, '{$_["prompt"]}'
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
	
	static function select_prompt(
		int $tournament
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/prompts';
		$_["tournament"] = $DB->int($tournament);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$_["table"]}` WHERE tournament={$_["tournament"]} LIMIT 1;
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
	
	static function update_prompt(
		int $id,
		int $tournament,
		string $prompt
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/prompts';
		$_["id"] = $DB->int($id);
		$_["tournament"] = $DB->int($tournament);
		$_["prompt"] = $DB->escape($prompt);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET 
	`tournament`={$_["tournament"]}
	,`prompt`='{$_["prompt"]}'
 WHERE `id`={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform databse query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function delete_prompt(
		int $id
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/prompts';
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
	
// Prediction //////////////////////////////////////////////////////////////////

	static function insert_prediction(
		int $event,
		string $prediction
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/predictions';
		$_["event"] = $DB->int($event);
		$_["prediction"] = $DB->escape($prediction);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`event`, `prediction`
) VALUES (
	{$_["event"]}, '{$_["prediction"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't preform database query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		
		$return = Site::change_prediction($event);	
		if(!$return) {
			trigger_error("Can't change 'prediction' on site", E_USER_WARNING);
		}
		
		return($id);
		
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
SELECT * FROM `{$_["table"]}` WHERE event={$_["event"]} LIMIT 1;
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
	
	static function update_prediction(
		int $id,
		int $event,
		string $prediction
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/predictions';
		$_["id"] = $DB->int($id);
		$_["event"] = $DB->int($event);
		$_["prediction"] = $DB->escape($prediction);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`event`={$_["event"]}
	,`prediction`='{$_["prediction"]}'
 WHERE `id`={$_["id"]};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$return = Site::change_prediction($event);	
		if(!$return) {
			trigger_error("Can't change 'prediction' on site", E_USER_WARNING);
		}
		
		return(true);
		
	}//}}}//
	
	static function delete_prediction(
		int $id
	) {//{{{//
		
		$DB = new DB;
		$_ = [];
		
		$_["table"] = '/main/predictions';
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

}

