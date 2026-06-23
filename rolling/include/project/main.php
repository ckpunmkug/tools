<?php

require('function/load.php');
require('function/encode.php');
require('function/export.php');
require('function/decode.php');
require('function/import.php');
require('function/launch.php');

if(true) // ArgV
{//{{{//

	ArgV::$description = "Program description";
	ArgV::apply($argv);
	
	// Examples
	/* {{{
		ArgV::add([
			"-a", "--A", NULL, "not required parameter",
			function () {
				define("A", true);
			}, false
		]);
		ArgV::add([
			"-b", "--B", NULL, "required parameter",
			function () {
				define("B", true);
			}, true
		]);
		ArgV::add([
			"-c", "--C", "STRING", "not required parameter with value",
			function ($string) {
				define("C", $string);
			}, false
		]);
		ArgV::add([
			"-d", "--D", "STRING", "required parameter with value",
			function ($string) {
				define("D", $string);
			}, true
		]);
		ArgV::add([
			"-first", NULL, NULL, "middle name not required parameter",
			function () {
				define("FIRST", $string);
			}, false
		]);
		ArgV::add([
			NULL, "--second", "STRING", "middle name required parameter with value",
			function ($string) {
				define("SECOND", $string);
			}, true
		]);
		ArgV::add([
			"--without-description", NULL, NULL, NULL,
			function ($string) {
				define("SECOND", $string);
			}, true
		]);
	}}} */
	
}//}}}//

function main(array $argv)
{
	if(true) // initialization
	{//{{{//
	
		$return = getcwd();
		if(!is_string($return)) {
			trigger_error("", E_USER_WARNING);
			return(false);
		}
		define('CWD', $return);
		
	}//}}}//
	
	if(!true)
	{//{{{//
	
		$path = CWD.'/porno.json';
		$return = import($path);
		if(!is_array($return)) {
			trigger_error("", E_USER_WARNING);
			return(false);
		}
		$ARRAY = $return;
		
		$result = [];
		
		foreach($ARRAY as $array) {
			if($array["status"] != 0) continue;
			$stdout = trim($array["stdout"]);
			$array = explode("\n", $stdout);
			if(count($array) != 1) continue;
			array_push($result, $array[0]);
		}
		
		$path = CWD."/IP.json";
		$return = export($path, $result);
		if(!$return) {
			trigger_error("", E_USER_WARNING);
			return(false);
		}
		
	}//}}}//
	
	if(!true) // launch tlslookup
	{//{{{//
	
	$result = [];
	
	$name = 'cucbku';
	$cd = count($DOMAIN);
	foreach($DOMAIN as $domain) {
		cd($cd);
		
		$host = $name.$domain;
		$command = "/usr/bin/tlslookup {$host}";
		$return = launch($command);
		if(!is_array($return)) {
			trigger_error("", E_USER_WARNING);
			return(false);
		}
		
		if($return["status"] != 0) continue;
		
		$return["host"] = $host;
		array_push($result, $return);
	}
	
	$path = CWD."/{$name}.json";
	$return = export($path, $result);
	if(!$return) {
		trigger_error("", E_USER_WARNING);
		return(false);
	}
		
	}//}}}//
	
	if(true) // launch whois
	{//{{{//
		
		$path = CWD."/IP.json";
		$return = import($path);
		if(!is_array($return)) {
			trigger_error("", E_USER_WARNING);
			return(false);
		}
		$IP = $return;
		
		$cd = count($IP);
		foreach($IP as $ip) {
			cd($cd);
			
			$command = "/usr/bin/whois {$ip}";
			$return = launch($command);
			if(!is_array($return)) {
				trigger_error("", E_USER_WARNING);
				return(false);
			}
			$result = $return;
			
			$path = CWD."/whois/{$ip}.json";
			$return = export($path, $result);
			if(!$return) {
				trigger_error("", E_USER_WARNING);
				return(false);
			}
			
			$number = rand(10, 20);
			sleep($number);
		}
		
	}//}}}//
	
	return(true);	
	
}
