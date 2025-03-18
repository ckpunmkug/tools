<?= 'xa' ?>
<html>

</html><?php 

define('KOHCTAHTA', 'Tpy');
$Tbl = 'Xy~Z';

function qpyHkLLuR() {
	return(true);
}

class kJLac
{
	function A()
	{
		return(true);
	}
}

if($Tbl == 'Xy~Z') $geBka = 'nu3ga';


__halt_compiler();

	static $host = CONFIG['host'];

	static $php_bin = '/usr/bin/php';
	static $x80_script = __DIR__.'/../class/x80.php';

	static function get_SERVER(string $file)
	{//{{{//
		
		$script = substr($file, strlen(self::$www_dir));
		
		$result = array (
			'HTTP_HOST' => self::$host,
			'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0',
			'HTTP_ACCEPT' => '*/*',
			'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.5',
			'SERVER_NAME' => self::$host,
			'SERVER_ADDR' => '127.0.0.1',
			'SERVER_PORT' => '80',
			'REMOTE_ADDR' => '127.0.0.1',
			'DOCUMENT_ROOT' => self::$www_dir,
			'REQUEST_SCHEME' => 'http',
			'SERVER_ADMIN' => 'webmaster@localhost',
			'SCRIPT_FILENAME' => $file,
			'REMOTE_PORT' => '45148',
			'GATEWAY_INTERFACE' => 'CGI/1.1',
			'SERVER_PROTOCOL' => 'HTTP/1.1',
			'REQUEST_METHOD' => 'GET',
			'QUERY_STRING' => '',
			'REQUEST_URI' => $script,
			'SCRIPT_NAME' => $script,
			'PHP_SELF' => $script,
		);
		
		return($result);
		
	}//}}}//

