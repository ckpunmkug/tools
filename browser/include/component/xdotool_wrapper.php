<?php

class xdotool_wrapper
{

/// Main
//{{{
	
	static function notice(string $text, bool $donthide = false)
	{//{{{//
			
			$command = notify." -u low -t 5000 ".escapeshellarg($text);
			if($donthide) $command = notify." -u critical ".escapeshellarg($text);
			exec($command);
		
	}//}}}//
	
	static $button_inscription = HOME.'/.cache/'.VENDOR.'/'.PROJECT.'/button_inscription.png';
	static $TMP_IMG = ['', ''];
	
	static $window_id = '';
	
	static function main()
	{//{{{//
	
		if(VERBOSE && DEBUG) var_dump('main');
		
		register_shutdown_function('xdotool_wrapper::shutdown');
		
		$c = count(self::$TMP_IMG);
		for($i = 0; $i < $c; $i += 1) {	
			$return = self::create_tmp_img(self::$TMP_IMG[$i]);
			if(!$return) {
				trigger_error("Can't create temporary png file", E_USER_WARNING);
				return(false);
			}
		}
		
		if(VERBOSE && DEBUG) var_dump(['TMP_IMG' => self::$TMP_IMG]);
		
		self::notice("xdotool_wrapper: Started");
		
		$command = xdotool.' getwindowfocus -f';
		$string = exec($command);
		if(preg_match('/^([0-9]+)$/', $string, $MATCH) != 1) {
			trigger_error("Can't get 'window id' using 'xdotool'", E_USER_WARNING);
			return(false);
		}
		self::$window_id = $MATCH[1];
		
		if(VERBOSE && DEBUG) var_dump(['window_id' => self::$window_id]);
		
		while(true) {
			usleep(100000);
		}
		
	}//}}}//

	static function shutdown()
	{//{{{//
		
		if(VERBOSE && DEBUG) var_dump('shutdown');
		
		$c = count(self::$TMP_IMG);
		for($i = 0; $i < $c; $i += 1) {	
			$return = unlink(self::$TMP_IMG[$i]);
			if(!$return) {
				trigger_error("Can't unlink temporary png file", E_USER_WARNING);
				return(false);
			}
		}
		
	}//}}}//
	
	static function create_tmp_img(string &$tmp_img)
	{//{{{//
	
		$temp_dir = sys_get_temp_dir();
		if(!is_string($temp_dir)) {
			trigger_error("Can't get system temporary dir", E_USER_WARNING);
			return(false);
		}
		
		$tempnam = tempnam($temp_dir, '');
		if(!is_string($tempnam)) {
			trigger_error("Can't create temporary file", E_USER_WARNING);
			return(false);
		}
		
		$name = substr($tempnam, strlen("{$temp_dir}/"));
		
		$oldname = $tempnam;
		$newname = "{$temp_dir}/{$name}.png";
		$return = rename($oldname, $newname);
		if(!$return) {
			trigger_error("Can't rename file", E_USER_WARNING);
			return(false);
		}
		
		$tmp_img = $newname;
		return(true);
		
	}//}}}//
	
//}}}

/// Preparation
//{{{
	
	static function preparation()
	{//{{{//
	
		$check = function() {
			if(!defined('INPUT_FILE')) {
				trigger_error("Path to input file not passed from command line", E_USER_ERROR);
				exit(255);
			}
			if(!defined('OUTPUT_FILE')) {
				trigger_error("Path to output file not passed from command line", E_USER_ERROR);
				exit(255);
			}
			return(true);
		};
	
		if(defined('ADD_GRID') && $check()) {
			$return = self::add_grid(INPUT_FILE, OUTPUT_FILE);
			if(!$return) {
				trigger_error("Can't add grid over image", E_USER_WARNING);
				return(false);
			}
			exit(0);
		}
	
		if(defined('CUT_RECTANGLE') && $check()) {
			$return = self::cut_rectangle(INPUT_FILE, OUTPUT_FILE, XDOTOOL_WRAPPER["button_inscription"]);
			if(!$return) {
				trigger_error("Can't cut rectangle from image", E_USER_WARNING);
				return(false);
			}
			exit(0);
		}
		
		return(true);
		
	}//}}}//
	
