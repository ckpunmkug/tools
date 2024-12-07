<?php

require_once('class/Initialization.php');
$Initialization = new Initialization;

require_once('class/HTML.php');
$HTML = new HTML();

HTML::$styles = [
	'share/style/main.css',
];

HTML::$scripts = [
];

require_once('function/main.php');

