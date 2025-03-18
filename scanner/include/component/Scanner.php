<?php

class Scanner
{
	static function main()
	{//{{{//
	
		$return = Data::open(DATABASE_FILE_PATH);
		if(!$return) {
			trigger_error("Can't open database from file", E_USER_WARNING);
			return(false);
		}
		
		if(defined('DISPOSABLE_SOURCE_FILE')) {
			require(DISPOSABLE_SOURCE_FILE);
			return(true);
		}
		
	}//}}}//
	static function get_ELEMENT_URL(string $html)
	{//{{{//
	
		$ELEMENT_URL = [];
		
		$TAG = [
			"form" => 'action',
			"a" => 'href',
			"link" => 'href',
			"script" => 'src',
			"img" => 'src',
		];
		
		foreach($TAG as $tag => $attribute) {
			$ELEMENT_URL[$tag] = [];
			$ELEMENT = Parser::getElementsByTagName($html, $tag);
			
			foreach($ELEMENT as $element) {
				if(!key_exists($attribute, $element['attributes'])) continue;
				array_push($ELEMENT_URL[$tag], $element['attributes'][$attribute]);
			}
		}
		
		return($ELEMENT_URL);
		
	}//}}}//
}

