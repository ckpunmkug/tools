<?php

function create_seo_friendly_urls(string $html)
{
	$result = '';
	
	$replace_href = function(string $href)
	{//{{{//
	
		if($href == '/') return('/');
	
		$result = '';
		
		$array = parse_url($href);
		if(!is_array($array)) {
			trigger_error("Can't parse url", E_USER_WARNING);
			return(false);
		}
		
		$PARAMETER = explode('&', $array["query"]);
		
		foreach($PARAMETER as $parameter) {
			$pattern = '/^tournament\=(.+)$/';
			if(preg_match($pattern, $parameter, $MATCH) == 1) {
				$result = '/';
				$result .= $MATCH[1];
			}
		}
		
		foreach($PARAMETER as $parameter) {
			$pattern = '/^event\=(.+)$/';
			if(preg_match($pattern, $parameter, $MATCH) == 1) {
				if($result != '') $result .= '/';
				$result .= $MATCH[1];
			}
		}
		
		foreach($PARAMETER as $parameter) {
			$pattern = '/^page\=(.+)$/';
			if(preg_match($pattern, $parameter, $MATCH) == 1) {
				if($result != '') $result .= '/';
				$result .= $MATCH[1];
			}
		}
	
		return($result);
	};//}}}//
	
	$dom = new DOMDocument;
	libxml_use_internal_errors(true);
	$dom->loadHTML($html);
	libxml_clear_errors();
	
	$A = $dom->getElementsByTagName('a');
	
	foreach ($A as $a) {
		$href = $a->getAttribute('href');
		$href = $replace_href($href);
		$a->setAttribute('href', $href);
	}
	
	$result = $dom->saveHTML();
	
	return($result);
}

