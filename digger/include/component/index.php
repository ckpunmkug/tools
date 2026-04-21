<?php

class index
{
	static function page_index()
	{//{{{//
	
		$href_prefix = URL_PATH.'?component=';
		
		HTML::$title = 'digger';
		HTML::$body .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="{$href_prefix}preg_search" accesskey="1">1 preg search</a>,
<a href="{$href_prefix}files_list" accesskey="2">2 files list</a>,
<a href="{$href_prefix}debugger" accesskey="3">3 debugger</a>,
<a href="{$href_prefix}notes" accesskey="4">4 notes</a>,
<hr />

Quick switch to any 'textarea' Alt+Shift+T<br />
Start debugger Ctrl+Enter

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		HTML::echo();
		
		return(true);
		
	}//}}}//
}

