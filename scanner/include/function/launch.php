#!/usr/bin/php
<?php

function launch(string $command, int $timeout) // array
{//{{{//
	
	$timeout *= 1000000;
	
	$std = [
		["pipe", "r"],
		["pipe", "w"],
		["pipe", "w"],
	];
	$PIPE = [];
	$cwd = getcwd();
	if(!is_string($cwd)) {
		trigger_error("Can't get current working directory", E_USER_WARNING);
		return(false);
	}
	
	$proc = proc_open($command, $std, $PIPE, $cwd);
	if(!is_resource($proc)) {
		if (defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
		trigger_error("Can't open process for command", E_USER_WARNING);
		return(false);
	}
	
	while(true) {//
		$proc_status = proc_get_status($proc);
		if(!is_array($proc_status)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
			trigger_error("Can't get process status for command", E_USER_WARNING);
			return(false);
		}
		
		if($proc_status["running"] == false) break;
	
		usleep(100000);
		$timeout -= 100000;
		if($timeout <= 0) {
			proc_terminate($proc, 9);
			
			foreach($PIPE as $pipe) {
				fclose($pipe);
			}
			proc_close($proc);
			
			if (defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
			trigger_error("Process with command timeout", E_USER_WARNING);
			return(false);
		}
	}// while(true)
	
	$result = [
		"status" => $proc_status["exitcode"],
	];
	
	fclose($PIPE[0]);
	
	$contents = stream_get_contents($PIPE[1]);
	if(!is_string($contents)) {
		if (defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
		trigger_error("Can't get command stdout contents", E_USER_WARNING);
		return(false);
	}
	$result["stdout"] = $contents;
	fclose($PIPE[1]);
	
	$contents = stream_get_contents($PIPE[2]);
	if(!is_string($contents)) {
		if (defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
		trigger_error("Can't get command stderr contents", E_USER_WARNING);
		return(false);
	}
	$result["stderr"] = $contents;
	fclose($PIPE[2]);
	
	proc_close($proc);
	
	return($result);
	
}//}}}//

