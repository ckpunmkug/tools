<?php

data::$create["TEST_STATUS"] = function()
{//{{{//

	$table_name = "TEST_STATUS";
	$columns_describe = [
			"query" => 0,
			"result" => 0,
			"status" => '',
	];
	$return = Database::create_table($table_name, $columns_describe);
	if(!$return) {
		trigger_error("Can't create table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$drop["TEST_STATUS"] = function()
{//{{{//

	$table_name = "TEST_STATUS";
	$return = Database::drop_table($table_name);
	if(!$return) {
		trigger_error("Can't drop table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$get["TEST_STATUS"] = function(int $query)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'TEST_STATUS' WHERE query=:query;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"query" => $query,
	];

	$return = Database::query($sql, $data);
	if(!is_array($return)) {
		trigger_error("Can't SELECT * FROM 'TEST_STATUS'", E_USER_WARNING);
		return(false);
	}
	$ARRAY = $return;
	
	$result = [];
	foreach($ARRAY as $array) {
		$result[$array["result"]] = $array["status"];
	}

	return($result);
	
};//}}}//

data::$get["test_status"] = function(int $query, int $result)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'TEST_STATUS' WHERE query=:query AND result=:result;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"query" => $query,
		"result" => $result,
	];
	
	$return = Database::query($sql, $data);
	if(!is_array($return)) {
		trigger_error("Can't SELECT * FROM 'TEST_STATUS'", E_USER_WARNING);
		return(false);
	}
	$ROW = $return;
	
	if(count($ROW) == 0) return(NULL);
	
	return($ROW[0]);
	
};//}}}//

data::$add["test_status"] = function(int $query, int $result, string $status)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO 'TEST_STATUS' (query, result, status) VALUES (:query, :result, :status);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"query" => $query,
		"result" => $result,
		"status" => $status,
	];
	
	$return = Database::query($sql, $data);
	if(!is_int($return)) {
		trigger_error("Can't INSERT INTO 'TEST_STATUS'", E_USER_WARNING);
		return(false);
	}
	$id = $return;
	
	return($id);
	
};//}}}//

data::$update["test_status"] = function(int $query, int $result, string $status)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE 'TEST_STATUS' SET
	status=:status
 WHERE query=:query AND result=:result;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"status" => $status,
		"query" => $query,
		"result" => $result,
	];
	
	$return = Database::query($sql, $data);
	if(!$return) {
		trigger_error("Can't UPDATE ON 'TEST_STATUS'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$delete["test_status"] = function(int $id)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE * FROM 'TEST_STATUS' WHERE id=:id;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"id" => $id,
	];
	
	$return = Database::query($sql, $data);
	if(!$return) {
		trigger_error("Can't DELETE * FROM 'TEST_STATUS'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