	static function add_grid(string $input_file, string $output_file)
	{//{{{//
		
		$size = getimagesize($input_file);
		if(!is_array($size)) {
			trigger_error("Can't get input image size", E_USER_WARNING);
			return(false);
		}
		
		$image = imagecreatefrompng($input_file);
		if(!is_object($image)) {
			trigger_error("'GD' can't create image from 'png'", E_USER_WARNING);
			return(false);
		}
		
		$red = imagecolorallocate($image, 0xFF, 0x00, 0x00);
		
		for($x = 0; $x < $size[0]; $x += 50) {
			imageline($image, $x, 0, $x, $size[1], $red);
			if($x%300 == 0) {
				imageline($image, $x-1, 0, $x-1, $size[1], $red);
				imageline($image, $x+1, 0, $x+1, $size[1], $red);
			}
		}
		
		for($y = 0; $y < $size[1]; $y += 50) {
			imageline($image, 0, $y, $size[0], $y, $red);
			if($y%300 == 0) {
				imageline($image, 0, $y-1, $size[0], $y-1, $red);
				imageline($image, 0, $y+1, $size[0], $y+1, $red);
			}
		}
		
		$return = imagepng($image, $output_file);
		if(!$return) {
			trigger_error("Can't save image with grid to file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
	static function cut_rectangle(string $input_file, string $output_file, array $rectangle_coords)
	{//{{{//
		
		$img_src = imagecreatefrompng($input_file);
		if(!is_object($img_src)) {
			trigger_error("'GD' can't create input image from 'png'", E_USER_WARNING);
			return(false);
		}
		
		$width = $rectangle_coords[2] - $rectangle_coords[0];
		$height = $rectangle_coords[3] - $rectangle_coords[1];
		$img_dst = imagecreate($width, $height);
		
		$x = $rectangle_coords[0];
		$y = $rectangle_coords[1];
		imagecopy($img_dst, $img_src, 0, 0, $x, $y, $width, $height);
		
		$return = imagepng($img_dst, $output_file);
		if(!$return) {
			trigger_error("Can't save output image", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
	
//}}}

	static function click()
	{//{{{//
		
		sleep(1);
		$command = xdotool.' key --window '.self::$window_id.' click 1';
		exec($command);
		
	}//}}}//

	static function mousemove(int $x, int $y)
	{//{{{//
		
		sleep(1);
		$command = xdotool.' mousemove --window '.self::$window_id." {$x} {$y}";
		exec($command);
		
	}//}}}//

	static function press(string $buttons) 
	{//{{{//
		
		sleep(1);
		$command = xdotool.' key --window '.self::$window_id." {$buttons}";
		exec($command);
		
	}//}}}//


	static $wait_load = XDOTOOL_WRAPPER["wait_load"]; // Wait after 'enter search query' and 'click more results'
	static $max_pages = XDOTOOL_WRAPPER["max_pages"]; // Maximum number of opening pages.

	static function continue_grabbing_proccess()
	{//{{{//
	
		if(defined('VERBOSE') && VERBOSE) {
			self::notice("xdotool_wrapper: Continue grabbing process");
		}
		
		while(true) {	
			while(true) {
				$text = @duckduckgo::get_next_query();
				if($text !== false) break;
				sleep(self::$wait_load);
			}
			
			if($text === NULL) {
				self::notice("xdotool_wrapper: Complete", true);
				exit(0);
			}
			
			$text = escapeshellarg($text);
			$command = "/usr/bin/echo {$text} | ".xsel." -i -b";
			system($command);
			
			self::paste_search_query();
			sleep(self::$wait_load);
			self::open_full_search_results_list();
			sleep(self::$wait_load);
		}
		
	}//}}}//

	static function paste_search_query()
	{//{{{//
	
		if(defined('VERBOSE') && VERBOSE) {
			self::notice("xdotool_wrapper: Paste search query");
		}
		
		$x = XDOTOOL_WRAPPER["query_input"]["x"];
		$y = XDOTOOL_WRAPPER["query_input"]["y"];
		
		self::press('Home');
		self::mousemove($x, $y);
		self::click();
		self::press('Ctrl+a');
		self::press('Delete');
		self::press('Shift+Insert');
		self::press('Return');
		
	}//}}}//

	static function open_full_search_results_list()
	{//{{{//
	
		if(defined('VERBOSE') && VERBOSE) {
			self::notice("xdotool_wrapper: Open full search results list - started");
		}
		
		$x = XDOTOOL_WRAPPER["more_button"]["x"];
		$y = XDOTOOL_WRAPPER["more_button"]["y"];
		
		$attempts = XDOTOOL_WRAPPER["attempts"];
		for($i = self::$max_pages; $i > 1; $i -= 1) {
			up:
			self::press('End');
			self::mousemove($x, $y);
			
			$return = self::is_more_results_button_present();
			if(!$return) {
				if(!($attempts > 0)) break;
				$attempts -= 1;
				
				sleep(self::$wait_load);
				goto up;
			}
			else $attempts = XDOTOOL_WRAPPER["attempts"];
		
			self::click();
			sleep(self::$wait_load);
		}
		
		self::press("Ctrl+Shift+1");
		
		self::notice("xdotool_wrapper: Open full search results list - stoped");
		
	}//}}}//
	
	static function is_more_results_button_present()
	{//{{{//
	
		$file = self::$TMP_IMG[0];
		$command = import." -window root {$file}";
		$return = 0;
		passthru($command, $return);
		if ($return != 0) {
			trigger_error(import." can't take screenshot", E_USER_WARNING);
			return(false);
		}
		
		$return = self::cut_rectangle(self::$TMP_IMG[0], self::$TMP_IMG[1], XDOTOOL_WRAPPER["button_inscription"]);
		if(!$return) {
			trigger_error("Can't cut rectangle from screenshot", E_USER_WARNING);
			return(false);
		}
		
		$sample = self::$button_inscription;
		$test = self::$TMP_IMG[1];
		$similarity_score = self::$TMP_IMG[0];
		
		$command = compare." -metric NCC {$sample} {$test} {$similarity_score}";
		$return = launch($command, 30);
		
		if(VERBOSE && DEBUG) var_dump(['launch' => $return]);
		
		$NCC = floatval($return["stderr"]);
		
		if($NCC < 0.5) return(false);
		return(true);
		
	}//}}}//
	
}

