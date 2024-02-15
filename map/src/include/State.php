<?php declare(ticks = 1);

function signal_handler(int $signal) {
	exit(255);
}
pcntl_signal(SIGINT, "signal_handler", false);

class State
{
	var $ITEM = NULL;
	var $item = NULL;
	var $counter = 0;
	
	function __construct(string $item_name)
	{//{{{
		$file_name = Data::$dir_name.'/'.Settings::$state_file_name;
		$return = file_exists($file_name);
		if(!$return) {
			$return = file_put_contents($file_name, '[]');
			if(!is_int($return)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$file_name' => $file_name]);
				trigger_error("Can't put empty array in json to file", E_USER_ERROR);
				exit(255);
			}
		}
		
		$return = Data::import(Settings::$state_file_name);
		if(!is_array($return)) {
			trigger_error("Can't import state file", E_USER_ERROR);
			exit(255);
		}
		$this->ITEM = $return;
		
		if(!isset($this->ITEM["$item_name"])) {
			$this->ITEM["{$item_name}"] = [
				"in" => NULL,
				"complete" => 0,
				"out" => NULL,
			];
			
		}
		
		$this->item = &$this->ITEM["$item_name"];
		return(NULL);
	}//}}}
	
	function __destruct()
	{//{{{
		$return = Data::export(Settings::$state_file_name, $this->ITEM);
		if(!$return) {
			trigger_error("Can't export state file", E_USER_ERROR);
		}
	}//}}}
	
	function next()
	{//{{{
		if($this->counter < $this->item["complete"]) {
			$this->counter += 1;
			return(true);
		}
		
		if(defined('VERBOSE') && VERBOSE) {
			$string = "\rComplete {$this->counter} of {$this->item['in']} ";
			file_put_contents("php://stderr", $string, FILE_APPEND);
		}
		
		return(false);
	}//}}}
	
	function complete()
	{//{{{
		$this->item["complete"] += 1;
		$this->counter += 1;
	}//}}}
	
}
