<?php

class Page
{
	static function index()
	{//{{{//
		
		$_ = [
			"coverage" => URL_PATH.'?page=coverage',
			"debugger" => URL_PATH.'?page=debugger',
			"source" => URL["text_editor"].'?path='.urlencode(PATH["source"]),
			"commands" => URL["text_editor"].'?path='.urlencode(PATH["commands"]),
		];
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="{$_['coverage']}" accesskey="1"><u>1</u> coverage</a><br />
<a href="{$_['debugger']}" accesskey="2"><u>2</u> debugger</a><br />
<a href="{$_['source']}" accesskey="3"><u>3</u> edit source</a><br />
<a href="{$_['commands']}" accesskey="4"><u>4</u> edit commands</a><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'index';
		HTML::$styles = [
			'share/style/main.css',
		];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
	
	static function coverage()
	{//{{{//
	
		//var_dump($_GET); die;
	
		$file = NULL;
		if(
			isset($_GET["file"])
			&& is_string($_GET["file"])
			&& strlen($_GET["file"]) > 0
		) {
			$file = strval($_GET["file"]);
		}
	
		$from = NULL;
		if(
			isset($_GET["from"])
			&& is_string($_GET["from"])
			&& strlen($_GET["from"]) > 0
		) {
			$from = intval($_GET["from"]);
		}
	
		$to = NULL;
		if(
			isset($_GET["to"])
			&& is_string($_GET["to"])
			&& strlen($_GET["to"]) > 0
		) {
			$to = intval($_GET["to"]);
		}
		
		$ENVIRONMENT = [
			"PHP_INI_SCAN_DIR" => PATH["php_ini"],
		];
	
		$command = BIN["php"].' '.PATH["source"];
		$return = launch($command, 10, $ENVIRONMENT);
		if(!is_array($return)) {
			trigger_error("Can't launch 'php' with 'start.php'", E_USER_WARNING);
			return(false);
		}
		$status = $return["status"];
		$stdout = $return["stdout"];
		$stderr = $return["stderr"];
		
		$return = Method::get_u80_data($stderr);
		if(!is_array($return)) {
			trigger_error("Can't get 'u80' data", E_USER_WARNING);
			return(false);
		}
		
		$u80_data = $return["data"];
		$stderr = $return["text"];
		
		$return = Method::u80_data_to_html($u80_data, $file, $from, $to);
		$headers = $return["headers"];
		$code_coverage = $return["code_coverage"];
		
		$status = strval($status);
		$stderr = t2h($stderr, true);
		$stdout = t2h($stdout, true);
		
		$url_path = URL_PATH;
		
		$_ = [
			"file" => '',
			"from" => '',
			"to" => '',
		];
		if(is_string($file)) {
			$_["file"] = htmlentities($file);
		}
		if(is_int($from)) {
			$_["from"] = strval($from);
		}
		if(is_int($to)) {
			$_["to"] = strval($to);
		}
		
		$form = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<form action="{$url_path}" method="get">
	<input name="page" value="coverage" type="hidden" />
	<label>
		File
		<input name="file" value="{$_["file"]}" size="80" type="text" accesskey="f" />
	</label>
	<label>
		From
		<input name="from" value="{$_["from"]}" size="5" type="text" accesskey="b" />
	</label>
	<label>
		To
		<input name="to" value="{$_["to"]}" size="5" type="text" accesskey="e" />
	</label>
	<button type="submit" accesskey="g">Coverage</button>
</form>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$form}
<h4>headers</h4>
<div class="output">{$headers}</div>

<h4>code coverage</h4>
<div class="output" style="white-space: wrap;">{$code_coverage}</div>

<h4>status</h4>
<div class="output">{$status}</div>

<h4>stderr</h4>
<div class="output">{$stderr}</div>

<h4>stdout</h4>
<div class="output">{$stdout}</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = "coverage";
		HTML::$styles = [
			'share/style/main.css',
			'share/style/extend.css',
		];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
			
	}//}}}//

	static function debugger()
	{//{{{//
		
		$return = file_get_contents(PATH["commands"]);
		if(!is_string($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['PATH["commands"]' => PATH["commands"]]);
			trigger_error("Can't get contents of 'commands' file", E_USER_WARNING);
			return(false);
		}
		$COMMAND = explode("\n", $return);
		
		$debugger = new PHPDebugger(PATH["source"], PATH["cms"], NULL, 10, false);
		
		$output = '';
		foreach($COMMAND as $command) {
			$command = trim($command);
			if(substr($command, 0, 1) == '#') continue;
			$output .= "> {$command}\n";
			
			$return = $debugger->send($command);
			$output .= $return;
			if(strpos($return, '[Script ended normally]') !== false) break;
			
			if($command == 'quit' || $command == 'q') break;
		}
		
		$return = Method::get_u80_data($output);
		
		$html = Method::u80_data_to_html($return["data"]);
		
		$html["text"] = Method::output_to_html($return["text"]);
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h4>headers</h4>
<div class="output">{$html["headers"]}</div>

<h4>output</h4>
<div class="output">{$html["text"]}</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = "debugger";
		HTML::$styles = [
			'share/style/main.css',
			'share/style/extend.css',
		];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
}
