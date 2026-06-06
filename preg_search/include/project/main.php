<?php

function main()
{
	ArgV::$description = "Framework demo program";
	ArgV::add([
		"-t", "--test", NULL, "Run test file",
		function () {
			define("TEST", true);
		}, false
	]);
	ArgV::add([
		"-s", "--setup", NULL, "Setup project",
		function () {
			define("SETUP", true);
		}, false
	]);
	ArgV::add([
		"-p", "--purge", NULL, "Purge project",
		function () {
			define("PURGE", true);
		}, false
	]);
	ArgV::apply();
	
	if(defined('TEST')) {
		$return = require('project/test.php');
		if(!$return) {
			trigger_error("Test failed", E_USER_WARNING);
			return(false);
		}
		return(true);
	}
	
	if(defined('SETUP')) {
		$return = Setup::database();
		if(!$return) {
			trigger_error("Can't setup database", E_USER_WARNING);
			return(false);
		}
		
		echo("\nSetup complete\n");
		return(true);
	}
	
	if(defined('PURGE')) {
		$return = Method::purge();
		if(!$return) {
			trigger_error("Can't purge", E_USER_WARNING);
			return(false);
		}
		echo("\nPurge complete\n");
		return(true);
	}
	
	if(defined('VERBOSE') && VERBOSE) {
		$return = Method::is_setup();
	}
	else {
		$return = @Method::is_setup();
	}
	if($return) {
		echo("\nProject '".PROJECT."' ready for work\n");
	}
	else {
		echo("\nProject '".PROJECT."' is not setup\n");
	}
	
	return(true);
}

