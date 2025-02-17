<?php

/// Usage
/*{{{

$url = 'https://example.com';
$return = HTTP::GET($url);
//var_dump($return);

HTTP::$user_agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0';
HTTP::$proxy = 'tcp://127.0.0.1:8118';
HTTP::$follow_location = 0;
HTTP::$max_redirects = 1;
HTTP::$timeout = 30;

$url = 'https://check.torproject.org';
$return = HTTP::GET($url);
//var_dump($return);

}}}*/

class HTTP
{
	static $method = NULL;
	static $header = NULL;
	static $content = NULL;
	static $user_agent = NULL;
	static $proxy = NULL;
	static $follow_location = NULL;
	static $max_redirects = NULL;
	static $timeout = NULL; // MUST BE SET FLOAT VALUE LIKE 30.0 (30 seconds)
	
	static function check_url(string $url) // boolean
	{//{{{//
	
		$return = parse_url($url);
		if(!is_array($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
			trigger_error("Can't parse url", E_USER_WARNING);
			return(false);
		}
		$array = $return;
		
		if(@is_string($array["scheme"]) == false) {
			if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
			trigger_error("Can't find scheme in url", E_USER_WARNING);
			return(false);
		}
		$scheme = $array["scheme"];
		
		if(!($scheme == 'http' || $scheme == 'https')) {
			if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
			trigger_error("Incorrect scheme in url", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function request(string $url) // array
	{//{{{//
		
		if(!is_string(self::$method)) {
			trigger_error("HTTP::$method is not set", E_USER_WARNING);
			return(false);
		}
		
		$return = self::check_url($url);
		if(!$return) {
			if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
			trigger_error("Check url failed", E_USER_WARNING);
			return(false);
		}
		
		$context_options = [ "http" => [
			"method" => self::$method,
			"protocol_version" => 1.1,
			"ignore_errors" => true,
		]];
		
		if(is_array(self::$header)) {
			$context_options["http"]["header"] = self::$header;
		}
		if(is_string(self::$content)) {
			$context_options["http"]["content"] = self::$content;
		}
		if(is_string(self::$user_agent)) {
			$context_options["http"]["user_agent"] = self::$user_agent;
		}
		if(is_string(self::$proxy)) {
			$context_options["http"]["proxy"] = self::$proxy;
		}
		if(is_int(self::$follow_location)) {
			$context_options["http"]["follow_location"] = self::$follow_location;
		}
		if(is_int(self::$max_redirects)) {
			$context_options["http"]["max_redirects"] = self::$max_redirects;
		}
		if(is_float(self::$timeout)) {
			$context_options["http"]["timeout"] = self::$timeout;
		}
		
		$context = stream_context_create($context_options);
		if(!is_resource($context)) {
			trigger_error("Can't create http context", E_USER_WARNING);
			return(false);
		}
		
		if (defined('DEBUG') && DEBUG) {
			var_dump(stream_context_get_options($context));
		}
		
		$return = fopen($url, 'r', false, $context);
		if(!is_resource($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
			trigger_error("Can't open url stream with http context", E_USER_WARNING);
			return(false);
		}
		$stream = $return;
		$result = false;
		
		$return = stream_get_meta_data($stream);
		if(!is_array($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
			trigger_error("Can't get meta data from http stream for url", E_USER_WARNING);
			goto label_return;			
		}
		$meta_data = $return;
		
		$return = stream_get_contents($stream);
		if(!is_string($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
			trigger_error("Can't get contents from http stream for url", E_USER_WARNING);
			goto label_return;			
		}
		$contents = $return;
		
		$result = [
			"meta_data" => $meta_data,
			"contents" => $contents,
		];
		
		label_return:
		fclose($stream);
		return($result);
		
	}//}}}//
	
}

