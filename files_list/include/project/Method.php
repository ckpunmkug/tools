<?php

class Method
{
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
}

