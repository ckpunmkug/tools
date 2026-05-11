<?php

class Page
{
	static function index()
	{//{{{//
	
		$path = '';
		if(isset($_GET["path"])) {
			if(!eval(Check::$string.='$_GET["path"]')) return(false);
			$path = $_GET["path"];
		}
		
		$text = '';
		if($path != '') {
			$return = file_get_contents($path);
			if(!is_string($return)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
				trigger_error("Can't get contents from file", E_USER_WARNING);
				return(false);
			}
			$text = $return;
		}
		
		$_ = [
			"path" => htmlentities($path),
			"text" => htmlentities($text),
		];
		$form = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="action" value="" type="hidden" />
<div id="container">
	<span class="fixed">
		<label for="text"><u>T</u>ext</label>
		<label for="path"><u>P</u>ath</label>
	</span>
	<span class="flex">
		<input name="path" value="{$_['path']}" type="text" id="path" size="80" accesskey="p" />
	</span>
	<span class="fixed">
		<button name="action" value="load" type="submit" formtarget="_self" accesskey="l"><u>L</u>oad</button>
		<button name="action" value="save" type="submit" accesskey="s"><u>S</u>ave</button>
	</span>
</div>

HEREDOC;

///////////////////////////////////////////////////////////////}}}//
		$form = layout_form(URL_PATH, $form, 'id="controller" target="transceiver"');
	
		$textarea = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<textarea
	name="text" form="controller" id="text"
	cols="128" rows="36" wrap="off" autofocus accesskey="t"
	autocapitalize="none" autocomplete="off" autocorrect="off" spellcheck="false"
>{$text}</textarea>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<iframe name="transceiver" width="1024" height="96"></iframe>

<div id="window">
	<div id="header">
{$form}
	</div>
	<div id="main">
{$textarea}
	</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$title = basename($path);
		$_ = [
			"title" => htmlentities($title),
		];
		
		HTML::$title = $_["title"];
		HTML::$styles = [
			"share/style/main.css",
			"share/style/editor.css",
		];
		HTML::$scripts = [
			'share/script/editor.js',
			'share/script/main.js',
		];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
}
