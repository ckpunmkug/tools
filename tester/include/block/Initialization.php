<?php

define('LESS_BIN', '/usr/bin/less');
define('SELF_COMMAND', $argv[0]);

require_once('class/Args.php');
require_once('component/Tester.php');

if(true) // Initialization
{//{{{//

	Args::$description = "PHP Source Tester (gblpokoJL)";
	Args::add([
		"-a", "--action", '<action_name>', "Set script action",
		function ($string) {
			define("ACTION", $string);
		}, false
	]);
	Args::add([
		"-c", "--command", '<command_with_line_number>', "Used in less",
		function ($string) {
			define("COMMAND", $string);
		}, false
	]);
	Args::apply();
	
}//}}}//

