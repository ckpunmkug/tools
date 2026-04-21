<?php

class coverage
{
	static $BIN = [
		"php" => '/usr/bin/php',
	];
	static $URL = [
		"coverage" => URL_PATH.'?component=coverage',
	];
	
	static function page_index()
	{//{{{//
	
		if(!eval(Check::$string.='$_GET["search_result_id"]')) return(false);
		$search_result_id = $_GET["search_result_id"];
		
		$test_source = data::$get["test_source"]($search_result_id);
		if(!is_array($test_source)) {
			trigger_error("Can't get 'test_source'", E_USER_WARNING);
			return(false);
		}
			
		$GLOBALS_path = DIR.'/include/block/GLOBALS.php';
		$u80_path = DIR.'/include/class/u80.php';
		$cms_path = PATH['cms'];
		$source = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<?php
require('{$GLOBALS_path}');
require('{$u80_path}');
chdir('{$cms_path}');
?>{$test_source["text"]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$return = file_put_contents(PATH["start"], $source);
		if(!is_int($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['PATH["start"]' => PATH["start"]]);
			trigger_error("Can't put contents to 'start.php' file", E_USER_WARNING);
			return(false);
		}
		
		$command = self::$BIN["php"].' -c '.PATH["php_ini"]["coverage"].' '.PATH["start"];
		$return = launch($command);
		if(!is_array($return)) {
			trigger_error("Can't launch 'php' with 'start.php'", E_USER_WARNING);
			return(false);
		}
		$status = $return["status"];
		$stdout = $return["stdout"];
		$stderr = $return["stderr"];
		
		$u80 = self::extension_parse_u80($stderr);
		if(!is_array($u80)) {
			trigger_error("Can't parse 'u80' data", E_USER_WARNING);
			return(false);
		}
		
		$numbers = '';
		if(!key_exists($test_source["file"], $u80["COVERAGE"])) {
			if(defined('DEBUG') && DEBUG) var_dump(['$test_source["file"]' => $test_source["file"]]);
			trigger_error("Can't 'coverage' for passed file", E_USER_NOTICE);
		}
		else {
			foreach($u80["COVERAGE"][$test_source["file"]] as $number => $value) {
				$numbers .= ' '.strval($number); 
			}
		}
		
		$_ = [
			"stdout" => htmlentities($stdout),
			"stderr" => htmlentities($u80["stderr"]),
		];
		
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<code>{$numbers}</code><hr />
stderr<br />
<textarea cols="120" rows="10" readonly>{$_["stderr"]}</textarea><br />
stdout<br />
<textarea cols="120" rows="20" readonly>{$_["stdout"]}</textarea><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = "coverage";
		HTML::$body = $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//

	static function page_GLOBALS()
	{//{{{//
		
		$path = DIR.'/include/block/GLOBALS.php';
		$source = file_get_contents($path);
		if(!is_string($source)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
			trigger_error("Can't get contents of 'GLOBALS.php' file", E_USER_WARNING);
			return(false);
		}
		
		$_ = [
			"source" => t2h($source, true),
		];
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<code>{$_["source"]}</code>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'GLOBALS';
		HTML::$body .= $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//

	static function action_coverage()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["text"]')) return(false);
		$text = $_POST["text"];
		
		$return = file_put_contents(PATH["start"], $text);
		if(!is_int($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['PATH["start"]' => PATH["start"]]);
			trigger_error("Can't put contents to 'start.php' file", E_USER_WARNING);
			return(false);
		}
		
		$command = self::$BIN["php"].' '.PATH["start"];
		$return = launch($command);
		if(!is_array($return)) {
			trigger_error("Can't launch 'php' with 'start.php'", E_USER_WARNING);
			return(false);
		}
		$status = $return["status"];
		$stdout = $return["stdout"];
		$stderr = $return["stderr"];

		$_ = [
			"status" => strval($status),
			"stdout" => t2h($stdout, true),
			"stderr" => htmlentities($stderr),
		];
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="#status">status</a>
<a href="#stdout">stdout</a>
<a href="#stderr">stderr</a>
<fieldset>
	<legend>status</legend>
	<code id="status">{$_["status"]}</code>
</fieldset>
<fieldset>
	<legend>stdout</legend>
	<code id="stdout">{$_["stdout"]}</code>
</fieldset>
<fieldset>
	<legend>stderr</legend>
	<pre id="stderr">{$_["stderr"]}</pre>
</fieldset>
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'coverage result';
		HTML::$body .= $body;
		HTML::echo();
		
		return(true);
		
	}//}}}//

	static function extension_parse_u80(string $stderr)
	{//{{{//
		
		$pattern = '/^([^\x80]*)\x80([^\x80]+)\x80([^\x80]*)$/';
		$return = preg_match($pattern, $stderr, $MATCH);
		if($return !== 1) {
			trigger_error("Can't find 'u80' data", E_USER_WARNING);
			return(false);
		}
		$prefix = $MATCH[1];
		$json = $MATCH[2];
		$apendix = $MATCH[3];
		
		$string = $prefix.$apendix;
		$HEADER = [];
		$stderr = '';
		while(true) {
			$pattern = '/^([^\x81]*)\x81([^\x81]*)\x81(.*)$/';
			$return = preg_match($pattern, $string, $MATCH);
			if($return == 1) {
				array_push($HEADER, $MATCH[2]);
				$stderr .= $MATCH[1];
				$string = $MATCH[3];
			}
			else {
				$stderr .= $string;
				break;
			}
		}
		
		$COVERAGE = decode($json);
		if(!is_array($COVERAGE)) {
			trigger_error("Can't decode 'COVERAGE' from json", E_USER_WARNING);
			return(false);
		}
		
		$result = [
			"COVERAGE" => $COVERAGE,
			"HEADER" => $HEADER,
			"stderr" => $stderr,
		];
		
		return($result);
		
	}//}}}//
}

