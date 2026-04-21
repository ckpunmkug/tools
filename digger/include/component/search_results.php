<?php

class search_results
{
	static $URL = [
		"preg_search" => URL_PATH.'?component=preg_search',
		"search_results" => URL_PATH.'?component=search_results',
		"source_viewer" => URL_PATH.'?component=source_viewer',
		"test_source" => URL_PATH.'?component=test_source',
	];
	
	static $style = [
		"index" => 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
.status {
	padding: 0px 4px;
	font-family: Monospace;
	border: 1px solid darkgray;
	border-radius: 3px;
}

HEREDOC,
///////////////////////////////////////////////////////////////}}}//
	];
	
	static function page_index()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["search_query_id"]')) return(false);
		$query = intval($_GET["search_query_id"]);
		
		$SEARCH_RESULT = data::$get["SEARCH_RESULT"]($query);
		if(!is_array($SEARCH_RESULT)) {
			trigger_error("Can't get 'SEARCH_RESULT'", E_USER_WARNING);
			return(false);
		}
		
		$list = '';
		$counter = 0;
		foreach($SEARCH_RESULT as $search_result) {
			
			$index = strval($counter);
			$id = strval($search_result["id"]);
			$tabindex = strval($counter+1);
			$href = 
				self::$URL["source_viewer"]
				.'&path='.urlencode($search_result["file"])
				.'#'.strval($search_result["number"]);
			$text = t2h($search_result["line"]);
			
			$test_source = data::$get["test_source"]($search_result["id"]);
			if($test_source === NULL) {
				$test_source = ["status" => ' '];
			}
			if(!is_array($test_source)) {
				trigger_error("Can't get 'test_source'", E_USER_WARNING);
				return(false);
			}
			$_ = [
				"href" => self::$URL["test_source"].'&search_result_id='.strval($search_result["id"]),
				"status" => t2h($test_source["status"]),
			];
			
			$list .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="{$_['href']}" class="status"><code>{$_["status"]}</code></a>
<label>
	<input name="ID[{$index}]" value="{$id}" type="checkbox" tabindex="{$tabindex}" />
	<a href="{$href}" target="_blank"><code>{$text}</code></a>
</label>
<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			$counter += 1;
			
		}// foreach($SEARCH_RESULT as $search_result)
		
		$search_query_id = strval($query);
		$form_contents = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="search_query_id" value="$search_query_id" type="hidden" />
<div id="container">
	<div id="buttons">
		<button name="action" value="delete" type="submit" accesskey="d"><u>D</u>elete</button>
	</div>
	<div id="list">
{$list}
	</div>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$form = layout_form(self::$URL["search_results"], $form_contents);
		
		HTML::$title = 'search results';
		HTML::$style .= self::$style["index"];
		HTML::$body .= $form;
		HTML::echo();
		
		return(true);
		
	}//}}}//
	
	static function action_delete()
	{//{{{//
		
		if(!eval(Check::$string.='$_POST["search_query_id"]')) return(false);
		$search_query_id = intval($_POST["search_query_id"]);
		
		if(!eval(Check::$array.='$_POST["ID"]')) return(false);
		$ID = $_POST["ID"];
		
		foreach($ID as $key => $id) {
			if(!eval(Check::$string.='$id')) return(false);
			$ID[$key] = intval($id);
		}
		
		$return = data::$delete["search_results"]($ID);
		if(!$return) {
			trigger_error("Can't delete 'search_results'", E_USER_WARNING);
			return(false);
		}
		
		header("Location: ".self::$URL["search_results"].'&search_query_id='.strval($search_query_id));
		
		return(true);
		
	}//}}}//
}

