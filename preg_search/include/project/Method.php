<?php

class Method
{
/// search query page //////////////////////////////////////////////////////////

	static $SEARCH_QUERY = [];
	
	static function create_patterns_tree(array $SEARCH_QUERY)
	{//{{{//
		
		self::$SEARCH_QUERY = $SEARCH_QUERY;
		$tree = self::recursive_generator_of_patterns_tree(0, '');
		return($tree);
		
	}//}}}//
	
	static function recursive_generator_of_patterns_tree(int $id, string $pattern)
	{//{{{//
		
		$children = '';
		foreach(self::$SEARCH_QUERY as $search_query) {
			
			if($search_query["parent"] != $id) continue;
			
			$children .= self::recursive_generator_of_patterns_tree(
				$search_query["id"]
				,$search_query["pattern"]
			);
			
		}// foreach(self::$SEARCH_QUERY as $search_query)
		
		$_ = [
			"id" => strval($id),
			"text" => t2h($pattern),
			"children" => $children,
		];
		
		$_["href"] = 
			URL_PATH
			.'?page=search_results'
			."&query={$_['id']}"
		;
		
		$_["accesskey"] = '';
		$_["checked"] = '';
		$_["root"] = '';
		if($id == 0) {
			$_["accesskey"] = ' accesskey="t"';
			$_["checked"] = ' checked';
			$_["root"] = ' id="root"';
		}
		
		$ul = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<ul{$_["root"]}>
	<li>
		<span class="pattern">
			<input name="id" value="{$_['id']}" type="radio"{$_["accesskey"]}{$_["checked"]} />
			<a href="{$_['href']}" class="text">{$_["text"]}</a>
		</span>
{$_["children"]}
	</li>
</ul>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		return($ul);
		
	}//}}}//

/// search action //////////////////////////////////////////////////////////////
	
	static function apply_path_filter(array $PATH, string $filter)
	{//{{{//
		
		$result = [];
		foreach($PATH as $path) {
			if(!is_file($path)) continue;
		
			$return = preg_match($filter, $path);
			if($return === false) {
				if(defined('DEBUG') && DEBUG) var_dump(['$filter' => $filter]);
				trigger_error("Can't perform regular expression", E_USER_WARNING);
				return(false);
			}
			
			if($return != 1) continue;
			array_push($result, $path);
		}
		
		return($result);
		
	}//}}}//
	
	static function find_lines(array $PATH, string $pattern)	
	{//{{{//
		
		$SEARCH_RESULT = [];
		foreach($PATH as $file) {
		
			$LINE = file($file);
			if(!is_array($LINE)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't get lines array from file", E_USER_WARNING);
				return(false);
			}
			
			foreach($LINE as $key => $value) {
			
				$line = trim($value);
				$number = $key + 1;
				
				$return = preg_match($pattern, $line);
				if($return === false) {
					if(defined('DEBUG') && DEBUG) var_dump(['$pattern' => $pattern]);
					trigger_error("Incorrect 'pattern'", E_USER_WARNING);
					return(false);
				}
				if($return != 1) continue;
				
				array_push($SEARCH_RESULT, [
					"file" => $file,
					"line" => $line,
					"number" => $number,
				]);
				
			}// foreach($LINE as $key => $value)
			
		}// foreach($PHP_FILE as $file)
		
		return($SEARCH_RESULT);
		
	}//}}}//
	
	static function find_lines_by_tokens(array $PATH, string $constant)
	{//{{{//
		
		$SEARCH_RESULT = [];
		foreach($PATH as $file) {
		
			$return = file_get_contents($file);
			if(!is_string($return)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't get php source file contents", E_USER_WARNING);
				return(false);
			}
			$php_source = $return;
			$LINE = explode("\n", $php_source);
			array_unshift($LINE, '');
			
			$return = token_get_all($php_source);
			if(!is_array($return)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file_path' => $file_path]);
				trigger_error("Can't get all tokens from php source", E_USER_WARNING);
				return(false);
			}
			$TOKEN = $return;
			
						
			foreach($TOKEN as $token) {
				if(!is_array($token)) continue;
				if(!(
					isset($token[0])
					&& $token[0] == constant($constant)
				)) continue;
				
				array_push($SEARCH_RESULT, [
					"file" => $file,
					"line" => trim($LINE[$token[2]]),
					"number" => $token[2],
				]);
				
			}// foreach($TOKEN as $token)
			
		}// foreach($PHP_FILE as $file)
					
		return($SEARCH_RESULT);
		
	}//}}}//

/// setup action ///////////////////////////////////////////////////////////////
	
	static function create_database(string $path)
	{//{{{//
		
		$return = Database::open($path);
		if(!$return) {
			trigger_error("Can't open database file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function create_tables()
	{//{{{//
		
		$return = data::$drop["SEARCH_QUERY"]();
		if(!$return) {
			trigger_error("Can't drop 'SEARCH_QUERY'", E_USER_WARNING);
			return(false);
		}
		
		$return = data::$create["SEARCH_QUERY"]();
		if(!$return) {
			trigger_error("Can't drop 'SEARCH_QUERY'", E_USER_WARNING);
			return(false);
		}
		
		$return = data::$drop["SEARCH_RESULTS"]();
		if(!$return) {
			trigger_error("Can't drop 'SEARCH_RESULTS'", E_USER_WARNING);
			return(false);
		}
		
		$return = data::$create["SEARCH_RESULTS"]();
		if(!$return) {
			trigger_error("Can't drop 'SEARCH_RESULTS'", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
}

