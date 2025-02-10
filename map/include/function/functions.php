<?php

function warning(string $string)
{//{{{//
	
	trigger_error($string, E_USER_WARNING);
	return(false);
	
}//}}}//

function launch(string $command)
{//{{{//
	
	$output = [];
	$status = 0;
	$return = exec($command, $output, $status);
	
	if(!is_string($return))
		return warning("Exec command failed");
		
	if($status !== 0)
		return warning("Exec command status is not zero");
		
	$output = implode("\n", $output);
	return($output);
	
}//}}}//

