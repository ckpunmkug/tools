<?php 

define('DIR', realpath(__DIR__));
set_include_path(DIR."/include");

require_once('component/controller/security.php');
require_once(DIR."/config.php");

if(PHP_SAPI == 'cli') {
	require_once('class/Args.php');
}
else {
	require_once('class/HTML.php');
	HTML::$favicon = 'favicon.ico';
	$HTML = new HTML;
	HTML::$styles = [
		'style.css',
	];
}

require_once('class/DB.php');
require_once('class/HTTP.php');
require_once('class/Job.php');
require_once('block/init.php');

require_once('class/Check.php');
require_once('function/getElementsByTagName.php');

require_once('component/cpservm.php');
require_once('component/anthropic.php');

require_once('component/prediction.php');
prediction::init();

require_once('component/controller/Site.php');
require_once('component/controller/Data.php');
require_once('component/controller/Action.php');
require_once('component/controller/Page.php');
require_once('component/controller/Main.php');

$Main = new Main();

