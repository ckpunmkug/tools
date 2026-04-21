<?php

class preg_search
{
	static $parent_query = 0;
	
	static $URL = [
		"preg_search" => URL_PATH.'?component=preg_search',
		"search_results" => URL_PATH.'?component=search_results',
	];
	
	static $SEARCH_QUERY = [];
	static $tabindex = 1;
	
	static function layout_tree(int $search_query_id, string $search_query_pattern, string $results_href)
	{//{{{//
		
		self::$tabindex += 1;
		$tabindex = strval(self::$tabindex);
		
		$children = '';
		foreach(self::$SEARCH_QUERY as $search_query) {
			if($search_query["parent"] != $search_query_id) continue;
			$href = self::$URL["search_results"].'&search_query_id='.strval($search_query["id"]);
			$children .= self::layout_tree($search_query["id"], $search_query["pattern"], $href);
		}
		
		$search_query_id = strval($search_query_id);
		$checked = '';
		if($search_query_id == self::$parent_query) {
			$checked = 'checked';
		}
		$search_query_pattern = t2h($search_query_pattern);
		
		$li = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<li>
	<label>
		<input name="id" value="{$search_query_id}" type="radio" tabindex="{$tabindex}" {$checked} />
		<a href="{$results_href}">{$search_query_pattern}</a>
	</label>
	<ul>{$children}</ul>
</li>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
		return($li);
		
	}//}}}//
	
	static function page_index()
	{//{{{//
		
		self::$SEARCH_QUERY = data::$get["SEARCH_QUERY"]();
		if(!is_array(self::$SEARCH_QUERY)) {
			trigger_error("Can't get 'SEARCH_QUERY'", E_USER_WARNING);
			return(false);
		}
		
		$tree = self::layout_tree(0, '/root/', '');
		
		$form_contents = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div id="container">
	<div id="input">
		<u>P</u>attern 
		<input name="pattern" value="//" type="text" size="48" tabindex="1" autofocus accesskey="p" />
		<button name="action" value="search" type="submit" accesskey="s"><u>S</u>earch</button>
		<button name="action" value="delete" type="submit" accesskey="d"><u>D</u>elete</button>
	</div>
	<div id="tree">
		<ul>{$tree}</ul>
	</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$form = layout_form(self::$URL["preg_search"], $form_contents);
		
		HTML::$title = 'preg search';
		HTML::$body .= $form;
		HTML::echo();
		
		return(true);
		
	}//}}}//
	
	static function action_search()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["pattern"]')) return(false);
		$pattern = $_POST["pattern"];
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$parent = intval($_POST["id"]);
		
		$PHP_FILE = data::$get["PHP_FILE"]();
		if(!is_array($PHP_FILE)) {
			trigger_error("Can't get 'PHP_FILE'", E_USER_WARNING);
			return(false);
		}
		
		$SEARCH_RESULT = [];
		foreach($PHP_FILE as $file)
		{//{{{//
		
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
			
		}//}}}//
		
		$query = data::$add["search_query"]($parent, $pattern);
		if(!is_int($query)) {
			trigger_error("Can't add 'search_query'", E_USER_WARNING);
			return(false);
		}
		
		$return = data::$add["SEARCH_RESULT"]($query, $SEARCH_RESULT);
		if(!$return) {
			trigger_error("Can't add 'SEARCH_RESULT'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: ".self::$URL["search_results"].'&search_query_id='.strval($query));
		
		return(true);
		
	}//}}}//
	
	static function action_delete()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["id"]')) return(false);
		$id = intval($_POST["id"]);
		
		$return = data::$delete["search_query"]($id);
		if(!$return) {
			trigger_error("Can't delete 'search_query'", E_USER_WARNING);
			return(false);
		}
		
		header('Location: '.self::$URL["preg_search"]);
		
		return(true);
		
	}//}}}//
}

