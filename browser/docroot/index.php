<?php // network.proxy.no_proxies_on = 127.0.0.1:8080

define('VENDOR', 'ckpunmkug');
define('PROJECT', 'browser');

// load config
if(true) {//{{{

	$return = getenv('HOME', true);
	if(!is_string($return)) {
		trigger_error("Environment variable 'HOME' is not set", E_USER_ERROR);
		exit(255);
	}
	define('HOME', $return);
	
	$string = HOME.'/.config/'.VENDOR.'/'.PROJECT.'/config.php';
	require($string);
	
	if(!defined('CONFIG')) {
		trigger_error("Constant 'CONFIG' not defined in config file", E_USER_ERROR);
		exit(255);
	}
	
}//}}}

// basic includes
if(true) {//{{{

	set_include_path(__DIR__.'/../include');
	require_once('class/C.php');
	require_once('class/Args.php');
	
	require_once('class/Initialization.php');
	$Initialization = new Initialization();

	require_once('class/Data.php');
	$data_file = HOME."/.cache/".VENDOR."/".PROJECT."/data.sqlite";
	$return = Data::open($data_file);
	if(!$return) {
		trigger_error("Can't open sqlite database file", E_USER_ERROR);
		exit(255);
	}

	require_once('class/Main.php');

}//}}}

$Main = new Main();

