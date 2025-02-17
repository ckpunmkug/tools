<?php

/// Usage
/*{{{

HTTP::$user_agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0';
HTTP::$proxy = 'tcp://127.0.0.1:8118';
HTTP::$follow_location = 0;
HTTP::$max_redirects = 1;
HTTP::$timeout = 30;

$return = check_tor_connection();
var_dump($return);

}}}*/

function check_tor_connection()
{//{{{//

	$timeout = HTTP::$timeout;
	HTTP::$timeout = 5.0;
	$return = HTTP::GET('https://check.torproject.org');
	HTTP::$timeout = $timeout;
	if(!is_array($return)) {
		trigger_error("Can't GET https://check.torproject.org", E_USER_WARNING);
		return(false);
	}
	$html = $return["contents"];
	
	$DOMDocument = new DOMDocument;
	$return = $DOMDocument->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
	if(!$return) {
		if (defined('DEBUG') && DEBUG) var_dump(['$html' => $html]);
		trigger_error("Can't can't load html into DOM document", E_USER_WARNING);
		return(false);
	}
	
	$return = $DOMDocument->getElementsByTagName('h1');
	if(!is_object($return)) {
		trigger_error("Can't get H1 elements from DOM document", E_USER_WARNING);
		return(false);
	}
	$DOMNodeList = $return;
	
	$count = $DOMNodeList->count();
	for($index = 0; $index < $count; $index += 1) {//
	
		$return = $DOMNodeList->item($index);
		if(!is_object($return)) {
			trigger_error("Can't get DOM node from H1 elements list", E_USER_WARNING);
			return(false);
		}
		$DOMNode = $return;
		 
		$string = trim($DOMNode->textContent);
		$congratulations = 'Congratulations. This browser is configured to use Tor.';
		if(strcmp($string, $congratulations) === 0) return(true);
		
	}// for($index = 0; $index < $count; $index += 1)

	if (defined('DEBUG') && DEBUG) var_dump(['$html' => $html]);
	trigger_error("Can't found congratulations message in html", E_USER_WARNING);
	return(false);
	
/*
 <h1 class="on">

      Congratulations. This browser is configured to use Tor.

  </h1>
  <p>Your IP address appears to be:  <strong>178.162.197.91</strong></p>
*/
	
}//}}}//

