<?php

class source_viewer
{
	static $URL = [
		"source_viewer" => URL_PATH.'?component=source_viewer',
	];
	
	static $style = [
		"index" =>
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
#container {
	display: grid;
	grid-template-rows: auto 1fr;
	
	position: absolute;
	top: 0px;
	left: 0px;
	
	min-width: 100%;
	height: 100%;
}
#form {
	display: block;
	padding: 1px;
	border-bottom: 1px solid black;
}
#table {
	display: flex;
	overflow: scroll;
}
pre {
	margin: 0px 0px;
}
HEREDOC,
///////////////////////////////////////////////////////////////}}}//
	];
	
	static function page_index()
	{//{{{//
		
		$table = '';
		$path = '';
		if(!isset($_GET["path"])) goto after_source;
		
		if(!eval(Check::$string.='$_GET["path"]')) return(false);
		$path = $_GET["path"];
		
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
<span id="{$number}">{$number}</span>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

			
			$lines .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$line}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		}// foreach($SOURCE_LINE as $key => $value)
		
		$table = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<table>
	<tr>
		<td valign="top">
<pre><code>{$numbers}</code></pre>
		</td>
		<td valign="top">
<pre><code>{$lines}</code></pre>
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
	<input name="path" value="{$_['path']}" type="text" size="48" accesskey="p" />
</label>
<label>
	Li<u>n</u>e
	<input name="number" value="" type="text" size="5" accesskey="n" />
</label>
<button name="action" value="go" type="submit" accesskey="g"><u>G</u>o</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$form = layout_form(self::$URL["source_viewer"], $form);
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div id="container">
	<div id="form">
{$form}
	</div>
	<div id="table">
{$table}
	</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = t2h($path);
		HTML::$style .= self::$style["index"];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//

	static function action_go()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["path"]')) return(false);
		$path = urlencode($_POST["path"]);
		
		if(!eval(Check::$string.='$_POST["number"]')) return(false);
		$number = intval($_POST["number"]);
		
		if($number == 0) {
			$url = self::$URL["source_viewer"]."&path={$path}";
		}
		else {
			$number = strval($number);
			$url = self::$URL["source_viewer"]."&path={$path}#{$number}";
		}
		
		header("Location: {$url}");
		
		return(true);
		
	}//}}}//
}

