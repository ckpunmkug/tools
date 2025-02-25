<?php

class Parser
{
	static function domain(string $input_string)
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
}

