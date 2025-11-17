<?php 

define('DIR', __DIR__);
set_include_path(DIR.'/include');

require_once('component/guest/security.php');
require_once(DIR.'/config.php');

if(PHP_SAPI == 'cli') {
	require_once('class/Args.php');
}
else {

	require_once('function/create_seo_friendly_urls.php');
	require_once('class/HTML.php');
	HTML::$favicon = '/favicon.ico';
	$HTML = new HTML;
}

require_once('class/DB.php');
require_once('block/init.php');

require_once('class/Check.php');

require_once('component/Data.php');
require_once('component/guest/Action.php');
require_once('component/guest/Page.php');
require_once('component/guest/Main.php');

$Main = new Main();

