<?php

class files_list
{
	static $URL = [
		"source_viewer" => URL_PATH.'?component=source_viewer',
	];
	
	static function page_index()
	{//{{{//
		
		$PHP_FILE = data::$get["PHP_FILE"]();
		if(!is_array($PHP_FILE)) {
			trigger_error("Can't get 'PHP_FILE'", E_USER_WARNING);
			return(false);
		}
		
		$list = '';
		foreach($PHP_FILE as $php_file) {
			$_ = [
				"href" => self::$URL["source_viewer"].'&path='.urlencode($php_file),
				"text" => t2h($php_file),
			];
			$list .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="{$_['href']}">{$_["text"]}</a><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}// foreach($PHP_FILE as $php_file)
		
		HTML::$title = 'files list';
		HTML::$body .= $list;
		HTML::echo();
		
		return(true);
		
	}//}}}//
}

