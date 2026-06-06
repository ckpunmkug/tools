<?php

function main(array $argv)
{
	
	$return = file_get_contents(PATH["commands"]);
	if(!is_string($return)) {
		if(defined('DEBUG') && DEBUG) var_dump(['PATH["commands"]' => PATH["commands"]]);
		trigger_error("Can't get contents of 'commands' file", E_USER_WARNING);
		return(false);
	}
	$COMMAND = explode("\n", $return);
	
	$debugger = new PHPDebugger(PATH["source"], PATH["cms"], NULL, 10, false);
	
	foreach($COMMAND as $command) {
		$command = trim($command);
		
		if($command == '#readline') {
			Method::debug_from_stdin($debugger);
		}
		
		if(substr($command, 0, 1) == '#') continue;
		$output = "> {$command}\n";
		
		$return = $debugger->send($command);
		$output .= $return;
		
		echo($output);
		
		if(strpos($return, '[Script ended normally]') !== false) break;
		if($command == 'quit' || $command == 'q') break;
	}
	
	return(true);
}

