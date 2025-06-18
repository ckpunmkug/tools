<?php

class socks5_checker
{
	static $timeout = 5;
	static $domain = "example.com";
	static $port = "\x01\xBB";

	static function main(array $config, string $action, string $data)
	{//{{{//
		
		if($action == 'single') {
			$return = self::single($data);
			if(!$return) {
				trigger_error("Action 'single' failed", E_USER_WARNING);
				return(false);
			}
			return(true);
		}
		
	}//}}}//
	
	static function single(string $parameter)
	{//{{{//
		
		$pattern = '/^(\d+\.\d+\.\d+\.\d+):(\d+)$/';
		$return = preg_match($pattern, $parameter, $MATCH);
		if($return == 1) {
			$host = $MATCH[1];
			$port = $MATCH[2];
			
			$return = self::single4($host, $port);
			if(!$return) {
				trigger_error("Method 'single4' failed", E_USER_WARNING);
				return(false);
			}
			return(true);
		}
		
		trigger_error("Incorrect parameter 'host:port'", E_USER_WARNING);
		return(false);	
		
	}//}}}//
	
	static function single4(string $host, string $port)
	{//{{{//
		
		$url = "tcp://{$host}:{$port}";
		if(defined('VERBOSE') && VERBOSE) {
				user_error("Open connection");
		}
		$socket = stream_socket_client($url, $number, $string, self::$timeout);
		if(!is_resource($socket)) {
			trigger_error("{$string}", E_USER_WARNING);
			return(false);
		}
		
		$out = false;
		
		$message = "\x05\x01\x00";
		$return = fwrite($socket, $message);
		if(!is_int($return)) {
			trigger_error("Can't send 'METHODS'", E_USER_WARNING);
			goto close;
		}
		
		$message = fread($socket, 2);
		if(!is_string($message)) {
			trigger_error("Can't receive 'METHOD'", E_USER_WARNING);
			goto close;
		}
		
		if(substr($message, 0, 1) != "\x05") {
			trigger_error("Server not support socks5 protocol", E_USER_WARNING);
			goto close;
		}
		
		if(substr($message, 1, 1) != "\x00") {
			trigger_error("Server not support method - 'NO AUTHENTICATION REQUIRED'", E_USER_WARNING);
			goto close;
		}
	
		$length = strlen(self::$domain);
		$length = chr($length);
		$message = "\x05\x01\x00\x03".$length.self::$domain.self::$port;
		$return = fwrite($socket, $message);
		if(!is_int($return)) {
			trigger_error("Can't send 'SOCKS' request", E_USER_WARNING);
			goto close;
		}
		
		$message = fread($socket, 4);
		if(!is_string($message)) {
			trigger_error("Can't receive 'SOCKS' reply", E_USER_WARNING);
			goto close;
		}
		
		if(substr($message, 0, 1) != "\x05") {
			trigger_error("Server not support socks5 protocol", E_USER_WARNING);
			goto close;
		}
		
		$rep = substr($message, 1, 1);
		switch($rep) {
			case("\x00"):
				if(defined('VERBOSE') && VERBOSE) {
						user_error("Connection to domain succeeded");
				}
				break;
			case("\x01"):
				trigger_error("General SOCKS server failure", E_USER_WARNING);
				goto close;
			case("\x02"):
				trigger_error("Connection not allowed by ruleset", E_USER_WARNING);
				goto close;
			case("\x03"):
				trigger_error("Network unreachable", E_USER_WARNING);
				goto close;
			case("\x04"):
				trigger_error("Host unreachable", E_USER_WARNING);
				goto close;
			case("\x05"):
				trigger_error("Connection refused", E_USER_WARNING);
				goto close;
			case("\x06"):
				trigger_error("TTL expired", E_USER_WARNING);
				goto close;
			case("\x07"):
				trigger_error("Command not supported", E_USER_WARNING);
				goto close;
			case("\x08"):
				trigger_error("Address type not supported", E_USER_WARNING);
				goto close;
			default:
				trigger_error("Unassigned", E_USER_WARNING);
				goto close;
		}
		
		
		$out = true;
		
		close:
		if(defined('VERBOSE') && VERBOSE) {
				user_error("Close connection");
		}
		fclose($socket);
		return($out);
		
	}//}}}//
}

