<?php $globals = array (
  '_GET' => 
  array (
  ),
  '_POST' => 
  array (
  ),
  '_COOKIE' => 
  array (
  ),
  '_FILES' => 
  array (
  ),
  '_ENV' => 
  array (
  ),
  '_REQUEST' => 
  array (
  ),
  '_SERVER' => 
  array (
    'HOME' => '/srv/wordpress',
    'HTTPS' => 'on',
    'HTTP_HOST' => '', // example.com
    'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0',
    'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.5',
    'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
    'SERVER_NAME' => '', // example.com
    'SERVER_ADDR' => '127.0.0.1',
    'SERVER_PORT' => '443',
    'REMOTE_ADDR' => '127.0.0.1',
    'DOCUMENT_ROOT' => '', // /var/www/html
    'REQUEST_SCHEME' => 'https',
    'SCRIPT_FILENAME' => '',  // /var/www/html/index.php
    'REMOTE_PORT' => '65535',
    'GATEWAY_INTERFACE' => 'CGI/1.1',
    'SERVER_PROTOCOL' => 'HTTP/1.1',
    'REQUEST_METHOD' => '', // GET, POST
    'QUERY_STRING' => '', // abcd=xyz&qwerty=123456
    'REQUEST_URI' => '', // /directory/index.php
    'SCRIPT_NAME' => '', // /directory/index.php
    'PHP_SELF' => '', // /directory/index.php
  ),
);
foreach($globals as $key => $value) {
	$GLOBALS[$key] = $value;
}
unset($globals, $key, $value);

