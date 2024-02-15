<?php
class Prepare
{
	static function scan(string $data_dir_name, string $IPv4, string $country_names_abbr)
	{//{{{
		$BYTE = Parser::get_bytes_from_IPv4($IPv4);
		if(!is_array($BYTE)) {
			trigger_error("Can't parse an IPv4 address into bytes", E_USER_WARNING);
			return(false);
		}
		$network = "{$BYTE[3]}.{$BYTE[2]}";

		Settings::$network = $network;

		$data_dir_name = "{$data_dir_name}/{$network}";
		$return = file_exists($data_dir_name);
		if(!$return) {
			$return = mkdir($data_dir_name, 0755, true);
			if(!$return) {
				if (defined('DEBUG') && DEBUG) var_dump(['$data_dir_name' => $data_dir_name]);
				trigger_error("Can't create directory for data", E_USER_WARNING);
				return(false);
			}
		}
		
		$data_dir_name = Data::set_dir_name($data_dir_name);
		if(!is_string($data_dir_name)) {
			trigger_error("Can't set directory for data", E_USER_WARNING);
			return(false);
		}
			
		Settings::$data_dir_name = $data_dir_name;
		
		if(defined('VERBOSE') && VERBOSE) {
			$string = "Path to the data directory {$data_dir_name}";
			file_put_contents("php://stderr", "{$string}\n");
		}
		
		$country_names_abbr = trim($country_names_abbr);
		$country_names_abbr = explode(",", $country_names_abbr);
		
		Settings::$country_names_abbr = $country_names_abbr;
		
		return(true);
	}//}}}

	static function geoiplookup()
	{//{{{
		$State = new State("geoiplookup");
		if(is_int($State->item["in"])) return(true);
		
		$result = [];
		
		$network = Settings::$network;
		for($number = 0; $number < 256; $number += 1) {
			$ip = "{$network}.{$number}.1";
			array_push($result, $ip);
		}
		
		$output_file_name = "geoiplookup.input";
		$return = Data::export($output_file_name, $result);
		if(!$result) {
			trigger_error("Can't export to geoiplookup input file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["in"] = count($result);
		return(true);
	}//}}}

	static function nmapping()
	{//{{{
		$State = new State("nmapping");
		if(is_int($State->item["in"])) return(true);
		
		$result = [];
		
		$input_file_name = 'geoiplookup.result';
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import from geoiplookup result file", E_USER_WARNING);
			return(false);
		}
		
		foreach($data as $ip) {
			$BYTE = Parser::get_bytes_from_IPv4($ip);
			if(!is_array($BYTE)) {
				trigger_error("Can't get bytes from ip address", E_USER_WARNING);
				return(false);
			}
			
			$network_range = "{$BYTE[3]}.{$BYTE[2]}.{$BYTE[1]}.0-255";
			array_push($result, $network_range);
		}
		
		$output_file_name = 'nmapping.input';
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export to nmapping input file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["in"] = count($result);
		return(true);
	}//}}}

	static function nmaptcp()
	{//{{{
		$State = new State('nmaptcp');
		if(is_int($State->item["in"])) return(true);
		
		$result = [];
		
		$input_file_name = "nmapping.result";
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import data from nmapping result file", E_USER_WARNING);
			return(false);
		}
			
		$output_dir_name = "nmaptcp.input";
		$output_dir_name = Data::use_directory($output_dir_name);
		if(!is_string($output_dir_name)) {
			trigger_error("Can't use nmaptcp input directory", E_USER_WARNING);
			return(false);
		}
		
		foreach($data as $network_range => $IP) {
			array_push($result, $network_range);
			
			$contents = implode("\n", $IP);
			$output_file_name = "{$output_dir_name}/{$network_range}";
			$return = file_put_contents($output_file_name, $contents);
			if(!is_int($return)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$output_file_name' => $output_file_name]);
				trigger_error("Can't save list to nmaptcp input file", E_USER_WARNING);
				return(false);
			}
		}
		
		$State->item["in"] = count($result);
		return(true);
	}//}}}

	static function sslscan()
	{//{{{
		$State = new State("sslscan");
		if(is_int($State->item["in"])) return(true);
		
		$result = [];
		
		$input_file_name = "nmaptcp.result";
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import from nmaptcp result file", E_USER_WARNING);
			return(false);
		}
		
		foreach($data as $IP) {
			foreach($IP as $ip => $PORT) {
				if($PORT["443"]) {
					array_push($result, $ip);
				}
			}
		}
		
		$output_file_name = "sslscan.input";
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export to sslscan input file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["in"] = count($result);
		return(true);
	}//}}}

	static function nslookup()
	{//{{{
		$State = new State("nslookup");
		if(is_int($State->item["in"])) return(true);
			
		$result = [];
		
		$input_file_name = "sslscan.result";
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import from sslscan result file", E_USER_WARNING);
			return(false);
		}
		
		foreach($data as $ip => $DOMAIN) {
			foreach($DOMAIN as $domain) {
				if(!key_exists($domain, $result)) {
					$result["{$domain}"] = [];
				}
				if(!in_array($ip, $result["{$domain}"])) {
					array_push($result["{$domain}"], $ip);
				}
			}
		}
	
		$output_file_name = "nslookup.input";
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export to nslookup list file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["in"] = count($result);
		return(true);
	}//}}}

	static function wget()
	{//{{{
		$State = new State("wget");
		if(is_int($State->item["in"])) return(true);
		
		$result = [];
		
		$input_file_name = "nslookup.result";
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import nslookup result", E_USER_WARNING);
			return(false);
		}
		
		$tmp = [];
		foreach($data as $domain => $IP) {
			if(in_array($domain, $tmp)) continue;
			array_push($tmp, $domain);
		}
		$result["domain"] = $tmp;
		
		$input_file_name = "nmaptcp.result";
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import from nmaptcp result file", E_USER_WARNING);
			return(false);
		}
		
		$tmp = [];
		foreach($data as $network_range => $IP) {
			foreach($IP as $ip => $PORT) {
				if($PORT["80"]) {
					array_push($tmp, $ip);
				}
			}
		}
		$result["ip"] = $tmp;
		
		$output_file_name = "wget.input";
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export wget input", E_USER_WARNING);
			return(false);
		}
		
		$COUNT = [];
		$COUNT[0] = count($result["ip"]);
		$COUNT[1] = count($result["domain"]);
		
		$State->item["in"] = $COUNT[0] + $COUNT[1];
		return(true);
	}//}}}

}
