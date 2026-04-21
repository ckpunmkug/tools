<?php 

header("Content-Security-Policy: frame-ancestors 'self';");

ob_start(function($ob) {
	$ob = htmlentities($ob);
	return(<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
	<head>
	<style>
body {
	background: white;
	color: black;
}
	</style>
	<body>
		<pre>{$ob}</pre>
	</body>
</html>
HEREDOC);
});

class HTML
{
	static $head = "";
	static $title = "";
	
	static $styles = [];
	static $style = "";
	
	static $scripts = [];
	static $script = "";
	
	static $body = "";
	
	static function stylesheets()
	{//{{{
		
		$result = "";
		foreach(self::$styles as $style) {
			if(!is_string($style)) continue;	
			$result .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<link rel="stylesheet" href="{$style}" />

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}
		return($result);
		
	}//}}}
	
	static function scripts()
	{//{{{
	
		$result = "";
		foreach(self::$scripts as $script) {
			if(!is_string($script)) continue;
			$result .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<script src="{$script}"></script>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}
		return($result);
		
	}//}}}

	static function echo()
	{//{{{
	
		$ob = ob_get_clean();
		$ob = htmlentities($ob);
		if(strlen($ob) > 0) {
			self::$body = "<pre>{$ob}</pre>\n".self::$body;
		}
	
		$head = self::$head;
		$title = self::$title;
		
		$stylesheets = self::stylesheets();
		$style = self::$style;
		
		$scripts = self::scripts();
		$script = self::$script;
		
		$body = self::$body;
		
		$html = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
{$head}
		<title>{$title}</title>
{$stylesheets}
		<style>
{$style}
		</style>
{$scripts}
		<script>
{$script}
		</script>
	</head>
	<body>
{$body}
	</body>
</html>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		echo($html);
		
		return(true);
		
	}//}}}
}

