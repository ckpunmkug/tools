<?php

class prediction
{
	static $check = [];
	
	static function init()
	{
		
		self::$check["match"] = function()
		{//{{{//
			
			var_dump('xa-xa-xa');
			
		};//}}}//
		
		self::$check["test"] = function()
		{//{{{//
			
			return(true);
			
		};//}}}//
		
		self::$check["null"] = function()
		{//{{{//
			
			return(NULL);
			
		};//}}}//
		
	}
	
	static function replace_tags(array $tags, string $prompt)
	{//{{{//
		
		foreach($tags as $tag => $string) {
			if(@strval($tag) == '' || @strval($string) == '') {
				if (defined('DEBUG') && DEBUG) var_dump(['$tags' => $tags]);
				trigger_error("Incorrect incoming parameters", E_USER_WARNING);
				return(false);
			}
			
			$tag = preg_quote($tag, '/');
			$pattern = '/%'.$tag.'%/';
			$return = preg_replace($pattern, $string, $prompt);
			if($return === NULL) {
				trigger_error("preg replace error", E_USER_WARNING);
				return(false);
			}
			if(strcmp($return, $prompt) === 0) {
				if (defined('DEBUG') && DEBUG) var_dump(['tag' => $tag]);
				trigger_error("Tag in prompt not found", E_USER_WARNING);
				return(false);
			}
			
			$prompt = $return;
		}
		
		return($prompt);
		
	}//}}}//

	static function prepare_answer(string $answer)
	{//{{{//
		
		$pattern = "/^```json\n/";
		$answer = preg_replace($pattern, '', $answer);
		$pattern = "/\n```$/";
		$answer = preg_replace($pattern, '', $answer);
		
		$return = json_decode($answer, true);
		if(!is_array($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['answer' => $answer]);
			trigger_error("Can't decode 'answer' from json", E_USER_WARNING);
			return(false);
		}
		$answer = $return;
		
		if(
			@strval($answer["teams_analysis"]) == ''
			|| @strval($answer["key_factors"]) == ''
			|| @strval($answer["prediction"]) == ''
			|| @strval($answer["probability_count"]) == ''
			|| @strval($answer["total"]) == ''
			|| @strval($answer["keywords"]) == ''
			|| @strval($answer["description"]) == ''
		) {
			if (defined('DEBUG') && DEBUG) var_dump(['answer' => $answer]);
			trigger_error("Incorrect 'answer' array", E_USER_WARNING);
			return(false);
		}
		
		$json = json_encode($answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		
		return($json);
		
	}//}}}//

}

