<?php

class Method
{
	static function setup()
	{//{{{//
		
		$table = 'TEST_STATUS';
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}';

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$return = Database::query($sql);
		if(!is_array($return)) {
			trigger_error("Can't SELECT name FROM sqlite_master", E_USER_WARNING);
			return(false);
		}
		if(count($return) == 0) {
			$return = data::$create["TEST_STATUS"]();
			if(!$return) {
				trigger_error("Can't create 'TEST_STATUS'", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
		
	}//}}}//
	
	static $SEARCH_QUERY = [];
	
	static function create_status_tree()
	{//{{{//
		
		$return = data::$get["SEARCH_QUERY"]();
		if(!is_array($return)) {
			trigger_error("Can't get 'SEARCH_QUERY'", E_USER_WARNING);
			return(false);
		}
		$SEARCH_QUERY = $return;
		
		$result = [];
		foreach($SEARCH_QUERY as $search_query) {
			$return = data::$get["SEARCH_RESULT_ID"]($search_query["id"]);
			if(!is_array($return)) {
				trigger_error("Can't get 'SEARCH_RESULT'", E_USER_WARNING);
				return(false);
			}
			$SEARCH_RESULT_ID = $return;
			
			$return = data::$get["TEST_STATUS"]($search_query["id"]);
			if(!is_array($return)) {
				trigger_error("Can't get 'TEST_STATUS'", E_USER_WARNING);
				return(false);	
			}
			$TEST_STATUS = $return;
			
			$search_query["results"] = $SEARCH_RESULT_ID;
			$search_query["statuses"] = $TEST_STATUS;
			
			array_push($result, $search_query);
		}
		self::$SEARCH_QUERY = $result;
		
		$tree = self::recursive_generator_of_patterns_tree(0, '');
		return($tree);
		
	}//}}}//
	
	static function recursive_generator_of_patterns_tree(int $id, string $pattern)
	{//{{{//
		
		$current = [];
		$children = '';
		foreach(self::$SEARCH_QUERY as $search_query) {
			
			if($search_query["id"] == $id) {
				$current = $search_query;
				continue;
			}
			
			if($search_query["parent"] != $id) continue;
			
			$children .= self::recursive_generator_of_patterns_tree(
				$search_query["id"]
				,$search_query["pattern"]
			);
			
		}// foreach(self::$SEARCH_QUERY as $search_query)
		
		/// statuses ///////////////////////////////////////////////////
		
		$spans = '';
		if(count($current) == 0) goto label_patterns;
		
		foreach($current["results"] as $result) {
		
			if(array_key_exists($result, $current["statuses"])) {
				$_ = [
					"text" => htmlentities($current["statuses"][$result]),
					"title" => strval($result),
				];
			}
			else {
				$_ = [
					"text" => '0',
					"title" => strval($result),
				];
			}
			
			$spans .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<span class="status" title="{$_['title']}">{$_["text"]}</span>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			
		}// foreach($current["results"] as $result)
		
		label_patterns: ////////////////////////////////////////////////
		
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
		<span class="item">
			<span  class="pattern" title="{$_['id']}">{$_["text"]}</span>
{$spans}
		</span>
{$_["children"]}
	</li>
</ul>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		return($ul);
		
	}//}}}//
	
}

