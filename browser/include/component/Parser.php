<?php

class Parser
{
	
	static function action()
	{//{{{//
		
		$action = @strval($_POST["action"]);
		
		switch($action) {
			case('next'):
				echo("viagra");
				return(true);
		}
		
		if (defined('DEBUG') && DEBUG) @var_dump(['$_POST["action"]' => $_POST["action"]]);
		trigger_error("Incorrect action", E_USER_WARNING);
		return(false);
		
	}//}}}//
	
	static function create_database(string $config_file, string $first_query)
	{//{{{//
		
	}//}}}//
	
}

