<?php

class Page
{
	static function index()
	{//{{{//
		
		
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input type="text" list="frequently_values" />
<datalist id="frequently_values">
	<option value="abcd"></option>
	<option value="xyz"></option>
	<option value="123456"></option>
</datalist>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$styles = [
			'share/style/main.css',
		];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
}
