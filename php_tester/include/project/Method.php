<?php

class Method
{
	static function get_u80_data(string $string)
	{//{{{//
		
		$length = strlen($string);
		$data = [];
		$text = '';
		$u80 = false;
		$buffer = '';
		for($offset = 0; $offset < $length; $offset += 1) {
			
			$char = substr($string, $offset, 1);
			if($char != "\x80") {
				$buffer .= $char;
			}
			else {
				if($u80) {
					$u80 = false;
					array_push($data, $buffer);
				}
				else {
					$u80 = true;
					$text .= $buffer;
				}
				$buffer = '';
			}
			
		}// for($offset = 0; $offset < $length; $offset += 1)
		$text .= $buffer;
		
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

	static function u80_data_to_html(array $data)
	{//{{{//
		
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
		
		$code_coverage_to_html = function(array $item)
		{//{{{//
			
			$href = URL["source_viewer"].'?path='.urlencode($item["file"]);
			$_ = [
				"text" => t2h($item["file"]),
			];
			$html = 
///////////////////////////////////////////////////////////////
<<<HEREDOC
<a href="{$href}">{$_["text"]}</a><br />

HEREDOC;
///////////////////////////////////////////////////////////////
			foreach($item["lines"] as $number) {
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
				$code_coverage .= $code_coverage_to_html($item);
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
	
	static function debug_from_stdin(object $debugger)
	{//{{{//
		
		while(true) {
			echo("\n");
			$command = readline('> ');
			$command = trim($command);
			
			if($command == 'exit') return(true);
			
			$output = $debugger->send($command);
			echo($output);
			
			if(strpos($output, '[Script ended normally]') !== false) exit(0);
			if($command == 'quit' || $command == 'q') exit(0);
		}
		
	}//}}}//
}

