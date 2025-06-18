<?php

class FileSystem
{
	static function get_PATH(string $path)
	{//{{{//
		
		$PATH = [];
		array_push($PATH, $path);
		
		for ($index = 0; $index < count($PATH); $index++) {
			
			$path = $PATH[$index];
			if(is_link($path) || !is_dir($path)) continue;
			
			$resource = opendir($path);
			if (!is_resource($resource)) continue;
			
			while (true) {
				$name = readdir($resource);
				if (!is_string($name)) break;
				
				if ($name != "." && $name != "..")
					array_push($PATH, "{$path}/{$name}");
			}
			
			closedir($resource);
		}
		
		return($PATH);
		
	}//}}}//
}

