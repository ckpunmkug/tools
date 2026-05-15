<?php

class Page
{
	static function index()
	{//{{{//
		
		$path = PATH['cms'];
		if(isset($_GET["path"])) {
			if(!eval(Check::$string.='$_GET["path"]')) return(false);
			$path = $_GET["path"];
		}
		
		$filter = '/^.+\.php$/';
		if(isset($_GET["filter"])) {
			if(!eval(Check::$string.='$_GET["filter"]')) return(false);
			$filter = $_GET["filter"];
		}
		
		$return = FileSystem::get_dir_contents($path);
		if(!is_array($return)) {
			trigger_error("Can't get contents from directory", E_USER_WARNING);
			return(false);
		}
		$PATH = $return;
		
		$return = Method::apply_path_filter($PATH, $filter);
		if(!is_array($return)) {
			trigger_error("Can't apply filter to pathes list", E_USER_WARNING);
			return(false);
		}
		$PATH = $return;
		
		$list = '';
		foreach($PATH as $path) {
			$_ = [
				"href" => URL["source_viewer"].'?path='.urlencode($path),
				"text" => htmlentities($path),
			];
			$list .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="{$_['href']}">{$_["text"]}</a><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
			
		HTML::$title = 'files list';
		HTML::$styles = [
			"share/style/main.css",
		];
		HTML::$body = $list;
		HTML::echo();
		
		return(true);
		
	}//}}}//
}
