<?php

class test_source
{

	static $URL = [
		"test_source" => URL_PATH.'?component=test_source',
		"coverage" => URL_PATH.'?component=coverage',
	];
	static $style = [
		"index" => 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
iframe {
	display: block;
	width: 100%;
	height: 4lh;
}

HEREDOC,
///////////////////////////////////////////////////////////////}}}//
	];
	static $script = [
		"index" => 
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
var form, action, textarea;

function save()
{//{{{//
	
	action.setAttribute("name", "action");
	action.setAttribute("value", "save");
	form.submit();
	
}//}}}//

function windowOnKeyDown(event)
{//{{{//

	if(
		event.altKey == false
		&& event.ctrlKey == false
		&& event.metaKey == false
		&& event.shiftKey == false
	) {
		switch(event.key) {
			case('Tab'):
			if(document.activeElement == textarea) {
				event.preventDefault();
				event.stopPropagation();
				textarea.setRangeText("\t");
				textarea.selectionStart += 1;
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
			case('s'):
			event.preventDefault();
			event.stopPropagation();
			save();
			break;
		}
		return(true);
	}
	
}//}}}//

function windowOnLoad(event)
{//{{{//
	
	form = document.querySelector('form');
	action = document.getElementById('action');
	window.addEventListener("keydown", windowOnKeyDown);
	
	textarea = document.querySelector('textarea');
	
}//}}}//
window.addEventListener("keydown", windowOnLoad);

HEREDOC,
///////////////////////////////////////////////////////////////}}}//
	];
	
	static function page_index()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["search_result_id"]')) return(false);
		$search_result_id = intval($_GET["search_result_id"]);
		
		$search_result = data::$get["search_result"]($search_result_id);
		if($search_result === false) {
			trigger_error("Can't get 'search_result'", E_USER_WARNING);
			return(false);
		}
		if($search_result === NULL) {
			$search_result = [
				"id" => 0,
				"query" => 0,
				"file" => PATH["cms"],
				"number" => 0,
				"line" => '',
			];
		}
		
		$test_source = data::$get["test_source"]($search_result["id"]);
		if($test_source === false) {
			trigger_error("Can't get 'test_source'", E_USER_WARNING);
			return(false);
		}
		if($test_source === NULL) {
			$test_source = [
				"id" => 0,
				"result" => $search_result["id"],
				"status" => ' ',
				"text" => "<?php \n",
				"file" => $search_result["file"],
			];
		}
		
		$_ = [
			"result" => strval($test_source["result"]),
			"text" => htmlentities($test_source["text"]),
			"file" => t2h($test_source["file"]),
			" " => '',
			"?" => '',
			"!" => '',
			"-" => '',
			"+" => '',
			"coverage" => '',
		];
		
		switch($test_source["status"]) {
			case('?'):
			$_["?"] = 'checked';
			break;
			
			case('!'):
			$_["!"] = 'checked';
			break;
			
			case('-'):
			$_["-"] = 'checked';
			break;
			
			case('+'):
			$_["+"] = 'checked';
			break;
			
			default:
			$_[" "] = 'checked';
			break;
		}
		
		if($test_source["id"] != 0) {
			$_["coverage_href"] = self::$URL["coverage"].'&search_result_id='.strval($search_result["id"]);
			$_["coverage"] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="{$_['coverage_href']}" accesskey="c" target="_blank"><u>C</u>overage</a>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$form = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="result" value="{$_['result']}" type="hidden" />
<input id="action" type="hidden" />

Status
<label accesskey=" ">
	<input name="status" value=" " type="radio" {$_[" "]}/>
	[ ] undefined
</label>
<label accesskey="/">
	<input name="status" value="?" type="radio" {$_["?"]}/>
	[?] unknown
</label>
<label accesskey="1">
	<input name="status" value="!" type="radio" {$_["!"]}/>
	[!] prevented
</label>
<label accesskey="-">
	<input name="status" value="-" type="radio" {$_["-"]}/>
	[-] imposible
</label>
<label accesskey="=">
	<input name="status" value="+" type="radio" {$_["+"]}/>
	[+] complete
</label>

<br />
Coverage <u>f</u>ile
<input name="file" value="{$_['file']}" type="text" size="80" accesskey="f" />
{$_["coverage"]}
<br />

<textarea 
	name="text" 
	cols="120" rows="34" 
	accesskey="t"
	autocomplete="off"
	autocorrect="off"
	spellcheck="false"
	wrap="off"
	>{$_["text"]}</textarea><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$form = layout_form(self::$URL["test_source"], $form, 'target="iframe"');
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$form}
<iframe name="iframe"></iframe>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'test source';
		HTML::$style .= self::$style["index"];
		HTML::$script .= self::$script["index"];
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//
	
	static function action_save()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["result"]')) return(false);
		$result = intval($_POST["result"]);
		
		if(!eval(Check::$string.='$_POST["status"]')) return(false);
		$status = $_POST["status"];
		
		if(!eval(Check::$string.='$_POST["file"]')) return(false);
		$file = $_POST["file"];
		
		if(!eval(Check::$string.='$_POST["text"]')) return(false);
		$text = $_POST["text"];
		
		// [I]ukca = ecJLu B TekcTe ecTb cJLoBo NULL To firefox 3aMeHuT o6bl4Hble npo6eJLbl Ha Hepa3pblBHble
		$text = str_replace("\xC2\xA0", ' ', $text);
		$text = str_replace("\x0D\x0A", "\x0A", $text);
		
		$test_source = data::$get["test_source"]($result, $status, $text, $file);
		if($test_source === false) {
			trigger_error("Can't get 'test_source'", E_USER_WARNING);
			return(false);
		}
		if($test_source === NULL) {
			$return = data::$add["test_source"]($result, $status, $text, $file);
			if(!is_int($return)) {
				trigger_error("Can't add 'test_source'", E_USER_WARNING);
				return(false);
			}
		}
		if(is_array($test_source)) {
			$return = data::$update["test_source"]($result, $status, $text, $file);
			if(!$return) {
				trigger_error("Can't update 'test_source'", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = file_put_contents(PATH["start"], $text);
		if(!is_int($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['aaa' => 111]);
			trigger_error("Can't put 'test source' contents to file", E_USER_WARNING);
			return(false);
		}
		
		echo("Saved");
		//header('Location: '.self::$URL["test_source"].'&search_result_id='.strval($result));
		
		return(true);
		
	}//}}}//
}

