<?php

$return = getenv('HOME', true);
if(!is_string($return)) {
	trigger_error("Environment variable 'HOME' is not set", E_USER_ERROR);
	exit(255);
}
define('HOME', $return);

define('PATH', [
	"cms" => HOME."/www",
	"data" => HOME."/tester/digger",
	
	"database" => HOME."/tester/digger/database.sqlite",
	
	"start" => HOME."/tester/digger/start.php",
	"commands" => HOME."/tester/digger/phpdbg.cmd",
	"notes" => HOME."/tester/digger/notes.txt",
	
	"GLOBALS" => DIR.'/include/block/GLOBALS.php',
	"u80" => DIR.'/include/class/u80.php',
	
	"php_ini" => [
		"coverage" => '/etc/php/8.2/coverage/php.ini',
	],
]);

define('DEBUGGER_PREFIX',
	"<?php \n"
	."require('".PATH["GLOBALS"]."');\n"
	."?>"
);
define('COVERAGE_PREFIX',
	"<?php \n"
	."require('".PATH["GLOBALS"]."');\n"
	."require('".PATH["u80"]."');\n"
	."?>"
);

