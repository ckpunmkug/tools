<?php

$return = realpath(__DIR__.'/..');
define('DIR', $return);

set_include_path(DIR.'/include');
require(DIR.'/docroot/config.php');
require('block/initialization.php');
require('data/class.php');

$component = 'index';
if(isset($_GET['component']) && is_string($_GET['component'])) {
	$component = $_GET['component'];
}

$COMPONENT = [
	'index', 'notes',
	'files_list', 'source_viewer', 'preg_search', 'search_results','test_source',
	'coverage', 'debugger',
];

if(!in_array($component, $COMPONENT)) {
	if(defined('DEBUG') && DEBUG) var_dump(['$component' => $component]);
	trigger_error("Unsupported component", E_USER_ERROR);
	exit(255);
}

Main::initialization();

HTML::$styles = [
	'share/style/default.css',
];

require("component/{$component}.php");
Main::switch_request_method($component);

__halt_compiler();

