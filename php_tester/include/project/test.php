<?php

if(!defined('TEST')) die;

if(true) // launch
{//{{{//
	
	$command = '/usr/bin/env';
	
	system($command);
	//$return = launch($command); var_dump($return);
	
}//}}}//

if(!true) // setup_config
{//{{{//
	
	$return = Method::setup_config();
	if(!$return) {
		trigger_error("Can't setup 'config'", E_USER_WARNING);
		return(false);
	}
	
}//}}}//

if(!true) // setup_database
{//{{{//
	
	$return = Method::setup_database();
	if(!$return) {
		trigger_error("Can't setup database", E_USER_WARNING);
		return(false);
	}
	
}//}}}//

if(!true) // setup
{//{{{//
	
	$return = Method::setup();
	if(!$return) {
		trigger_error("Can't setup", E_USER_WARNING);
		return(false);
	}
	
}//}}}//

if(!true) // purge
{//{{{//
	
	$return = Method::purge();
	if(!$return) {
		trigger_error("Can't purge", E_USER_WARNING);
		return(false);
	}
	
}//}}}//

