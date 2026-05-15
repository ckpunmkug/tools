<?php

class Page
{
	static function index()
	{//{{{//
		
		$return = Method::setup();
		if(!$return) {
			trigger_error("Can't setup", E_USER_WARNING);
			return(false);
		}
		
		$_ = [
			"files_list" => URL["files_list"],
			"preg_search" => URL["preg_search"],
			"php_tester" => URL["php_tester"],
			"source_viewer" => URL["source_viewer"],
			"status_tree" => URL_PATH.'?page=status_tree',
			"text_editor" => URL["text_editor"],
		];
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="{$_['files_list']}" accesskey="1"><u>1</u> files_list</a><br />
<a href="{$_['preg_search']}" accesskey="2"><u>2</u> preg_search</a><br />
<a href="{$_['php_tester']}" accesskey="3"><u>3</u> php_tester</a><br />
<a href="{$_['source_viewer']}" accesskey="4"><u>4</u> source_viewer</a><br />
<a href="{$_['status_tree']}" accesskey="5"><u>5</u> status_tree</a><br />
<a href="{$_['text_editor']}" accesskey="6"><u>6</u> text_editor</a><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'digger';
		HTML::$styles = [
			'share/style/main.css',
		];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//

	static function status_tree()
	{//{{{//
		
		$return = Method::create_status_tree();
		if(!is_string($return)) {
			trigger_error("Can't create 'status' tree", E_USER_WARNING);
			return(false);
		}
		$tree = $return;
		
		$form = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<label>
	Search <u>q</u>uery id
	<input name="query" value="" type="text" size="4" accesskey="q" />
</label>
<br />

<label>
	Search <u>r</u>esult id
	<input name="result" value="" type="text" size="4" accesskey="r" />
</label>
<br />

<label>
	<u>T</u>est status
	<input name="status" value="" type="text" size="1" accesskey="t" />
</label>
<br />

<button name="action" value="set_status" type="submit" accesskey="s"><u>S</u>et</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$form = layout_form(URL_PATH, $form);
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$form}
{$tree}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body = $body;
		HTML::$styles = [
			'share/style/main.css',
			'share/style/status_tree.css',
		];
		HTML::$scripts = [
			'share/script/main.js',
		];
		HTML::echo();
		
		return(true);
		
	}//}}}//
}
