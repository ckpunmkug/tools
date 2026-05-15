<?php

define('VENDOR', 'ckpunmkug');
define('PROJECT', 'php_tester');
define('DEBUG', true);

define('PATH', [
	"php_ini" => '/etc/php/8.2/digger/coverage',
	"cms" => HOME.'/www',
	"source" => HOME.'/www/php_tester/test.php',
	"commands" => HOME.'/www/php_tester/phpdbg.cmd',
]);
define('URL', [
	"source_viewer" => 'source_viewer/index.php',
	"text_editor" => 'text_editor/index.php',
]);
define('BIN', [
	"php" => '/usr/bin/php',
]);

