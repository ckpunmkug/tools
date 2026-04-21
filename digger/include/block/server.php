<?php
	
if(defined('QUIET') && QUIET === true) {
	ini_set('error_reporting', 0);
	ini_set('display_errors', '0');
}
else {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', '1');
	ini_set('html_errors', '0');
}

require('class/HTML.php');
require('function/t2h.php');
require('function/layout_form.php');
require('class/Main.php');

