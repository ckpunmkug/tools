<?php

define("\x80", '/srv/wordpress/www/wp-admin/includes/file.php');

if(chdir('/srv/wordpress/www') != true) {
	trigger_error("Can't change dir to docroot", E_USER_ERROR);
	exit(255);
}

function is_uploaded_file(string $path)
{//{{{//
	
	if(is_file($path)) {
		if(is_readable($path)) {
			return(true);
		}
	}
	
	return(false);
	
}//}}}//

function move_uploaded_file(string $source, string $destination)
{//{{{//
	
	if(is_file($source)) {
		if(is_readable($source)) {
			return(rename($source, $destination));
		}
	}
	
	return(false);
	
}//}}}//

if(true) // _SERVER
{//{{{//
$_SERVER = array (
	'HTTP_HOST' => 'wordpress.localhost',
	'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0',
	'HTTP_ACCEPT' => '*/*',
	'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.5',
	'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, br, zstd',
	'HTTP_SEC_GPC' => '1',
	'HTTP_CONNECTION' => 'keep-alive',
	'HTTP_SEC_FETCH_DEST' => 'empty',
	'HTTP_SEC_FETCH_MODE' => 'no-cors',
	'HTTP_SEC_FETCH_SITE' => 'cross-site',
	'HTTP_PRIORITY' => 'u=4',
	'HTTP_PRAGMA' => 'no-cache',
	'HTTP_CACHE_CONTROL' => 'no-cache',
	'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
	'SERVER_SIGNATURE' => "<address>Apache/2.4.62 (Debian) Server at wordpress.localhost Port 80</address>\n",
	'SERVER_SOFTWARE' => 'Apache/2.4.62 (Debian)',
	'SERVER_NAME' => 'wordpress.localhost',
	'SERVER_ADDR' => '127.0.0.1',
	'SERVER_PORT' => '80',
	'REMOTE_ADDR' => '127.0.0.1',
	'DOCUMENT_ROOT' => '/srv/wordpress/www',
	'REQUEST_SCHEME' => 'http',
	'CONTEXT_PREFIX' => '',
	'CONTEXT_DOCUMENT_ROOT' => '/srv/wordpress/www',
	'SERVER_ADMIN' => 'webmaster@localhost',
	'SCRIPT_FILENAME' => '/srv/wordpress/www/index.php',
	'REMOTE_PORT' => '45148',
	'GATEWAY_INTERFACE' => 'CGI/1.1',
	'SERVER_PROTOCOL' => 'HTTP/1.1',
	'REQUEST_METHOD' => 'GET',
	'QUERY_STRING' => '',
	'REQUEST_URI' => '/',
	'SCRIPT_NAME' => '/index.php',
	'PHP_SELF' => '/index.php',
	'REQUEST_TIME_FLOAT' => 1741455554.458104,
	'REQUEST_TIME' => 1741455554,
);	
}//}}}//

// 1 00103:01014  $move_new_file = @move_uploaded_file( $file['tmp_name'], $new_file );

if(true) // /srv/wordpress/www/wp-admin/includes/file.php:1014
{//{{{//

	require_once('/srv/wordpress/www/wp-load.php');
	require_once('/srv/wordpress/www/wp-admin/includes/file.php');
	
	// 0
	if(true) // function _wp_handle_upload( &$file, $overrides, $time, $action )
	{//{{{//
		$file = ['name' => 'shell.php'];
		$file['tmp_name'] = '/tmp/shell';
		
		if(!copy(__DIR__.'/shell.php', $file['tmp_name'])) {
			trigger_error("Can't copy `shell` to `tmp file`", E_USER_ERROR);
			exit(255);
		}
		
		$file['size'] = filesize($file['tmp_name']);
		
		$overrides = ['test_type' => false];
		$_POST['action'] = 'wp_handle_upload';
		_wp_handle_upload( $file, $overrides, NULL, 'wp_handle_upload');
	}//}}}//
	
}//}}}//

