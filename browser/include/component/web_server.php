<?php

class web_server
{

	static function main(array $config)
	{//{{{//
		
		if(!eval(C::$S.='$config["host"]')) return(false);
		self::$host = $config["host"];
		
		if(!eval(C::$S.='$config["port"]')) return(false);
		self::$port = $config["port"];
		
	}//}}}//

}

$WEB_SERVER_HOST = '127.0.0.1';
$WEB_SERVER_PORT = '8080';

$PHP_AUTH_USER = posix_getlogin();
$PHP_AUTH_PW = '';

$string = 'qwertyuiopasdfghjklzxcvbnm';
for ($index = 0; $index < 8; $index++) {
	$PHP_AUTH_PW .= substr($string, rand(0, 25), 1);
}

putenv("PHP_AUTH_USER={$PHP_AUTH_USER}");
putenv("PHP_AUTH_PW={$PHP_AUTH_PW}");

$string = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC

PHP built-in web server:
  username = {$PHP_AUTH_USER}
  password = {$PHP_AUTH_PW}
  url = http://{$WEB_SERVER_HOST}:{$WEB_SERVER_PORT} 

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
echo("{$string}\n");

$path = realpath(__DIR__.'/../docroot/index.php');
$command = "/usr/bin/php -S {$WEB_SERVER_HOST}:{$WEB_SERVER_PORT} {$path}";
system($command);


