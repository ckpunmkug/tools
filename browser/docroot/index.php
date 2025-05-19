<?php // network.proxy.no_proxies_on = 127.0.0.1:8080

// config
$config_dir = getenv('CONFIG_DIR', true);
if(!is_string($config_dir)) {
	trigger_error("Environment variable 'CONFIG_DIR' is not set", E_USER_ERROR);
	exit(255);
}
$config_file = realpath("{$config_dir}/config.php");
if(!is_string($config_file)) {
	trigger_error("Can't get real path for 'config_file'", E_USER_ERROR);
	exit(255);
}
require_once($config_file);

$include_path = realpath(__DIR__.'/../include');
if(!is_string($include_path)) {
	trigger_error("Can't get real path for 'include_path'", E_USER_ERROR);
	exit(255);
}
set_include_path($include_path);

require_once('class/Initialization.php');
$Initialization = new Initialization();

$cache_dir = getenv('CACHE_DIR', true);
if(!is_string($cache_dir)) {
	trigger_error("Environment variable 'CONFIG_DIR' is not set", E_USER_ERROR);
	exit(255);
}
$data_file = "{$cache_dir}/data.sqlite";

require_once('class/Data.php');
$return = Data::open($data_file);
if(!$return) {
	trigger_error("Can't open sqlite database file", E_USER_ERROR);
	exit(255);
}

require_once('class/Main.php');

if(!true) // TECTbl
{//{{{//
	
	$_SERVER["REQUEST_METHOD"] = 'POST';
	
	if(true) // duckduckgo
	{//{{{//
		
		$_POST['component'] = 'duckduckgo';
		
		if(!true) {
			$_POST['action'] = 'add_results';
			$_POST['data'] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{
	"queries": [
		"abcd"
		,"qwerty"
	]
	,"query": {
		"text": "qwerty"
		,"results": [
			{
				"url": "xxx"
				,"title": "yyy"
				,"description": "zzz"
			}
			,{
				"url": "XXX"
				,"title": "YYY"
				,"description": "ZZZ"
			}
		]
	}
}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		if(true) {
			$_POST['action'] = 'get_next_query';
		}
		
	}//}}}//
	
}//}}}//

$Main = new Main();
