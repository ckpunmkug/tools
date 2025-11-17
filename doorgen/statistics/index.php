<?php 

define('DIR', realpath(__DIR__));
set_include_path(DIR."/include");

require_once('component/statistics/security.php');
require_once(DIR."/config.php");

if(PHP_SAPI == 'cli') {
	require_once('class/Args.php');
}
else {
	require_once('class/HTML.php');
	HTML::$favicon = '/favicon.ico';
	$HTML = new HTML;
	HTML::$styles = [
	];
}

require_once('class/Check.php');

require_once('component/statistics/Statistics.php');
require_once('component/statistics/Main.php');

//$Main = new Main();

$logs_dir = '/srv/streamrank_site/nginx';
try {
	$Statistics = new Statistics($logs_dir);
}
catch(Exception $Exception) {
	trigger_error($Exception->getMessage(), E_USER_ERROR);
	exit(255);
}

$statistics = $Statistics->get_statistics();

HTML::$body .= "<pre>{$statistics}</pre>";
