<?php

data::$create["DUCKDUCKGO_RESULT"] = function()
{//{{{//
	
	$table_name = 'DUCKDUCKGO_RESULT';
	$columns_describe = [
		"query" => 0,
		"url" => '',
		"title" => '',
		"description" => '',
	];
	$return = Database::create_table($table_name, $columns_describe);
	if(!$return) {
		trigger_error("Can't create '{$table_name}' table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

