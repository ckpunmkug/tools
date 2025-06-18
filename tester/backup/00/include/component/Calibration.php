<?php

class Calibration
{
	static function main()
	{//{{{//
		
		HTML::$styles = [
			'/share/style/main.css',
			'/share/profile/VNC.css',
			'/share/component/Calibration/style.css',
		];
		
		HTML::$scripts = [
			'/share/component/Calibration/script.js',
		];
		
		$html = file_get_contents(SHARE_DIR.'/component/Calibration/index.html');
		if(!is_string($html)) {
			trigger_error("Can't get content from 'Calibration' index file", E_USER_WARNING);
			return(false);
		}
		HTML::$body = $html;
		
		return(true);
		
	}//}}}//
}

