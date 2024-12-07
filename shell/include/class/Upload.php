<?php

class Upload
{
	
	static $style = '';
	static $script = '';
	
	static function page()
	{//{{{//
		HTML::$title = "Upload";
		
		array_unshift(HTML::$styles, 'share/style/bootstrap.css');
		
		HTML::$style .= self::$style;
		
		HTML::$script .= self::$script;
		
		$_ = [
			"url_path" => htmlentities(URL_PATH),
			"csrf_token" => htmlentities(CSRF_TOKEN),
			
			"post_max_size" => htmlentities(ini_get('post_max_size')),
			"upload_max_filesize" => htmlentities(ini_get('upload_max_filesize')),
			"max_file_uploads" => htmlentities(ini_get('max_file_uploads')),
		];
		
		if($_["post_max_size"] == '0') $_["post_max_size"] = '0 (unlimited)';
		
		HTML::$body .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC

<dialog>
	<form name="upload_files" action="{$_['url_path']}" method="post" enctype="multipart/form-data" target="input_output">
		<input name="csrf_token" value="{$_['csrf_token']}" type="hidden" />
		<fieldset>
			<span name="php_ini">
				post_max_size: {$_['post_max_size']}<br />
				upload_max_filesize: {$_['upload_max_filesize']}<br />
				max_file_uploads: {$_['max_file_uploads']}<br />
			</span><br />
			
			<label>Upload path</label><hr />
			<input name="path" value="/var/www/upload" type="text" /><br />
			<hr />
			
			<label name='select_files' for="files"
				><span name="select_files" class="glyphicon glyphicon-list-alt" aria-hidden="true"></span
			></label>
			<input id="files" name="files[]" type="file" multiple />
			Number of selected files <span name="files_number">0</span><br />
			<hr />
			
			<button name="action" value="upload" type="submit">Upload</button>
		</fieldset>
	</form>
</dialog>

<dialog name="input_output">
	<fieldset>
		<iframe name="input_output"></iframe><br />
		<hr />
		<button name="close_input_output">Close</button>
	</fieldset>
</dialog>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		return(true);
	}//}}}//
	
	static function action()
	{//{{{//

		$csrf_token = @strval($_POST["csrf_token"]);
		if(strcmp(CSRF_TOKEN, $csrf_token) !== 0) {
			trigger_error("Incorrect `csrf token`", E_USER_WARNING);
			return(false);
		}
		
		$return = self::move_uploaded_files();
		if(!is_int($return)) {
			trigger_error("Can't move uploaded files", E_USER_WARNING);
			return(false);
		}
		
		$_ = [
			"uploaded_files_count" => htmlentities($return),
		];
		
		HTML::$body .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC

Uploaded files count: {$_["uploaded_files_count"]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		return(true);
	}//}}}//

	static function move_uploaded_files()
	{//{{{//
		
		$return = self::get_parameters();
		if(!is_array($return)) {
			trigger_error("Can't get incoming parameters", E_USER_WARNING);
			return(false);
		}
		$FILE = $return;
		
		$result = 0;
		foreach($FILE as $file) {
			$return = move_uploaded_file($file[0], $file[1]);
			if(!$return) {
				if (defined('DEBUG') && DEBUG) var_dump([
					'source' => $file[0],
					'destination' => $file[1],
				]);
				trigger_error("Can't move uploaded file", E_USER_WARNING);
				continue;
			}
			$result += 1;
		}
		
		return($result);
		
	}//}}}//
	
	static function get_parameters()
	{//{{{//
		
		if(@is_string($_POST["path"]) != true) {
			trigger_error('`$_POST["path"]` is not string', E_USER_WARNING);
			return(false);
		}
		$path = $_POST["path"];
		
		if(!file_exists($path)) {
			trigger_error("`path` not exists", E_USER_WARNING);
			return(false);
		}
		
		if(!is_dir($path)) {
			trigger_error("`path` is not dir", E_USER_WARNING);
			return(false);
		}
		
		if(!is_writable($path)) {
			trigger_error("`path` is not writable", E_USER_WARNING);
			return(false);
		}
		
		$path = realpath($path);
		
		$result = [];
		
		foreach($_FILES["files"]["name"] as $index => $name) {
			$error = $_FILES["files"]["error"][$index];
			if($error !== 0) {
				if (defined('DEBUG') && DEBUG) var_dump(['$name' => $name]);
				trigger_error("Uploaded file have error", E_USER_WARNING);
				continue;
			}
			
			$tmp = $_FILES["files"]["tmp_name"][$index];
			array_push($result, [$tmp, $path.'/'.$name]);
		}
		
		return($result);
		
	}//}}}//
	
}

