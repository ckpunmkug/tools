<?php
class Parser
{
	static function get_bytes_from_IPv4(string $IPv4)
	{//{{{
		$BYTE = [];
		
		$pattern = '/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/';
		if(preg_match($pattern, $IPv4, $MATCH) != 1) {
			if (defined('DEBUG') && DEBUG) var_dump(['$IPv4' => $IPv4]);
			trigger_error("Passed string is in the wrong format", E_USER_WARNING);
			return(false);
		}
		
		for($index = 4; $index > 0; $index -= 1) {
			$byte = intval($MATCH[$index]);
			if(!($byte >= 0 && $byte <= 255)) {
				if (defined('DEBUG') && DEBUG) var_dump([
					'$index' => $index,
					'$byte' => $byte,
				]);
				trigger_error("Byte in IPv4 address out of range", E_USER_WARNING);
				return(false);
			}
			array_push($BYTE, $byte);
		}
		
		//var_dump($BYTE);
		return($BYTE);
	}//}}}

	static function geoiplookup(array $WHAT, string $text)
	{//{{{
		foreach($WHAT as $what) {
			$number = preg_match_all("/{$what}/", $text);
			if(is_int($number) && $number > 0) return(true);
		}
		return(false);
	}//}}}

	static function nmapping(string $text)
	{//{{{
		$result = [];
	
		$LINE = explode("\n", $text);
		foreach($LINE as $line) {
			$line = trim($line);
			if(preg_match('/^Nmap scan report for.+$/', $line) != 1) continue;
			
			if(preg_match('/^.+\(([\d\.]+)\)$/', $line, $MATCH) == 1) {
				array_push($result, $MATCH[1]);
				continue;
			}
			
			if(preg_match('/^.+\s+([\d\.]+)$/', $line, $MATCH) == 1) {
				array_push($result, $MATCH[1]);
				continue;
			}
		}
		
		return($result);
	}//}}}
	
	static function nmaptcp(string $text)
	{//{{{
		$IP = [];
		$ip = '';
		$port = [];
		$port["80"] = false;
		$port["443"] = false;
		
		$LINE = explode("\n", $text);
		foreach($LINE as $line) {
			$line = trim($line);
			
			$pattern = '/^Nmap scan report for ([\d+\.]+)$/';
			if(preg_match($pattern, $line, $MATCH) == 1) {
				if(!empty($ip)) {
					$IP["{$ip}"] = $port;
					$port["80"] = false;
					$port["443"] = false;
				}
			
				$ip = $MATCH[1];
				continue;
			}
			
			$pattern = '/^80\/tcp\s+open\s+http$/';
			if(preg_match($pattern, $line) == 1) {
				$port["80"] = true;
				continue;
			}
			$pattern = '/^443\/tcp\s+open\s+https$/';
			if(preg_match($pattern, $line) == 1) {
				$port["443"] = true;
				continue;
			}
		}
		
		$result = [];
		foreach($IP as $ip => $PORT) {
			if($PORT["80"]) {
				$result["{$ip}"] = $PORT;
				continue;
			}
			if($PORT["443"]) {
				$result["{$ip}"] = $PORT;
				continue;
			}
		}
		
		return($result);
	}//}}}

	static function sslscan(string $output_text)
	{//{{{
		$result = [];
		$LINE = explode("\n", $output_text);
		foreach($LINE as $line) {
			$line = trim($line);
			
			if(preg_match('/^Subject:\s+(.+)$/', $line, $MATCH) == 1) {
				$string = $MATCH[1];
				$string = trim($string);
				if(preg_match('/\./', $string) != 1) continue;
				if(preg_match('/^((\*\.)|())([a-z0-9\-\.]+)$/', $string, $MATCH) == 1) {
					$domain = $MATCH[4];
					if(!in_array($domain, $result)) {
						array_push($result, $domain);
					}
				}
			}
			
			if(preg_match('/^Altnames:\s+(.+)$/', $line, $MATCH) == 1) {
				$string = $MATCH[1];
				$string = trim($string);
				$STRING = explode(",", $string);
				foreach($STRING as $string) {
					$string = trim($string);
					if(preg_match('/\./', $string) != 1) continue;
					if(preg_match('/^DNS:((\*\.)|())([a-z0-9\-\.]+)$/', $string, $MATCH) == 1) {
						$domain = $MATCH[4];
						if(!in_array($domain, $result)) {
							array_push($result, $domain);
						}
					}
				}
			}
		}
		return($result);
	}//}}}

	static function nslookup(string $output_text, array $IP)
	{//{{{
		$result = [];
		foreach($IP as $ip) {
			$number = preg_match_all("/{$ip}/", $output_text);
			if($number > 0) {
				array_push($result, $ip);
			}
		}
		return($result);
	}//}}}
	
}
