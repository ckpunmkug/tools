<?php

data::$create["PHP_FILE"] = function()
{//{{{//

	$table_name = "PHP_FILE";
	$columns_describe = [
			"path" => '',
	];
	$return = Database::create_table($table_name, $columns_describe);
	if(!$return) {
		trigger_error("Can't create table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$drop["PHP_FILE"] = function()
{//{{{//

	$table_name = "PHP_FILE";
	$return = Database::drop_table($table_name);
	if(!$return) {
		trigger_error("Can't drop table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$add["PHP_FILE"] = function(array $PHP_FILE)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM 'PHP_FILE';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$return = Database::query($sql);
	if(!$return) {
		trigger_error("Can't DELETE FROM 'PHP_FILE'", E_USER_WARNING);
		return(false);
	}
	
	$cd = count($PHP_FILE);
	foreach($PHP_FILE as $path) {
		cd($cd);
		
		if(!eval(Check::$string.='$path')) return(false);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO 'PHP_FILE' (path) VALUES (:path);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$data = [
				"path" => $path,
			];
			
			$return = Database::query($sql, $data);
			if(!$return) {
				trigger_error("Can't INSERT INTO 'PHP_FILE'", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
	
};//}}}//

data::$get["PHP_FILE"] = function()
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'PHP_FILE';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

	$ROW = Database::query($sql);
	if(!is_array($ROW)) {
		trigger_error("Can't SELECT * FROM 'PHP_FILE'", E_USER_WARNING);
		return(false);
	}
	
	$PHP_FILE = [];
	foreach($ROW as $row) {
		$PHP_FILE[$row["id"]] = $row["path"];
	}
	
	return($PHP_FILE);
	
};//}}}//

