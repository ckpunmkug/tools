<?php

require(__DIR__.'/../include/class/Data.php');

$filename = '/srv/tor-browser/test/chars';
$string = file_get_contents($filename);
$string = Data::escape($string.'abc');
echo(bin2hex($string)."\n");
