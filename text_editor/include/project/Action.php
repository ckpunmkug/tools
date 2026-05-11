<?php

class Action
{
	static function save()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["path"]')) return(false);
		$path = $_POST["path"];
		
		if(!eval(Check::$string.='$_POST["text"]')) return(false);
		$text = $_POST["text"];
		
		$return = '';
		$length = strlen($text);
		for($offset = 0; $offset < $length; $offset += 1) {
			$char = substr($text, $offset, 1);
			$ord = ord($char);
			if($ord != 0x0D) $return .= $char;
		}
		$text = $return;
		
		$return = file_put_contents($path, $text);
		if(!is_int($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't put contents to file", E_USER_WARNING);
			return(false);
		}
		$bytes = $return;
		
		$_ = [
			"path" => htmlentities($path),
			"bytes" => strval($bytes),
		];
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div id="whitespace"></div>
{$_["bytes"]} bytes saved in {$_["path"]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'complete'; // iframe.contentDocument.title
		HTML::$styles = [
			"share/style/main.css",
			"share/style/editor.css",
		];
	HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
	
	static function load()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["path"]')) return(false);
		$path = trim($_POST["path"]);
		
		$return = is_file($path);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("The given path is not a regular file. ", E_USER_WARNING);
			return(false);
		}
		
		$_ = [
			"path" => urlencode($path),
		];
		header("Location: ".URL_PATH."?path={$_['path']}");
		
		return(true);
		
	}//}}}//
}

