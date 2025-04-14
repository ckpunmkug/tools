<?php

require_once('class/Args.php');
require_once('class/Data.php');
require_once('class/Parser.php');
require_once('function/launch.php');
require_once('function/counter.php');
require_once('component/Scanner.php');

Args::$description = "HTTP(S) Scanner (ckaHep)";
Args::add([
	"-D", "--database", "<path_to_file>", "Path to database file",
	function ($string) {
		define("DATABASE_FILE_PATH", $string);
	}, false
]);
Args::add([
	"-I", "--input-file", "<path_to_file>", "Path to input file",
	function ($string) {
		define("INPUT_FILE_PATH", $string);
	}, false
]);
Args::add([
	"-S", "--disposable-file", "<path_to_file>", "Path to disposable php source file",
	function ($string) {
		define("DISPOSABLE_SOURCE_FILE", $string);
	}, false
]);
Args::apply();

