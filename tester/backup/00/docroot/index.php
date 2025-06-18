<?php 

define('PROJECT_DIR', realpath(__DIR__.'/..'));
set_include_path(PROJECT_DIR.'/include');
define('SHARE_DIR', PROJECT_DIR.'/docroot/share');

require_once('class/Initialization.php');
$Initialization = new Initialization();

require_once('class/HTML.php');
$HTML = new HTML();

require_once('class/Main.php');
$Main = new Main();

