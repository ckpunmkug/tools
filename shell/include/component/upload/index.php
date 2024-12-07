<?php

// post_max_size = 0
// upload_max_filesize = 32768M
// max_file_uploads = 1024

require_once('component/upload/Upload.php');

Upload::$style .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC

body
	{/*{{{*/
		background: #000;
	}/*}}}*/

input[type='file']
	{/*{{{*/
		display: none;
	}/*}}}*/
label[name='upload']
	{/*{{{*/
		margin-bottom: 1ch;
		line-height: calc(1lh + 3ch);
		padding: 1ch 1ch;
		background: #555;
	}/*}}}*/
.glyphicon
	{/*{{{*/
		background: #555;
		font-size: 24px;
		top: 6px;
		left: -1px;
	}/*}}}*/

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

Upload::page();

