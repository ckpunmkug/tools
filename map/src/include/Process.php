<?php
class Process
{
	static function geoiplookup()
	{//{{{
		$State = new State("geoiplookup");
		if(is_int($State->item["out"])) return(true);
		
		$result = [];
		
		$input_dir_name = "geoiplookup.output";
		$data = Data::get_files_from_dir($input_dir_name);
		if(!is_array($data)) {
			trigger_error("Can't get files from geoiplookup output directory", E_USER_WARNING);
			return(false);
		}
		
		$ABBR = Settings::$country_names_abbr;
		foreach($data as $array) {
			$ip = $array["name"];
			$input_file_name = $array["path"];
			$contents = file_get_contents($input_file_name);
			if(!is_string($contents)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$input_file_name' => $input_file_name]);
				trigger_error("Can't get contents from geoiplookup output file", E_USER_WARNING);
				return(false);
			}
			
			$return = Parser::geoiplookup($ABBR, $contents);
			if($return) {
				array_push($result, $ip);
			}
		}
		
		$output_file_name = "geoiplookup.result";
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export to geoiplookup result file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["out"] = count($result);
		return(true);
	}//}}}
	
	static function nmapping()
	{//{{{
		$State = new State("nmapping");
		if(is_int($State->item["out"])) return(true);
		
		$result = [];
		
		$input_dir_name = 'nmapping.output';
		$data = Data::get_files_from_dir($input_dir_name);
		if(!is_array($data)) {
			trigger_error("Can't get files from nmapping output directory", E_USER_WARNING);
			return(false);
		}
		
		foreach($data as $file) {
			$network_range = $file['name'];
			$input_file_name = $file['path'];
			
			$contents = file_get_contents($input_file_name);
			if(!is_string($contents)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$input_file_name' => $input_file_name]);
				trigger_error("Can't get contents from nmapping output file", E_USER_WARNING);
				return(false);
			}
			
			$IP = Parser::nmapping($contents);
			$count = count($IP);
			if($count == 0) continue;
			
			$result[$network_range] = $IP;
		}
		
		$output_file_name = 'nmapping.result';
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export nmapping result to file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["out"] = count($result);
		return(true);
	}//}}}

	static function nmaptcp()
	{//{{{
		$State = new State('nmaptcp');
		if(is_int($State->item["out"])) return(true);
			
		$result = [];
		
		$input_dir_name = 'nmaptcp.output';
		$data = Data::get_files_from_dir($input_dir_name);
		if(!is_array($data)) {
			trigger_error("Can't get files from nmaptcp output directory", E_USER_WARNING);
			return(false);
		}
		
		foreach($data as $file) {
			$network_range = $file["name"];
			$input_file_name = $file['path'];
			
			$contents = file_get_contents($input_file_name);
			if(!is_string($contents)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$input_file_name' => $input_file_name]);
				trigger_error("Can't get contents from nmaptcp output file", E_USER_WARNING);
				return(false);
			}
			
			$IP = Parser::nmaptcp($contents);
			if(count($IP) == 0) continue;
			
			$result["{$network_range}"] = $IP;
		}
		
		$output_file_name = 'nmaptcp.result';
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export nmaptcp result to file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["out"] = count($result);
		return(true);
	}//}}}
	
	static function sslscan()
	{//{{{
		$State = new State('sslscan');
		if(is_int($State->item["out"])) return(true);
		
		$result = [];
		
		$input_dir_name = 'sslscan.output';
		$data = Data::get_files_from_dir($input_dir_name);
		if(!is_array($data)) {
			trigger_error("Can't get files from sslscan output directory", E_USER_WARNING);
			return(false);
		}
		
		foreach($data as $file) {
			$ip = $file['name'];
			$input_file_name = $file['path'];
			
			$contents = file_get_contents($input_file_name);
			if(!is_string($contents)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$input_file_name' => $input_file_name]);
				trigger_error("Can't get contents from sslscan output file", E_USER_WARNING);
				return(false);
			}
			
			$DOMAIN = Parser::sslscan($contents);
			if(empty($DOMAIN)) continue;
			
			$result["{$ip}"] = $DOMAIN;
		}
		
		$output_file_name = 'sslscan.result';
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export sslscan result to file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["out"] = count($result);
		return(true);
	}//}}}
	
	static function nslookup()
	{//{{{
		$State = new State('nslookup');
		if(is_int($State->item["out"])) return(true);
		
		$result = [];
		
		$input_file_name = 'nslookup.input';
		$DOMAIN = Data::import($input_file_name);
		if(!is_array($DOMAIN)) {
			trigger_error("Can't import from nslookup list file", E_USER_WARNING);
			return(false);
		}
		
		$input_dir_name = 'nslookup.output';
		$FILE = Data::get_files_from_dir($input_dir_name);
		if(!is_array($FILE)) {
			trigger_error("Can't get files from nslookup output directory", E_USER_WARNING);
			return(false);
		}
		
		foreach($FILE as $file) {
			$domain = $file["name"];
			$file_name = $file["path"];
			
			$contents = file_get_contents($file_name);
			if(!is_string($contents)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$file_name' => $file_name]);
				trigger_error("Can't get contents from nslookup output file", E_USER_WARNING);
				return(false);
			}
			
			$IP = $DOMAIN["{$domain}"];
			$IP = Parser::nslookup($contents, $IP);
			if(empty($IP)) continue;
			
			$result["{$domain}"] = $IP;
		}
		
		$output_file_name = 'nslookup.result';
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export nslookup result to file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["out"] = count($result);
		return(true);
	}//}}}

	static function wget()
	{//{{{
		$State = new State('wget');
		if(is_int($State->item["out"])) return(true);
		
		$result = [];
		
		$input_dir_name = 'wget.output';
		$FILE = Data::get_files_from_dir($input_dir_name);
		if(!is_array($FILE)) {
			trigger_error("Can't get files from wget output directory", E_USER_WARNING);
			return(false);
		}
		
		foreach($FILE as $file) {
			$address = $file["name"];
			$file_name = $file["path"];
			
			$number = filesize($file_name);
			if($number == 0) continue;
			
			array_push($result, $address);
		}
		
		$output_file_name = 'wget.result';
		$return = Data::export($output_file_name, $result);
		if(!$return) {
			trigger_error("Can't export to wget result file", E_USER_WARNING);
			return(false);
		}
		
		$State->item["out"] = count($result);
		return(true);
	}//}}}
	
}
