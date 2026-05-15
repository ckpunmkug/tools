<?php

data::$create["SEARCH_QUERY"] = function()
{//{{{//

	$table_name = "SEARCH_QUERY";
	$columns_describe = [
			"parent" => 0,
			"pattern" => '',
	];
	$return = Database::create_table($table_name, $columns_describe);
	if(!$return) {
		trigger_error("Can't create table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$drop["SEARCH_QUERY"] = function()
{//{{{//

	$table_name = "SEARCH_QUERY";
	$return = Database::drop_table($table_name);
	if(!$return) {
		trigger_error("Can't drop table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$get["SEARCH_QUERY"] = function()
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'SEARCH_QUERY';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

	$SEARCH_QUERY = Database::query($sql);
	if(!is_array($SEARCH_QUERY)) {
		trigger_error("Can't SELECT * FROM 'SEARCH_QUERY'", E_USER_WARNING);
		return(false);
	}

	return($SEARCH_QUERY);
	
};//}}}//

data::$add["search_query"] = function(int $parent, string $pattern)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO 'SEARCH_QUERY' (parent, pattern) VALUES (:parent, :pattern);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"parent" => $parent,
		"pattern" => $pattern,
	];
	
	$id = Database::query($sql, $data);
	if(!is_int($id)) {
		trigger_error("Can't INSERT INTO 'SEARCH_QUERY'", E_USER_WARNING);
		return(false);
	}
	
	return($id);
	
};//}}}//

data::$delete["search_query"] = function(int $id)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'SEARCH_QUERY' WHERE parent=:parent;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"parent" => $id,
	];
	
	$SEARCH_QUERY = Database::query($sql, $data);
	if(!is_array($SEARCH_QUERY)) {
		trigger_error("Can't SELECT * FROM 'SEARCH_QUERY'", E_USER_WARNING);
		return(false);
	}
	
	foreach($SEARCH_QUERY as $search_query) {
		data::$delete["search_query"]($search_query["id"]);
	}
	
	$return = data::$delete["SEARCH_RESULT"]($id);
	if(!$return) {
		trigger_error("Can't delete 'SEARCH_RESULT'", E_USER_WARNING);
		return(false);
	}
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM 'SEARCH_QUERY' WHERE id=:id;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"id" => $id,
	];
	
	$return = Database::query($sql, $data);
	if(!$return) {
		trigger_error("Can't DELETE FROM 'SEARCH_QUERY'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

