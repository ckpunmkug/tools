<?php declare(ticks = 1);
class Action
{
	static function geoiplookup()
	{//{{{
		if(defined('VERBOSE') && VERBOSE) {
			$string = "Look up country using IP Address";
			file_put_contents("php://stderr", "\n{$string}\n");
		}
	
		$input_file_name = 'geoiplookup.input';
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import geoiplookup input file", E_USER_WARNING);
			return(false);
		}
		
		$output_dir_name = 'geoiplookup.output';
		$output_dir_name = Data::use_directory($output_dir_name);
		if(!is_string($output_dir_name)) {
			trigger_error("Can't use geoiplookup output directory", E_USER_WARNING);
			return(false);
		}
			
		$State = new State("geoiplookup");
		foreach($data as $ip) {
			if($State->next()) continue;
			
			$command = "geoiplookup {$ip} > {$output_dir_name}/{$ip}";
			$return = 0;
			system($command, $return);
			if($return != 0 && defined('DEBUG') && DEBUG) {
				var_dump(['$command' => $command]);
				trigger_error("'geoiplookup' execution failed", E_USER_NOTICE);
			}
			
			$State->complete();
		}
		
		return(true);
	}//}}}

	static function nmapping()
	{//{{{
		if(defined('VERBOSE') && VERBOSE) {
			$string = "Nmap host discovery";
			file_put_contents("php://stderr", "\n{$string}\n");
		}
		
		$input_file_name = 'nmapping.input';
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import from nmapping input file", E_USER_WARNING);
			return(false);
		}
		
		$output_dir_name = 'nmapping.output';
		$output_dir_name = Data::use_directory($output_dir_name);
		if(!is_string($output_dir_name)) {
			trigger_error("Can't use nmapping output directory", E_USER_WARNING);
			return(false);
		}
		
		$State = new State('nmapping');
		foreach($data as $network_range) {
			if($State->next()) continue;
			
			$command = "nmap -sn {$network_range} > {$output_dir_name}/{$network_range}";
			$return = 0;
			system($command, $return);
			if($return != 0 && defined('DEBUG') && DEBUG) {
				var_dump(['$command' => $command]);
				trigger_error("'nmap' execution failed", E_USER_NOTICE);
			}
			
			$State->complete();
		}
		
		return(true);
	}//}}}

	static function nmaptcp()
	{//{{{
		if(defined('VERBOSE') && VERBOSE) {
			$string = "Nmap scan 80,443 ports";
			file_put_contents("php://stderr", "\n{$string}\n");
		}
		
		$input_dir_name = 'nmaptcp.input';
		$data = Data::get_files_from_dir($input_dir_name);
		if(!is_array($data)) {
			trigger_error("Can't get files from nmaptcp list directory", E_USER_WARNING);
			return(false);
		}
		
		$output_dir_name = 'nmaptcp.output';
		$output_dir_name = Data::use_directory($output_dir_name);
		if(!is_string($output_dir_name)) {
			trigger_error("Can't use nmaptcp output directory", E_USER_WARNING);
			return(false);
		}
		
		$State = new State('nmaptcp');
		foreach($data as $file) {
			if($State->next()) continue;
		
			$subnet = $file['name'];
			$input_file_name = $file['path'];
			
			$command = "nmap -n -p T:80,443 -iL ${input_file_name} > {$output_dir_name}/{$subnet}";
			$return = 0;
			system($command, $return);
			if($return != 0 && defined('DEBUG') && DEBUG) {
				var_dump(['$command' => $command]);
				trigger_error("'nmap' execution failed", E_USER_NOTICE);
			}
			
			$State->complete();
		}
		
		return(true);
	}//}}}
	
	static function sslscan()
	{//{{{
		if(defined('VERBOSE') && VERBOSE) {
			$string = "SSL/TLS Scan a HTTPS servers";
			file_put_contents("php://stderr", "\n{$string}\n");
		}
		
		$input_file_name = 'sslscan.input';
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import from sslscan input file", E_USER_WARNING);
			return(false);
		}
		
		$output_dir_name = 'sslscan.output';
		$output_dir_name = Data::use_directory($output_dir_name);
		if(!is_string($output_dir_name)){
			trigger_error("Can't use sslscan output directory", E_USER_WARNING);
			return(false);
		}
		
		$State = new State('sslscan');
		foreach($data as $ip) {
			if($State->next()) continue;
			
			$command = "sslscan --no-colour --no-heartbleed {$ip}:443 > {$output_dir_name}/{$ip}";
			$return = 0;
			system($command, $return);
			if($return != 0 && defined('DEBUG') && DEBUG) {
				var_dump(['$command' => $command]);
				trigger_error("'sslscan' execution failed", E_USER_NOTICE);
			}
			
			$State->complete();
		}
		
		return(true);
	}//}}}
	
	static function nslookup()
	{//{{{
		if(defined('VERBOSE') && VERBOSE) {
			$string = "Check domains found in certificates";
			file_put_contents("php://stderr", "\n{$string}\n");
		}
		
                $input_file_name = 'nslookup.input';
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import from nslookup input file", E_USER_WARNING);
			return(false);
		}
		
                $output_dir_name = 'nslookup.output';
		$output_dir_name = Data::use_directory($output_dir_name);
		if(!is_string($output_dir_name)) {
			trigger_error("Can't use nslookup output directory", E_USER_WARNING);
			return(false);
		}
		
		$State = new State('nslookup');
		foreach($data as $domain => $IP) {
			if($State->next()) continue;
			
			$command = "nslookup {$domain} 127.0.0.1 > {$output_dir_name}/{$domain}";
			$return = 0;
			system($command, $return);
			if($return != 0 && defined('DEBUG') && DEBUG) {
				var_dump(['$command' => $command]);
				trigger_error("'nslookup' execution failed", E_USER_NOTICE);
			}
			
			$State->complete();
		}
		
		return(true);
	}//}}}

	static function wget()
	{//{{{
		if(defined('VERBOSE') && VERBOSE) {
			$string = "Loading index pages from addresses and domains";
			file_put_contents("php://stderr", "\n{$string}\n");
		}
		
		$input_file_name = 'wget.input';
		$data = Data::import($input_file_name);
		if(!is_array($data)) {
			trigger_error("Can't import from wget input", E_USER_WARNING);
			return(false);
		}
		
		$output_dir_name = 'wget.output';
		$output_dir_name = Data::use_directory($output_dir_name);
		if(!is_string($output_dir_name)) {
			trigger_error("Can't use wget output directory", E_USER_WARNING);
			return(false);
		}
		
		$user_agent = '"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/115.0"';
		
		$State = new State("wget");
		foreach($data["domain"] as $domain) {
			if($State->next()) continue;
			
			$url = "https://{$domain}/";
			$output_file_name = "{$output_dir_name}/{$domain}";
			$command = "wget -q -O {$output_file_name} -t 8 -T 10 -U {$user_agent} -Q 10M {$url}";
			$return = 0;
			system($command, $return);
			if($return != 0 && defined('DEBUG') && DEBUG) {
				var_dump(['$command' => $command]);
				trigger_error("'wget' execution failed", E_USER_NOTICE);
			}
			
			$State->complete();
		}
		
		foreach($data["ip"] as $ip) {
			if($State->next()) continue;
			
			$url = "http://{$ip}/";
			$output_file_name = "{$output_dir_name}/{$ip}";
			$command = "wget -q -O {$output_file_name} -t 8 -T 10 -U {$user_agent} -Q 10M {$url}";
			$return = 0;
			system($command, $return);
			if($return != 0 && defined('DEBUG') && DEBUG) {
				var_dump(['$command' => $command]);
				trigger_error("'wget' execution failed", E_USER_NOTICE);
			}
			
			$State->complete();
		}
		return(true);
	}//}}}
	
}
