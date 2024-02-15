<?php

define('VERBOSE', true);
define('DEBUG', false);

function help()
{//{{{
	$message = 
<<<HEREDOC
Example:
	php cli.php -ip 192.168.0.1 -dir /tmp -geo US,CA

 -ip <address>
 	The starting IPv4 address
	
 -dir <path>
 	The directory path for data
 
 -geo <abbreviation>
 	The country abbreviation
HEREDOC;

	$return = file_put_contents('php://stdout', "\n{$message}\n\n", FILE_APPEND);
	if(!is_int($return)) {
		trigger_error("Can't print help message to stdout", E_USER_WARNING);
		return(false);
	}
	
	return(true);
}//}}}

function submain()
{//{{{
	// Preparing to Scan
	//{{{
	$return = Prepare::scan(DATA_PATH, START_IP, GEO_ABBR);
	if(!$return) {
		trigger_error("Prepare for start scan failed", E_USER_WARNING);
		return(false);
	}
	//}}}
	
	// Look up country using IP Address
	//{{{
	$return = Prepare::geoiplookup();
	if(!$return) {
		trigger_error("Prepare geoiplookup failed", E_USER_WARNING);
		return(false);
	}
	$return = Action::geoiplookup();
	if(!$return) {
		trigger_error("Action geoiplookup failed", E_USER_WARNING);
		return(false);
	}
	$return = Process::geoiplookup();
	if(!$return) {
		trigger_error("Process geoiplookup failed", E_USER_WARNING);
		return(false);
	}
	//}}}

	// Nmap host discovery
	//{{{
	$return = Prepare::nmapping();
	if(!$return) {
		trigger_error("Prepare nmapping failed", E_USER_WARNING);
		return(false);
	}
	$return = Action::nmapping();
	if(!$return) {
		trigger_error("Action nmapping failed", E_USER_WARNING);
		return(false);
	}
	$return = Process::nmapping();
	if(!$return) {
		trigger_error("Process nmapping failed", E_USER_WARNING);
		return(false);
	}
	//}}}
	
	// Nmap scan 80,443 ports
	//{{{
	$return = Prepare::nmaptcp();
	if(!$return) {
		//if (defined('DEBUG') && DEBUG) var_dump(['' => ]);
		trigger_error("Prepare nmaptcp failed", E_USER_WARNING);
		return(false);
	}
	$return = Action::nmaptcp();
	if(!$return) {
		trigger_error("Action nmaptcp failed", E_USER_WARNING);
		return(false);	
	}
	$return = Process::nmaptcp();
	if(!$return) {
		trigger_error("Process nmaptcp failed", E_USER_WARNING);
		return(false);
	}
	//}}}
	
	// SSL/TLS Scan a HTTPS servers
	//{{{
	$return = Prepare::sslscan();
	if(!$return) {
		trigger_error("Prepare sslscan failed", E_USER_WARNING);
		return(false);
	}
	$return = Action::sslscan();
	if(!$return) {
		trigger_error("Action sslscan failed", E_USER_WARNING);
		return(false);
	}
	$return = Process::sslscan();
	if(!$return) {
		trigger_error("Process sslscan failed", E_USER_WARNING);
		return(false);
	}
	//}}}
	
	// Check domains found in certificates
	//{{{
	$return = Prepare::nslookup();
	if(!$return) {
		trigger_error("Prepare nslookup failed", E_USER_WARNING);
		return(false);
	}
	$return = Action::nslookup();
	if(!$return) {
		trigger_error("Action nslookup failed", E_USER_WARNING);
		return(false);
	}
	$return = Process::nslookup();
	if(!$return) {
		trigger_error("Process nslookup failed", E_USER_WARNING);
		return(false);
	}
	//}}}
	
	// Loading index pages from addresses and domains
	//{{{
	$return = Prepare::wget();
	if(!$return) {
		trigger_error("Prepare wget failed", E_USER_WARNING);
		return(false);
	}
	$return = Action::wget();
	if(!$return) {
		trigger_error("Action wget failed", E_USER_WARNING);
		return(false);
	}
	$return = Process::wget();
	if(!$return) {
		trigger_error("Process wget failed", E_USER_WARNING);
		return(false);
	}
	//}}}
	
	return(true);
}//}}}

function main(array $ARGUMENT)
{//{{{
	/// Parse command line arguments
	$count = count($ARGUMENT);
	for($index = 0; $index < $count; $index +=1 ) 
	{//{{{
		$argument = &$ARGUMENT[$index];
		switch($argument) {
			case('-h'):
			case('--help'):
				define('HELP', true);
				break;
			case('-ip'):
				if(!key_exists($index + 1, $ARGUMENT)) {
					trigger_error("'ip' value is not set on command line", E_USER_WARNING);
					return(false);
				}
				$index += 1;
				define('START_IP', $ARGUMENT[$index]);
				break;
			case('-dir'):
				if(!key_exists($index + 1, $ARGUMENT)) {
					trigger_error("'dir' value is not set on command line", E_USER_WARNING);
					return(false);
				}
				$index += 1;
				define('DATA_PATH', $ARGUMENT[$index]);
				break;
			case('-geo'):
				if(!key_exists($index + 1, $ARGUMENT)) {
					trigger_error("'string' value is not set on command line", E_USER_WARNING);
					return(false);
				}
				$index += 1;
				define('GEO_ABBR', $ARGUMENT[$index]);
				break;
		}
	}//}}}
	
	if(defined('HELP') && HELP) {
		$return = help();
		return($return);
	}
	
	if(!( defined('START_IP') && defined('DATA_PATH') && defined('GEO_ABBR') )) {
		trigger_error("Some arguments not passed from command line", E_USER_WARNING);
		return(false);
	}
	
	$return = submain();
	return($return);
}//}}}

