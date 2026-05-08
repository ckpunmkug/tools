<?php

class Layout
{
	static $SEARCH_QUERY = [];
	
	static function patterns_tree(array $SEARCH_QUERY)
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
			<a href="{$_['href']}">{$_["text"]}</a>
		</span>
{$_["children"]}
	</li>
</ul>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		return($ul);
		
	}//}}}//
}
