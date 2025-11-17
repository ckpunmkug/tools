<?php

class Statistics
{
	var $logs_dir = '';
	var $LOG_FILE = [];

	function __construct(string $logs_dir)
	{//{{{//
		
		$resource = opendir($logs_dir);
		if(!is_resource($resource)) {
			if (defined('DEBUG') && DEBUG) var_dump(['logs dir' => $logs_dir]);
			throw new Exception("Can't open 'logs dir'");
		}
		
		while(true) {
			$name = readdir($resource);
			if(!is_string($name)) break;
			
			$log_file = "{$logs_dir}/{$name}";
			if(!is_file($log_file)) continue;
			
			if(!is_readable($log_file)) {
				if (defined('DEBUG') && DEBUG) var_dump(['log file' => $log_file]);
				trigger_error("'log file' is not readable", E_USER_WARNING);
			}
			
			array_push($this->LOG_FILE, $log_file);
		}
		
		closedir($resource);
		
		return(true);
		
	}//}}}//

	function get_statistics()
	{//{{{//
		
		$text = '';
		foreach($this->LOG_FILE as $log_file) {
			
			$return = @$this->parse_file($log_file);
			if(!is_array($return)) continue;
			
			$statuses = '';
			foreach($return["status"] as $code => $count) {
				$statuses .= "{$code} - {$count}\n";
			}
			
			$ip_count = count($return["ip"]);
			$user_agent_count = count($return["user_agent"]);
			$URI_count = count($return["URI"]);
			$basename = basename($log_file);
			
			$text .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$return["date"][0]} - {$return["date"][1]}
File name = {$basename}
Lines in file = {$return["lines"]}
Unique IP addresses =  {$ip_count}
Unique URI = {$URI_count}
Unique user agents = {$user_agent_count}
Statuses
{$statuses}
\n
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		return($text);
		
	}//}}}//
	
	function parse_file(string $log_file)
	{//{{{//
	
		$result = [
			"date" => [0,0],
			"lines" => 0,
			"ip" => [],
			"URI" => [],
			"status" => [],
			"user_agent" => [],
		];
		
		$pattern = '/^.+\.gz$/';
		if(preg_match($pattern, $log_file) != 1) {
			$resource = fopen($log_file, 'r');
		}
		else {
			$resource = fopen("compress.zlib://{$log_file}", 'r');
		}
		if(!is_resource($resource)) {
			if (defined('DEBUG') && DEBUG) var_dump(['log file' => $log_file]);
			trigger_error("Can't open for read log file", E_USER_WARNING);
			return(false);
		}
		
		while(true) {
			$string = fgets($resource);
			if(feof($resource)) break;
			if(!is_string($string)) {
				trigger_error("Can't get string from log file", E_USER_WARNING);
				return(false);
			}
			
			$result["lines"] += 1;
			
			$array = $this->parse_line($string);
			if(!is_array($array)) continue;
			
			if($result["date"][0] == 0) {
				$result["date"][0] = $array["date_time"];
			}
			else {
				$result["date"][1] = $array["date_time"];
			}
			
			if(!key_exists($array['ip'], $result["ip"])) {
				$result["ip"]["{$array['ip']}"] = 0;
			}
			$result["ip"]["{$array['ip']}"] += 1;
			
			if(!key_exists($array['URI'], $result["URI"])) {
				$result["URI"]["{$array['URI']}"] = 0;
			}
			$result["URI"]["{$array['URI']}"] += 1;
			
			if(!key_exists($array['status'], $result["status"])) {
				$result["status"]["{$array['status']}"] = 0;
			}
			$result["status"]["{$array['status']}"] += 1;
			
			if(!key_exists($array['user_agent'], $result["user_agent"])) {
				$result["user_agent"]["{$array['user_agent']}"] = 0;
			}
			$result["user_agent"]["{$array['user_agent']}"] += 1;
			
		}
		
		return($result);
		
	}//}}}//

