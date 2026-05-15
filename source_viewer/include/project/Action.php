<?php

class Action
{
	static function go()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["path"]')) return(false);
		$path = urlencode($_POST["path"]);
		
		if(!eval(Check::$string.='$_POST["number"]')) return(false);
		$number = intval($_POST["number"]);
		
		if($number == 0) {
			$url = URL_PATH."?path={$path}";
		}
		else {
			$number = strval($number);
			$url = URL_PATH."?path={$path}#{$number}";
		}
		
		header("Location: {$url}");
		
		return(true);
		
	}//}}}//
}

