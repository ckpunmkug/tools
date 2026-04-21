<?php

class debugger
{
	static $URL = [
		"debugger" => URL_PATH.'?component=debugger',
	];
	
	static $style = [
		"index" => 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
#container {
	display: grid;
	grid-template-rows: 1fr auto;
	position: absolute;
	top: 0px;
	left: 0px;
	width: 100%;
	height: 100%;
	
	#editor {
		all: unset;
		display: grid;
		
		form {
			textarea {
				display: block;
				width: 100%;
				height: 100%;
			}
		}
	}
	#transmitter {
		iframe {
			all: unset;
			display: none;
			width: 100%;
			height: 24lh;
		}
	}
}

HEREDOC,
///////////////////////////////////////////////////////////////}}}//
	];
	
	static $script = [
		"index" => 
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
var debug = {
	form: null,
	commands: null,
	
};
var save = {
	form: null,
	commands: null,
	perform: function()
	{//{{{//
		
		save.commands.value = debug.commands.value;
		save.form.submit();
		
	},//}}}//
};
var transmitter = null;

function windowOnKeyDown(event)
{//{{{//

	if(
		event.altKey == false
		&& event.ctrlKey == false
		&& event.metaKey == false
		&& event.shiftKey == false
	) {
		switch(event.key) {
			case('Escape'):
			transmitter.style.setProperty('display', 'none');
			break;
			
			case('Tab'):
			if(document.activeElement == debug.commands) {
				event.preventDefault();
				event.stopPropagation();
				debug.commands.setRangeText("\t");
				debug.commands.selectionStart += 1;
			}
			break;
		}
		return(true);
	}
	
	if(
		event.altKey == false
		&& event.ctrlKey == true
		&& event.metaKey == false
		&& event.shiftKey == false
	) {
		switch(event.key) {
			case('Enter'):
			event.preventDefault();
			event.stopPropagation();
			debug.form.submit();
			break;
			
			case('s'):
			event.preventDefault();
			event.stopPropagation();
			save.perform();
			break;
		}
		return(true);
	}
	
}//}}}//

function windowOnLoad(event)
{//{{{//

	window.addEventListener("keydown", windowOnKeyDown);
	
	debug.form = document.querySelector('form[name="debug"]');
	debug.commands = debug.form.querySelector('textarea[name="commands"]');
	
	save.form = document.querySelector('form[name="save"]');
	save.commands = save.form.querySelector('input[name="commands"]');
	
	transmitter = document.querySelector('iframe[name="transmitter"]');
	transmitter.addEventListener("load", function(event) {
		var $text = transmitter.contentDocument.body.innerText.trim();
		if($text != 'complete') {
			transmitter.style.setProperty('display', 'block');
		}
	});
	
}//}}}//
window.addEventListener("keydown", windowOnLoad);

HEREDOC,
///////////////////////////////////////////////////////////////}}}//
	];

	static function page_index()
	{//{{{//
		
		$commands = self::extension_load();
		if(!is_string($commands)) {
			trigger_error("Can't load 'commands'", E_USER_WARNING);
			return(false);
		}

		$_ = [
			"commands" => htmlentities($commands),
		];
		
		$debug_form = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="action" value="debug" type="hidden" />
<textarea 
	name="commands"
	accesskey="t"
	autocomplete="off"
	autocorrect="off"
	spellcheck="false"
	style="white-space: nowrap;"
	>{$_["commands"]}</textarea>
	
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$debug_form = layout_form(self::$URL["debugger"], $debug_form, 'name="debug"');
		
		$save_form = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="commands" value="" type="hidden" />
<input name="action" value="save" type="hidden" />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$save_form = layout_form(self::$URL["debugger"], $save_form, 'name="save" target="transmitter"');
		$save_iframe = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<iframe name="transmitter"></iframe>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div id="container">
	<div id="editor">
{$debug_form}
	</div>
	<div id="transmitter">
{$save_form}
{$save_iframe}
	</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'debugger';
		HTML::$style = self::$style["index"];
		HTML::$script = self::$script["index"];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
	
	static function action_debug()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["commands"]')) return(false);
		$commands = $_POST["commands"];
		
		$return = self::extension_save($commands);
		if(!$return) {
			trigger_error("Can't save 'commands'", E_USER_WARNING);
			return(false);
		}
		
		require_once('class/PHPDebugger.php');
		
		$debugger = new PHPDebugger(PATH["start"], PATH["cms"], NULL, 10, false);
		$COMMAND = explode("\n", $commands);
		
		$output = '';
		foreach($COMMAND as $command) {
			$command = trim($command);
			if(substr($command, 0, 1) == '#') continue;
			$output .= "> {$command}\n";
			
			$return = $debugger->send($command);
			$output .= $return;
			if(strpos($return, '[Script ended normally]') !== false) break;
		}
		
		$_ = [
			"output" => t2h($output, true),
		];
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<code>{$_["output"]}</code>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		HTML::$title = 'debugger output';
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//

	static function action_save()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["commands"]')) return(false);
		$commands = $_POST["commands"];
		
		$return = self::extension_save($commands);
		if(!$return) {
			trigger_error("Can't save 'commands'", E_USER_WARNING);
			return(false);
		}
		
		HTML::$body = 'complete';
		HTML::echo();
		
		return(true);
		
	}//}}}//

	static function extension_save(string $commands)
	{//{{{//
	
		$commands = str_replace("\xC2\xA0", ' ', $commands);
		$commands = str_replace("\x0D\x0A", "\x0A", $commands);
	
		$return = file_put_contents(PATH["commands"], $commands);
		if(!is_int($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['PATH["commands"]' => PATH["commands"]]);
			trigger_error("Can't put 'commands' to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function extension_load()
	{//{{{//
		
		$commands = file_get_contents(PATH["commands"]);
		if(!is_string($commands)) {
			if(defined('DEBUG') && DEBUG) var_dump(['PATH["commands"]' => PATH["commands"]]);
			trigger_error("Can't get 'commands' from file", E_USER_WARNING);
			return(false);
		}
		
		return($commands);
		
	}//}}}//
}

