<?php

class xdotool_wrapper
{
	static $notify = '/usr/bin/notify-send';
	static $xdotool = '/usr/bin/xdotool';
	static $import = '/usr/bin/import';
	static $window_id = '';
	static $grid_size = 50;
	
	static function preparator()
	{//{{{//
	
		if(defined('ADD_GRID')) {
			$return = self::add_grid();
			if(!$return) {
				trigger_error("Can't add grid over image", E_USER_WARNING);
				return(false);
			}
			exit(0);
		}
	
		if(defined('CUT_RECTANGLE')) {
			$return = self::cut_rectangle();
			if(!$return) {
				trigger_error("Can't cut rectangle from image", E_USER_WARNING);
				return(false);
			}
			exit(0);
		}
		
		return(true);
		
	}//}}}//

	static function add_grid()
	{//{{{//
		
		if(!defined('INPUT_FILE')) {
			trigger_error("Path to input file not passed from command line", E_USER_WARNING);
			return(false);
		}
		
		if(!defined('OUTPUT_FILE')) {
			trigger_error("Path to output file not passed from command line", E_USER_WARNING);
			return(false);
		}
		
		$size = getimagesize(INPUT_FILE);
		if(!is_array($size)) {
			trigger_error("Can't get input image size", E_USER_WARNING);
			return(false);
		}
		
		$image = imagecreatefrompng(INPUT_FILE);
		if(!is_object($image)) {
			trigger_error("'GD' can't create image from 'png'", E_USER_WARNING);
			return(false);
		}
		
		$red = imagecolorallocate($image, 0xFF, 0x00, 0x00);
		
		for($x = 0; $x < $size[0]; $x += self::$grid_size) {
			imageline($image, $x, 0, $x, $size[1], $red);
			if($x%300 == 0) {
				imageline($image, $x-1, 0, $x-1, $size[1], $red);
				imageline($image, $x+1, 0, $x+1, $size[1], $red);
			}
		}
		
		for($y = 0; $y < $size[1]; $y += self::$grid_size) {
			imageline($image, 0, $y, $size[0], $y, $red);
			if($y%300 == 0) {
				imageline($image, 0, $y-1, $size[0], $y-1, $red);
				imageline($image, 0, $y+1, $size[0], $y+1, $red);
			}
		}
		
		$return = imagepng($image, OUTPUT_FILE);
		if(!$return) {
			trigger_error("Can't save image with grid to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function cut_rectangle()
	{//{{{//
			
		if(!defined('INPUT_FILE')) {
			trigger_error("Path to input file not passed from command line", E_USER_WARNING);
			return(false);
		}
		
		if(!defined('OUTPUT_FILE')) {
			trigger_error("Path to output file not passed from command line", E_USER_WARNING);
			return(false);
		}
		
		$rectangle = XDOTOOL_WRAPPER["label_rectangle"];
		
		$img_src = imagecreatefrompng(INPUT_FILE);
		if(!is_object($img_src)) {
			trigger_error("'GD' can't create image from 'png'", E_USER_WARNING);
			return(false);
		}
		
		$width = $rectangle[2] - $rectangle[0];
		$height = $rectangle[3] - $rectangle[1];
		$img_dst = imagecreate($width, $height);
		
		$x = $rectangle[0];
		$y = $rectangle[1];
		imagecopy($img_dst, $img_src, 0, 0, $x, $y, $width, $height);
		
		$return = imagepng($img_dst, OUTPUT_FILE);
		if(!$return) {
			trigger_error("Can't save image with label text of button", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function get_and_put_label_rectangle(string $screenshot_file, string $label_rectangle_file)
	{//{{{//
		
		$rectangle = XDOTOOL_WRAPPER["label_rectangle"];
		
		$img_src = imagecreatefrompng($screenshot_file);
		if(!is_object($img_src)) {
			trigger_error("'GD' can't create image from 'png'", E_USER_WARNING);
			return(false);
		}
		
		$width = $rectangle[2] - $rectangle[0];
		$height = $rectangle[3] - $rectangle[1];
		$img_dst = imagecreate($width, $height);
		
		$x = $rectangle[0];
		$y = $rectangle[1];
		imagecopy($img_dst, $img_src, 0, 0, $x, $y, $width, $height);
		
		$return = imagepng($img_dst, $label_rectangle_file);
		if(!$return) {
			trigger_error("Can't save image with label text of button", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	
	static function main()
	{//{{{//
		
		if(!file_exists(self::$notify)) {
			trigger_error(self::$notify." not found. Install 'libnotify-bin' or change path", E_USER_ERROR);
			exit(255);
		}
		
		if(!file_exists(self::$xdotool)) {
			trigger_error(self::$xdotool." not found. Install 'xdotool' or change path", E_USER_ERROR);
			exit(255);
		}
		
		if(!file_exists(self::$import)) {
			trigger_error(self::$import." not found. Install 'imagemagick' or change path", E_USER_ERROR);
			exit(255);
		}
		
		$message = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
X do tool wrapper started
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		self::notice($message);
		//self::wait(5);
		
		$command = self::$xdotool.' getwindowfocus -f';
		$string = exec($command);
		if(preg_match('/^([0-9]+)$/', $string, $MATCH) != 1) {
			trigger_error("Can't get 'window id' using 'xdotool'", E_USER_WARNING);
			return(false);
		}
		self::$window_id = $MATCH[1];
		if (defined('DEBUG') && DEBUG) var_dump(['self::$window_id' => self::$window_id]);
		
		while(true) {
			usleep(100000);
		}
		
		return(true);
		
	}//}}}//
	
	static function notice(string $text)
	{//{{{//
		
			$command = self::$notify." -u low -t 5000 ".escapeshellarg($text);
			exec($command);
		
	}//}}}//
	
	static function error(string $text)
	{//{{{//
		
			$command = self::$notify." -u critical ".escapeshellarg($text);
			exec($command);
		
	}//}}}//
	
	static function wait(int $seconds)
	{//{{{//
		
		$cound_down = $seconds;
		while($cound_down > 0) {
			$command = self::$notify." -u low -t 950 'Continued in {$cound_down} seconds'";
			exec($command);
			$cound_down -= 1;
			sleep(1);
		}
		
	}//}}}//

	static function click(int $x, int $y)
	{//{{{//
		
		$command = self::$xdotool.' mousemove --window '.self::$window_id." {$x} {$y}";
		exec($command);
		$command = self::$xdotool.' key --window '.self::$window_id.' click 1';
		exec($command);
		sleep(1);
		
	}//}}}//

	static function press(string $buttons) 
	{//{{{//
		
		$command = self::$xdotool.' key --window '.self::$window_id." {$buttons}";
		exec($command);
		sleep(1);
		
	}//}}}//

	static function paste_search_query()
	{//{{{//
	
		if(defined('VERBOSE') && VERBOSE) {
			self::notice("Paste search query");
		}
		sleep(1);
		
		$x = XDOTOOL_WRAPPER["query_input"]["x"];
		$y = XDOTOOL_WRAPPER["query_input"]["y"];
		
		self::press('Home');
		self::click($x, $y);
		self::press('Ctrl+a');
		self::press('Delete');
		self::press('Shift+Insert');
		self::press('Return');
		
	}//}}}//
	
	static function click_more_button()
	{//{{{//
	
		if(defined('VERBOSE') && VERBOSE) {
			self::notice("Click 'More Results' button");
		}
		sleep(1);
		
		$x = XDOTOOL_WRAPPER["more_button"]["x"];
		$y = XDOTOOL_WRAPPER["more_button"]["y"];
		
		self::press('End');
		self::click($x, $y);
		
	}//}}}//

	static function open_full_search_results_list()
	{//{{{//
	
		if(defined('VERBOSE') && VERBOSE) {
			self::notice("X do tool wrapper try open full search results list");
		}
		sleep(1);
		
		$x = XDOTOOL_WRAPPER["more_button"]["x"];
		$y = XDOTOOL_WRAPPER["more_button"]["y"];
		
		self::press('End');
		$return = self::is_more_results_button_present();
		var_dump(['button_present' => $return]);
		termination_signal_handler();
		
	}//}}}//
	
	static function is_more_results_button_present()
	{//{{{//
	
		$file_name = HOME_DIR.'/.cache/'.VENDOR.'/'.PROJECT.'/label_rectangle.png';
		
		$tmp_dir = '/tmp/command0';
			
		$command = self::$import." -window root {$tmp_dir}/screenshot.png";
		$return = 0;
		passthru($command, $return);
		if ($return != 0) {
			trigger_error(self::import." can't take screenshot", E_USER_WARNING);
			return(false);
		}
		
		$return = self::get_and_put_label_rectangle("{$tmp_dir}/screenshot.png", "{$tmp_dir}/label_rectangle.png");
		if(!$return) {
			trigger_error("Can't get and put 'label_rectangle'", E_USER_WARNING);
			return(false);
		}
		
		// compare -subimage-search original.png enable.png similarity_score.png
		
		return(true);
		
	}//}}}//
	
}

