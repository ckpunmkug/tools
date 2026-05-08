<?php

class Page
{
	static function index()
	{//{{{//
		
		header('Location: '.URL_PATH.'?page=search_query');
		return(true);
		
	}//}}}//
	
	static function help()
	{//{{{//
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h4>How to rewrite default 'search path' and 'file pattern'</h4>
In <b>URL query</b><br />
<br>
parameter 'path' - set 'search path'<br />
parameter 'filter' - set 'file pattern'<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body .= $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
	
	static function search_query()
	{//{{{//
		
		$path = PATH["cms"];
		if(isset($_GET["path"]) && is_string($_GET["path"])) {
			$path = $_GET["path"];
		}
		
		$filter = '/^.+\\.php$/';
		if(isset($_GET["filter"]) && is_string($_GET["filter"])) {
			$filter = $_GET["filter"];
		}
		
		$return = data::$get["SEARCH_QUERY"]();
		if(!is_array($return)) {
			trigger_error("Can't get 'SEARCH_QUERY'", E_USER_WARNING);
			return(false);
		}
		$SEARCH_QUERY = $return;
		
		$tree = Layout::patterns_tree($SEARCH_QUERY);
		
		$_ = [
			"path" => htmlentities($path),
			"filter" => htmlentities($filter),
			"help" => URL_PATH.'?page=help',
		];
		
		$form = 
//////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div id="buttons">
	<button name="action" value="search" type="submit" accesskey="s"><u>S</u>earch</button>
	<button name="action" value="delete_query" type="submit" accesskey="d"><u>D</u>elete</button>
	<a href="{$_['help']}" class="button" accesskey="h"><u>H</u>elp</a>
</div><br />

<div id="path">
	Search <u>p</u>ath<br />
	<input name="path" value="{$_['path']}" type="text" accesskey="p" />
</div><br />

<div id="filter">
	<u>F</u>ile pattern<br />
	<input name="filter" value="{$_['filter']}" type="text" accesskey="f" />
</div><br />

<div id="pattern">
	<u>L</u>ine pattern<br />
	<input name="pattern" value="//i" type="text" accesskey="l" autofocus />
</div><br />

<div id="tree">
Patterns <u>t</u>ree<br />
{$tree}
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$body = layout_form(URL_PATH, $form);
		
		array_push(HTML::$styles, 'share/style/search_query.css');
		HTML::$body .= $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
	
	static function search_results()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["query"]')) return(false);
		$query = $_GET["query"];
		
		$return = data::$get["SEARCH_RESULT"]($query);
		if(!is_array($return)) {
			trigger_error("Can't get 'SEARCH_RESULT'", E_USER_WARNING);
			return(false);
		}
		$SEARCH_RESULT = $return;
		
		$list = '';
		$counter = 0;
		foreach($SEARCH_RESULT as $search_result) {
			
			$index = strval($counter);
			$id = strval($search_result["id"]);
			$tabindex = strval($counter+1);
			$href = 
				URL["source_viewer"]
				.'?path='.urlencode($search_result["file"])
				.'#'.strval($search_result["number"]);
			$text = t2h($search_result["line"]);
			
			$list .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div style="white-space: nowrap;">
	{$id}
	<input name="ID[{$index}]" value="{$id}" type="checkbox" tabindex="{$tabindex}" />
	<a href="{$href}" class="code">{$text}</a>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$counter += 1;
			
		}// foreach($SEARCH_RESULT as $search_result)
		
		$form = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div id="container">
	<div id="buttons">
		<button name="action" value="delete_results" type="submit" accesskey="d"><u>D</u>elete</button>
	</div>
	<div id="list">
{$list}
	</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$form = layout_form(URL_PATH, $form);
		
		HTML::$title = 'search results';
		HTML::$body .= $form;
		HTML::echo();
		
		return(true);
		
	}//}}}//
}
