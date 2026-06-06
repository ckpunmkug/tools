<?php

class Method
{
	static function setup()
	{//{{{//
		
		$PATH = [
			"u80" => DIR.'/include/class/u80.php',
			"GLOBALS" => DIR.'/include/block/GLOBALS.php',
			"DOCUMENT_ROOT" => PATH["cms"],
		];
		
		$contents = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<?php

define("\\xC2\\x80", 'path_to_coverage_source');
require('{$PATH["u80"]}');
require('{$PATH["GLOBALS"]}');

\${"\\xC2\\x80"} = [
	"shell" => '/tmp/shell.php',
	"host" => 'localhost',
	"script" => '/index.php',
];
file_put_contents(\${"\\xC2\\x80"}["shell"], '<?php phpinfo();');
chdir('{$PATH["DOCUMENT_ROOT"]}');

\$_SERVER["HTTP_HOST"] = \${"\\xC2\\x80"}["host"];
\$_SERVER["SERVER_NAME"] = \${"\\xC2\\x80"}["host"];
\$_SERVER["DOCUMENT_ROOT"] = '{$PATH["DOCUMENT_ROOT"]}';
\$_SERVER["SCRIPT_FILENAME"] = '{$PATH["DOCUMENT_ROOT"]}'.\${"\\xC2\\x80"}["script"];
\$_SERVER["REQUEST_METHOD"] = 'GET';
\$_SERVER["QUERY_STRING"] = ''; // abcd=xyz&qwerty=123456
\$_SERVER["PHP_SELF"] = \${"\\xC2\\x80"}["script"];
\$_SERVER["SCRIPT_NAME"] = \${"\\xC2\\x80"}["script"];
\$_SERVER["REQUEST_URI"] = \${"\\xC2\\x80"}["script"];

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		$return = Project::setup_file(PATH["source"], $contents);
		if(!$return) {
			trigger_error("Can't setup 'source", E_USER_WARNING);
			return(false);
		}
		
		$contents = 
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
break /var/www/html/index.php:3
run
break del 0
ev var_dump($_SERVER);
step
#readline
continue
quit

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$return = Project::setup_file(PATH["commands"], $contents);
		if(!$return) {
			trigger_error("Can't setup 'commands'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function purge()
	{//{{{//
		
		$return = Project::purge_file(PATH["source"], 2);
		if(!$return) {
			trigger_error("Can't purge 'source'", E_USER_WARNING);
			return(false);
		}
		
		$return = Project::purge_file(PATH["commands"], 2);
		if(!$return) {
			trigger_error("Can't purge 'commands'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function get_u80_data(string $string)
	{//{{{//
		
		$length = strlen($string);
		$u80 = false;
		$buffer = '';
		$text = '';
		$data = [];
		for($offset = 0; $offset < $length; $offset += 1) {
			
			$char = substr($string, $offset, 1);
			
			if($char == "\x02") {
				$u80 = true;
				$buffer = '';
				continue;
			}
			if($char == "\x03") {
				$u80 = false;
				array_push($data, $buffer);
				continue;
			}
		
			if($u80) {
				$buffer .= $char;
			}
			else {
				$text .= $char;
			}
						
		}// for($offset = 0; $offset < $length; $offset += 1)
		
		$ARRAY = [];
		foreach($data as $json) {
			$return = decode($json);
			if(!is_array($return)) {
				trigger_error("Can't decode json from u80 data", E_USER_WARNING);
				return(false);
			}
			array_push($ARRAY, $return);
		}
		
		$result = [
			"data" => $ARRAY,
			"text" => $text,
		];
		
		return($result);
		
	}//}}}//

	static function u80_data_to_html(array $data, string $file = NULL, int $from = NULL, int $to = NULL)
	{//{{{//
	
		$filter = $file;
		
		$http_header_to_html = function(array $item)
		{//{{{//
			
			$_ = [
				"text" => t2h($item["string"]),
			];
			
			$html = 
///////////////////////////////////////////////////////////////
<<<HEREDOC
{$_["text"]}<br />

HEREDOC;
///////////////////////////////////////////////////////////////
			
			return($html);
			
		};//}}}//
		
		$code_coverage_to_html = function(string $file, array $LINE)
		{//{{{//
			
			$href = URL["source_viewer"].'?path='.urlencode($file);
			$_ = [
				"text" => htmlentities($file),
			];
			$html = 
///////////////////////////////////////////////////////////////
<<<HEREDOC
<a href="{$href}">{$_["text"]}</a><br />

HEREDOC;
///////////////////////////////////////////////////////////////
			foreach($LINE as $number => $flag) {
				if(!eval(Check::$int.='$number')) return(false);
				$number = strval($number);
				$_ = [
					"href" => "{$href}#".$number,
					"text" => $number,					
				];
				$html .= 
///////////////////////////////////////////////////////////////
<<<HEREDOC
<a href="{$_['href']}">{$_["text"]}</a>

HEREDOC;
///////////////////////////////////////////////////////////////
			}
			
			return($html.'<br />');
			
		};//}}}//
		
		$headers = '';
		$code_coverage = '';
		foreach($data as $item) {
			$type = $item["type"];
			
			if($type == 'http_header') {
				$headers .= $http_header_to_html($item);
				continue;
			}
			
			if($type == 'code_coverage') {
				$ITEM = $item["array"];
				foreach($ITEM as $file => $LINE) {
					if(
						$filter !== NULL
						&& $filter != $file
					) continue;
					
					if(
						$from !== NULL
						&& $to !== NULL
					) {
						$result = [];
						foreach($LINE as $number => $flag) {
							if($number >= $from && $number <= $to) {
								$result[$number] = $flag;
							}
						}
						$LINE = $result;
					}
					
					$code_coverage .= $code_coverage_to_html($file, $LINE);
				}
				continue;
			}
			
		}// foreach($u80 as $item)
		
		$result = [
			"headers" => $headers,
			"code_coverage" => $code_coverage,
		];
		
		return($result);
		
	}//}}}//
	
	static function output_to_html(string $output)
	{//{{{//
		
		$string = $output;
		$output = '';
		$strlen = strlen($string);
		for($offset = 0; $offset < $strlen; $offset += 1) {
			$char = substr($string, $offset, 1);
			$ord = ord($char);
			if(($ord >= 0x20 && $ord <= 0x7E) || $ord == 0x0A || $ord == 0x09) {
				$output .= $char;
			}
			else {
				$output .= sprintf('%s%X', '%', $ord);
			}
		}
		$output = htmlentities($output, true);
		
		$STRING = explode("\n", $output);
		$text = '';
		foreach($STRING as $string) {
			$string = trim($string);
			
			$pattern = '/(.+)\s+at\s+(.+)\:([0-9]+)$/';
			$return = preg_match($pattern, $string, $MATCH);
			if($return == 1) {
				$_ = [
					"href" => URL["source_viewer"]."?path={$MATCH[2]}#{$MATCH[3]}",
				];
				$string = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$MATCH[1]} at <a href="{$_['href']}" class="viewer">{$MATCH[2]}:{$MATCH[3]}</a><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$text .= $string;
				continue;
			}
			
			$pattern = '/(.+)\s+at\s+(.+)\:([0-9]+)\,\s+hits(.+)$/';
			$return = preg_match($pattern, $string, $MATCH);
			if($return == 1) {
				$_ = [
					"href" => URL["source_viewer"]."?path={$MATCH[2]}#{$MATCH[3]}",
				];
				$string = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$MATCH[1]} at <a href="{$_['href']}" class="viewer">{$MATCH[2]}:{$MATCH[3]}</a>, hits{$MATCH[4]}<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
				$text .= $string;
				continue;
			}
			
			$string = preg_replace("/ /", '&nbsp;', $string);
			$string = preg_replace("/	/", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $string);
			$text .= "{$string}<br />\n";
		}
		$output = $text;
		
		return($output);
		
	}//}}}//
	
	static function cli_debugger()
	{//{{{//
		
		$return = file_get_contents(PATH["commands"]);
		if(!is_string($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['PATH["commands"]' => PATH["commands"]]);
			trigger_error("Can't get contents of 'commands' file", E_USER_WARNING);
			return(false);
		}
		$COMMAND = explode("\n", $return);
		
		$debugger = new PHPDebugger(PATH["source"], PATH["cms"], NULL, 10, false);
		
		foreach($COMMAND as $command) {
			$command = trim($command);
			
			if($command == '#readline') {
				while(true) {
					echo("\n");
					$command = readline('> ');
					$command = trim($command);
					
					if($command == 'exit') return(true);
					
					$output = $debugger->send($command);
					echo($output);
					
					if(strpos($output, '[Script ended normally]') !== false) return(true);
					if($command == 'quit' || $command == 'q') return(true);
				}
			}
			
			if(substr($command, 0, 1) == '#') continue;
			$output = "> {$command}\n";
			
			$return = $debugger->send($command);
			$output .= $return;
			
			echo($output);
			
			if(strpos($return, '[Script ended normally]') !== false) break;
			if($command == 'quit' || $command == 'q') break;
		}
		
		return(true);
		
	}//}}}//
}

