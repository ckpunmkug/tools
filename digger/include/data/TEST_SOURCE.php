<?php

data::$create["TEST_SOURCE"] = function()
{//{{{//

	$table_name = "TEST_SOURCE";
	$columns_describe = [
			"result" => 0,
			"status" => '',
			"text" => '',
			"file" => '',
	];
	$return = Database::create_table($table_name, $columns_describe);
	if(!$return) {
		trigger_error("Can't create table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$drop["TEST_SOURCE"] = function()
{//{{{//

	$table_name = "TEST_SOURCE";
	$return = Database::drop_table($table_name);
	if(!$return) {
		trigger_error("Can't drop table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$get["test_source"] = function(int $result)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM 'TEST_SOURCE' WHERE result=:result;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"result" => $result,
	];
	
	$ROW = Database::query($sql, $data);
	if(!is_array($ROW)) {
		trigger_error("Can't SELECT * FROM 'TEST_SOURCE'", E_USER_WARNING);
		return(false);
	}
	
	if(count($ROW) == 0) {
		return(NULL);
	}
	
	return($ROW[0]);
	
};//}}}//

data::$add["test_source"] = function(int $result, string $status, string $text, string $file)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO 'TEST_SOURCE' (result, status, text, file) VALUES (:result, :status, :text, :file);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"result" => $result,
		"status" => $status,
		"text" => $text,
		"file" => $file,
	];
	
	$id = Database::query($sql, $data);
	if(!is_int($id)) {
		trigger_error("Can't INSERT INTO 'TEST_SOURCE'", E_USER_WARNING);
		return(false);
	}
	
	return($id);
	
};//}}}//

data::$update["test_source"] = function(int $result, string $status, string $text, string $file)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE 'TEST_SOURCE' SET
	status=:status,
	text=:text,
	file=:file
 WHERE result=:result;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"status" => $status,
		"text" => $text,
		"file" => $file,
		"result" => $result,
	];
	
	$return = Database::query($sql, $data);
	if(!$return) {
		trigger_error("Can't UPDATE 'TEST_SOURCE'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

data::$delete["test_source"] = function(int $result)
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM 'TEST_SOURCE' WHERE result=:result;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$data = [
		"result" => $result,
	];
	
	$return = Database::query($sql, $data);
	if(!$return) {
		trigger_error("DELETE FROM 'TEST_SOURCE'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

