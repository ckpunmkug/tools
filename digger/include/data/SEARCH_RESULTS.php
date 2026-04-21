<?php

data::$create["SEARCH_RESULTS"] = function()
{//{{{//

	$table_name = "SEARCH_RESULTS";
	$columns_describe = [
			"query" => 0,
			"file" => '',
			"number" => 0,
			"line" => '',
	];
	$return = Database::create_table($table_name, $columns_describe);
	if(!$return) {
		trigger_error("Can't create table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$drop["SEARCH_RESULTS"] = function()
{//{{{//

	$table_name = "SEARCH_RESULTS";
	$return = Database::drop_table($table_name);
	if(!$return) {
		trigger_error("Can't drop table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$add["SEARCH_RESULT"] = function(int $query, array $SEARCH_RESULT)
{//{{{//
	
	foreach($SEARCH_RESULT as $search_result) {
		
		if(!eval(Check::$string.='$search_result["file"]')) return(false);
		if(!eval(Check::$int.='$search_result["number"]')) return(false);
		if(!eval(Check::$string.='$search_result["line"]')) return(false);
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO 'SEARCH_RESULTS' (query, file, number, line) VALUES (:query, :file, :number, :line);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$data = [
			"query" => $query,
			"file" => $search_result["file"],
			"number" => $search_result["number"],
			"line" => $search_result["line"],
		];
		
		$id = Database::query($sql, $data);
		if(!is_int($id)) {
			trigger_error("Can't INSERT INTO 'SEARCH_RESULTS'", E_USER_WARNING);
			return(false);
		}
		
	}// foreach($SEARCH_RESULT as $search_result)
	
	return(true);
	
};//}}}//

data::$get["SEARCH_RESULT"] = function(int $query)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'SEARCH_RESULTS' WHERE query=:query;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"query" => $query,
	];
	
	$SEARCH_RESULT = Database::query($sql, $data);
	if(!is_array($SEARCH_RESULT)) {
		trigger_error("Can't SELECT * FROM 'SEARCH_RESULTS'", E_USER_WARNING);
		return(false);
	}
	
	return($SEARCH_RESULT);
	
};//}}}//

data::$delete["SEARCH_RESULT"] = function(int $query)
{//{{{/
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'SEARCH_RESULTS' WHERE query=:query;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"query" => $query,
	];
	
	$SEARCH_RESULT = Database::query($sql, $data);
	if(!$SEARCH_RESULT) {
		trigger_error("SELECT * FROM 'SEARCH_RESULTS'", E_USER_WARNING);
		return(false);
	}
	
	$ID = [];
	foreach($SEARCH_RESULT as $search_result) {
		array_push($ID, $search_result["id"]);
	}
	
	$return = data::$delete["search_results"]($ID);
	if(!$return) {
		trigger_error("Can't delete 'search_results'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$get["search_result"] = function(int $id)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'SEARCH_RESULTS' WHERE id=:id;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"id" => $id,
	];
	
	$ROW = Database::query($sql, $data);
	if(!is_array($ROW)) {
		trigger_error("Can't SELECT * FROM 'SEARCH_RESULTS'", E_USER_WARNING);
		return(false);
	}
	
	if(count($ROW) == 0) {
		return(NULL);
	}
	
	return($ROW[0]);
	
};//}}}//

data::$delete["search_results"] = function(array $ID)
{//{{{//
	
	foreach($ID as $id) {
		if(!eval(Check::$int.='$id')) return(false);
		
		$return = data::$delete["test_source"]($id);
		if(!$return) {
			trigger_error("Can't delete 'test_source'", E_USER_WARNING);
			return(false);
		}
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM 'SEARCH_RESULTS' WHERE id=:id;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$data = [
			"id" => $id,
		];
		
		$return = Database::query($sql, $data);
		if(!$return) {
			trigger_error("Can't DELETE FROM 'SEARCH_RESULTS'", E_USER_WARNING);
			return(false);
		}
		
	}// foreach($ID as $id)
	
	return(true);
	
};//}}}//

