<?php

// post_max_size = 0
// upload_max_filesize = 32768M
// max_file_uploads = 1024

set_include_path(__DIR__.'/../include');

require_once('block/default.php');
require_once('class/Upload.php');

Upload::$style .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC

input[id='files']
	{/*{{{*/
		display: none;
	}/*}}}*/
label[name='select_files']
	{/*{{{*/
		line-height: calc(24px + 2ch);
		padding: 1ch;
		background: #555;
		color: #CCC;
		border-radius: 4px;
	}/*}}}*/
.glyphicon[name='select_files']
	{/*{{{*/
		background: #555;
		font-size: 24px;
		line-height: 24px;
		left: -1px;
		top: 6px;
	}/*}}}*/

dialog[name='input_output']
	{/*{{{*/
		display: none;
	}/*}}}*/
iframe[name='input_output']
	{/*{{{*/
		width: 600px;
		height: 300px;
	}/*}}}*/

span[name='php_ini']
	{/*{{{*/
		color: #888;
	}/*}}}*/

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

Upload::$script .= 
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'

function filesOnChange()
{//{{{//

	var $element, $fileList = this.files;
	$element = document.querySelector("span[name='files_number']");
	$element.innerText = $fileList.length;
	
}//}}}//

function windowOnLoad(event)
{//{{{//
	
	var $element;
	
	$element = document.getElementById('files');
	$element.value = null;
	$element.addEventListener('change', filesOnChange, false);

	$element = document.querySelector("form[name='upload_files']");
	$element.addEventListener("submit", function(){
		var $element = document.querySelector("dialog[name='input_output']");
		$element.style.setProperty("display", "flex");
	});
	
	$element = document.querySelector("button[name='close_input_output']");
	$element.addEventListener("click", function(){
		var $element = document.querySelector("dialog[name='input_output']");
		$element.style.setProperty("display", "none");
	});

}//}}}//

window.addEventListener('load', windowOnLoad);

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

main('Upload::page', 'Upload::action');

