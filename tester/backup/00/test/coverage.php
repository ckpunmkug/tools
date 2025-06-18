<?php //#!/usr/bin/env phpdbg -qrr

declare(strict_types=1);
$return = function_exists('phpdbg_start_oplog');
var_dump($return);
phpdbg_start_oplog();
if(true) {
	echo("XA!");
}
$return = phpdbg_end_oplog();
var_dump($return);
