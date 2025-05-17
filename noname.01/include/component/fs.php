<?php

class fs
{
	static $script =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
var $data = {
	"/": {
		"folder": {
			"zzz": {
				
			}
			,"aaa": "?component=list&file=./aaa"
		}
		,"bbb": "?component=list&file=./bbb" 
	}
}

function windowOnLoad(event)
{//{{{//
	fs.main();
}//}}}//
window.addEventListener("load", windowOnLoad);

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

	static function page()
	{//{{{//
		
		HTML::$scripts = [
			"share/script/fs.js",
		];
		HTML::$script .= self::$script;
		HTML::$body .=
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC

<div name="fs">
	<div name="tree"
		><ul name="root"
			><li name="branch"
				><span name="branch">branch</span
				><ul name="branch"
				></ul
			></li
			><li class="leaf"
				><span class="leaf">leaf</span
			></li
		></ul
	></div>
	<div name="input">input</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		return(true);
		
	}//}}}//
	
}

