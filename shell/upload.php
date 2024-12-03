<?php

require_once(__DIR__.'/include/class/Initialization.php');
$Initialization = new Initialization;

require_once(__DIR__.'/include/class/HTML.php');
$HTML = new HTML();

HTML::$styles = [
	'share/style/main.css',
];

class Upload
{//{{{//
	
	static $style = '';
	static $script = '';
	
	static function page()
	{//{{{//
		
	}//}}}//
	
}//}}}//


HTML::$style .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC

body
	{/*{{{*/
		background: #000;
	}/*}}}*/

#upload
	{/*{{{*/
		display: none;
	}/*}}}*/
label[for='upload']
	{/*{{{*/
		padding: 6px 12px;
		background: #000;
		color: #3A0;
		border: solid 2px #666;
		border-radius: 4px;
		box-shadow: 8px 8px 10px #000;
	}/*}}}*/
td[name='upload']
	{/*{{{*/
		line-height: 32px;
		text-align: center;
	}/*}}}*/
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

HTML::$body .=
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div name="center-middle" style="background: #000;">
	<fieldset style="width: 48ch;">
		<table style="width: 100%;">
			<tr>
				<td name="upload">
					<label for="upload">Upload</label>
					<input id="upload" type="file" />
				</td>
			</tr>
		</table>
	</fieldset>
	
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