	function parse_line(string $string)
	{//{{{//
		
		$result = [];
		
// ip //////////////////////////////////////////////////////////////////////////
		
		$pattern = '/^(\S+)\s+(.+)$/';
		if(preg_match($pattern, $string, $MATCH) == 1) {
			$result["ip"] = $MATCH[1];
			$string = $MATCH[2];
		}
		else {
			if (defined('DEBUG') && DEBUG) var_dump(['log line' => $string]);
			trigger_error("Incorrect 'ip' in log line", E_USER_WARNING);
			return(false);
		}

// remote_user /////////////////////////////////////////////////////////////////
		
		$pattern = '/^(\S+)\s+(\S+)\s+(.+)$/';
		if(preg_match($pattern, $string, $MATCH) == 1) {
			$result["remote_user"] = [ $MATCH[1], $MATCH[2] ];
			$string = $MATCH[3];
		}
		else {
			if (defined('DEBUG') && DEBUG) var_dump(['log line' => $string]);
			trigger_error("Incorrect 'remote user' in log line", E_USER_WARNING);
			return(false);
		}

// date_time ///////////////////////////////////////////////////////////////////
		
		$pattern = '/^\[([^\]]+)\]\s+(.+)$/';
		if(preg_match($pattern, $string, $MATCH) == 1) {
			$result["date_time"] = $MATCH[1];
			$string = $MATCH[2];
		}
		else {
			if (defined('DEBUG') && DEBUG) var_dump(['log line' => $string]);
			trigger_error("Incorrect 'date time' in log line", E_USER_WARNING);
			return(false);
		}

// method URI version //////////////////////////////////////////////////////////
		
		$pattern = '/^"((?:[^"\\\\]|\\\\.)*?)"\s+(.+)$/';
		if(preg_match($pattern, $string, $MATCH) == 1) {
			$sub_string = $MATCH[1];
			$string = $MATCH[2];
		}
		else {
			if (defined('DEBUG') && DEBUG) var_dump(['log line' => $string]);
			trigger_error("Incorrect 'method URI version' in log line", E_USER_WARNING);
			return(false);
		}
		
		$pattern = '/^(\S+)\s+(\S+)\sHTTP\/([0-9\.]+)$/';
		if(preg_match($pattern, $sub_string, $MATCH) == 1) {
			$result["method"] = $MATCH[1];
			$result["URI"] = $MATCH[2];
			$result["version"] = $MATCH[3];
		}
		else {
			if (defined('DEBUG') && DEBUG) var_dump(['log line' => $string]);
			trigger_error("Incorrect 'method URI version' in log line", E_USER_WARNING);
			return(false);
		}
		
// status bytes ////////////////////////////////////////////////////////////////
		
		$pattern = '/^(\S+)\s+(\S+)\s(.+)$/';
		if(preg_match($pattern, $string, $MATCH) == 1) {
			$result["status"] = $MATCH[1];
			$result["bytes"] = $MATCH[2];
			$string = $MATCH[3];
		}
		else {
			if (defined('DEBUG') && DEBUG) var_dump(['log line' => $string]);
			trigger_error("Incorrect 'status bytes' in log line", E_USER_WARNING);
			return(false);
		}

// referer /////////////////////////////////////////////////////////////////////
		
		$pattern = '/^"((?:[^"\\\\]|\\\\.)*?)"\s+(.+)$/';
		if(preg_match($pattern, $string, $MATCH) == 1) {
			$result["referer"] = $MATCH[1];
			$string = $MATCH[2];
		}
		else {
			if (defined('DEBUG') && DEBUG) var_dump(['log line' => $string]);
			trigger_error("Incorrect 'referer' in log line", E_USER_WARNING);
			return(false);
		}

// user_agent //////////////////////////////////////////////////////////////////
		
		$pattern = '/^"((?:[^"\\\\]|\\\\.)*?)".*$/';
		if(preg_match($pattern, $string, $MATCH) == 1) {
			$result["user_agent"] = $MATCH[1];
		}
		else {
			if (defined('DEBUG') && DEBUG) var_dump(['log line' => $string]);
			trigger_error("Incorrect 'user_agent' in log line", E_USER_WARNING);
			return(false);
		}

		return($result);
		
	}//}}}//
}

