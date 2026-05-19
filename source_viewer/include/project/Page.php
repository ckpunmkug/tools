<?php

class Page
{
	static function index()
	{//{{{//
			
		$table = '';
		$path = '';
		if(!isset($_GET["path"])) goto after_source;
		
		if(!eval(Check::$string.='$_GET["path"]')) return(false);
		$path = $_GET["path"];
		
		$pattern = '/^(.+):([0-9]+)$/';
		$return = preg_match($pattern, $path, $MATCH);
		if($return == 1) {
			$_ = [
				"location" => URL_PATH.'?path='.urlencode($MATCH[1])."#{$MATCH[2]}",
			];
			header("Location: {$_['location']}");
			return(true);
		}
		
		$return = FileSystem::is_file_rwx($path, true, false, false);
		if(!$return) {
			trigger_error("Incorrect passed 'path'", E_USER_WARNING);
			return(false);
		}
		
		$return = file_get_contents($path);
		if(!is_string($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't get contents from 'source' file", E_USER_WARNING);
			return(false);
		}
		$LINE = explode("\n", $return);
		
		$numbers = '';
		$lines = '';
		foreach($LINE as $key => $value) {
		
			$number = strval($key+1);
			$line = t2h(rtrim($value, "\r\n"));
			
			$numbers .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<span id="{$number}">{$number}</span><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

			
			$lines .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$line}<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		}// foreach($SOURCE_LINE as $key => $value)
		
		$table = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<table>
	<tr>
		<td>
{$numbers}
		</td>
		<td style="white-space: nowrap;">
{$lines}
		</td>
	</tr>
</table>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		after_source:
		
		$_ = [
			"path" => t2h($path),
		];
		$form = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<label>
	<u>P</u>ath
	<input name="path" value="{$_['path']}" type="text" size="80" accesskey="p" />
</label>
<label>
	Li<u>n</u>e
	<input name="number" value="" type="text" size="5" accesskey="n" />
</label>
<button name="action" value="go" type="submit" accesskey="g"><u>G</u>o</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$form = layout_form(URL_PATH, $form);
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div id="window">
	<div id="header">
{$form}
	</div>
	<div id="main">
{$table}
	</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
		$dirname = dirname($path);
		$dirname = basename($dirname);
		$filename = basename($path);
		
		$_ = [
			"title" => htmlentities("{$dirname}/{$filename}"),
		];
		
		HTML::$title = $_["title"];
		HTML::$styles = [
			'share/style/main.css',
			'share/style/index.css',
		];
		HTML::$scripts = [
			'share/script/dblclick_regexp_selection.js',
		];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
}
