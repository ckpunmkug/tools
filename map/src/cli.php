<?php

/// include sources
if(true) {//{{{
	set_include_path(__DIR__.'/include');
	
	require_once('Check.php');
	require_once('Data.php');
	require_once('Settings.php');
	require_once('State.php');
	
	require_once('Prepare.php');
	require_once('Action.php');
        require_once('Process.php'); 
	require_once('Parser.php');
	
	require_once('main.php');
}//}}}

$return = main($argv);
if($return !== true) {
	trigger_error("Script executed with error", E_USER_ERROR);
	exit(255);
}
exit(0);
