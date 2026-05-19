<?php

data::$create["SEARCH_QUERY"] = function()
{//{{{//
	
	$table_name = 'SEARCH_QUERY';
	$columns_describe = [
		"parent" => 0,
		"text" => '',
		"url" => '',
		"level" => 0,
		"complete" => 0,
	];
	$return = Database::create_table($table_name, $columns_describe);
	if(!$return) {
		trigger_error("Can't create '{$table_name}' table", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

