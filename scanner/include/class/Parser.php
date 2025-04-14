<?php

class Parser
{
	static function getElementsByTagName(string $html, string $tag_name)
	{//{{{//
	
		$result = [];

		$dom = new DOMDocument();
		$return = $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);

		if ($return === false) {
			trigger_error("Can't load html to DOMDocument", E_USER_WARNING);
			return(false);
		}

		$list = $dom->getElementsByTagName($tag_name);

		for ($i = 0; $i < $list->length; $i++) {

			$node = $list->item($i);
			$text = $node->textContent;
			$attributes = array();
			
			for ($j = 0; $j < $node->attributes->length; $j++) {
			
				$attribute = $node->attributes->item($j);
				$attributes[$attribute->nodeName] = $attribute->nodeValue;
			}
			
			array_push($result, [
				'text' => $text
				,'attributes' => $attributes
			]);
		}

		return $result;
		
	}//}}}//
	static function get_domain_info(string $input_string) // array
	{//{{{//
		$info = 
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'

$return = array(4) {
  ["domain"]=>
  string(12) "www.abcd.xyz"
  ["levels"]=>
  int(3)
  ["www"]=>
  int(1)
  ["top"]=>
  string(3) "xyz"
}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$expression = '/[^\-\.a-zA-Z0-9]+/';
		$return = preg_match($expression, $input_string);
		if($return === 1) {
			if (defined('DEBUG') && DEBUG) var_dump(['$input_string' => $input_string]);
			trigger_error("Input string contains unsupported chars", E_USER_WARNING);
			return(false);
		}
		
		$return = strtolower($input_string);
		if(strcmp($return, $input_string) !== 0) {
			if (defined('DEBUG') && DEBUG) var_dump(['$input_string' => $input_string]);
			trigger_error("Input string contains uppercase chars", E_USER_NOTICE);
		}
		$input_string = $return;
		
		$return = trim($input_string, '.');
		if(strcmp($return, $input_string) !== 0) {
			if (defined('DEBUG') && DEBUG) var_dump(['$input_string' => $input_string]);
			trigger_error("Input string have strange form", E_USER_NOTICE);
		}
		$input_string = $return;
		
		$array = explode('.', $input_string);
		$result = [];
		
		$result["www"] = 0;
		if(strcmp($array[0], 'www') === 0) {
			$result["www"] = 1;
			array_shift($array);
		}
		$result["domain"] = implode('.', $array);
		$result["levels"] = count($array);
		$result["top"] = array_pop($array);
		
		return($result);
		
	}//}}}//
	static function get_wordpress_plugins(array $URL)
	{//{{{//
		
		$result = [];
		foreach($URL as $url) {
			
			$regexp = '/^.+\/wp\-content\/plugins\/([^\/]+)\/.+$/';
			$return = preg_match($regexp, $url, $MATCH);
			if($return == 1) {
				if(!in_array($MATCH[1], $result)) {
					array_push($result, $MATCH[1]);
				}
			}
		}
		
		return($result);
		
	}//}}}//

	static function HEADER(array $HEADER) // UNDER CONSTRUCTION
	{//{{{//
		
		$Status = NULL;
		$Location = NULL;
		$Server = NULL;
		
		foreach($HEADER as $header) { //
			$header = trim($header);
		
			if($Status == 200) {
				$pattern = '/^Server\:\s+(.+)$/';
				if(preg_match($pattern, $header, $MATCH) == 1) {
					$Server = $MATCH[1];
					continue;
				}
			}
			else {
				$pattern = '/^HTTP\/1\.1\s+(\d+).*$/';
				if(preg_match($pattern, $header, $MATCH) == 1) {
					$Status = intval($MATCH[1]);
					continue;
				}
				
				$pattern = '/^Location\:\s+(.+)$/';
				if(preg_match($pattern, $header, $MATCH) == 1) {
					$Location = $MATCH[1];
					continue;
				}
			}
			
		} // foreach($HEADER as $header)
		
		$result = [
			'Location' => $Location,
			'Status' => $Status,
			'Server' => $Server,
		];
		return($result);
		
	}//}}}//
}

