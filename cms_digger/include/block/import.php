<?php

$path = DIR.'/docroot/'.URL["preg_search"];
$return = realpath($path);
if(!is_string($return)) {
	trigger_error("Can't get real path for 'preg_search' index.php", E_USER_ERROR);
	exit(255);
}

$return = dirname($return).'/../include/project/config.php';
$return = realpath($return);
if(!is_string($return)) {
	trigger_error("Can't get real path for 'preg_search' config.php", E_USER_ERROR);
	exit(255);
}
$config = $return;

$home = HOME;
$command = BIN["php"]." -r \"define('HOME', '{$home}'); require('{$config}'); var_export(PATH);\"";
$return = launch($command);
if(!is_array($return) || $return["status"] != 0) {
	trigger_error("Can't launch import shell command", E_USER_ERROR);
	exit(255);
}

$array = $return["stdout"];
$source = "return({$array});";
$return = eval($source);
if(!is_array($return)) {
	trigger_error("Can't evaluate return 'PATH' array", E_USER_ERROR);
	exit(255);
}
$PATH = $return;

if(!eval(Check::$string.='$PATH["database"]')) return(false);
$database = $PATH["database"];

define('IMPORT', [
	"database" => $database,
]);

