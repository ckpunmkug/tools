<?php

class phpdbg_wrapper
{
	function __construct(string $commands_file, string $source_file, string $working_dir)
	{//{{{//
		
		$commands = file_get_contents($commands_file);
		if(!is_string($commands)) {
			trigger_error("Can't get contents from file with 'phpdbg commands'", E_USER_WARNING);
			return(false);
		}
		$COMMAND = explode("\n", $commands);
		
		$a = [];
		foreach($COMMAND as $command) {
			$command = trim($command);
			if(strlen($command) == 0) continue;
			if(preg_match('/^#.*$/', $command) == 1) continue;
			array_push($a, $command);
		}
		$COMMAND = $a;
		
		$PHPDebugger = new PHPDebugger($source_file, $working_dir);
		$PHPDebugger->verbose = true;
		
		foreach($COMMAND as $command) {
			$output = $PHPDebugger->send($command);
		}
		
		return(true);
		
	}//}}}//
}
