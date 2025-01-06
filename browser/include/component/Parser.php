<?php

class Parser
{
	
	static function action()
	{//{{{//
		
		$action = @strval($_POST["action"]);
		switch($action) {
			case("next"):
				echo("xa-xa-xa");
				reutrn(true);
			default:
				trigger_error("Unsupported action on `parser`", E_USER_WARNING);
				return(false);
		}
		
	}//}}}//
	
}

