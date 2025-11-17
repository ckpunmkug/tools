<?php 

class HTML
{
	static $language = "";
	
	static $meta = "";
	
	static $canonical = "";
	static $robots = "";
	
	static $title = "";
	static $description = "";
	static $keywords = "";
	
	static $favicon = "";
	
	static $styles = [];
	static $style = "";
	
	static $scripts = [];
	static $script = "";
	
	static $body = "";
	
	function __construct()
	{//{{{
		ob_start(function($buffer) {
			$buffer = htmlentities($buffer);
			$buffer = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
	<head>
	<body>
		<pre>{$buffer}</pre>
	</body>
</html>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
			return($buffer);
		});
	}//}}}
	
	function __wakeup()
	{//{{{
		trigger_error("Can't unserialize this class", E_USER_ERROR);
		exit(255);
	}//}}}
	
	function __destruct()
	{//{{{
		$buffer = ob_get_contents();
		ob_end_clean();
		$buffer = htmlentities($buffer);
		
		if(!empty($buffer)) {
			HTML::$body = "<pre>{$buffer}</pre>".HTML::$body;
		}
		
		$html = HTML::generate_html();
		
		if(@boolval(CONFIG["seo_friendly_urls"]) == true) {
			$html = create_seo_friendly_urls($html);
		}
		
		echo($html);
	}//}}}
	
	static function generate_stylesheets(array $styles)
	{//{{{
		$result = "";
		foreach($styles as $style) {
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
	
	static function generate_scripts(array $scripts)
	{//{{{
		$result = "";
		foreach($scripts as $script) {
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

	static function generate_html()
	{//{{{
		$language = self::$language;
		
		$meta = self::$meta;
		
		$canonical = self::$canonical;
		$robots = self::$robots;
		
		$title = self::$title;
		$description = self::$description;
		$keywords = self::$keywords;
		
		$favicon = self::$favicon;
		
		$stylesheets = self::generate_stylesheets(self::$styles);
		$style = self::$style;
		
		$scripts = self::generate_scripts(self::$scripts);
		$script = self::$script;
		
		$body = self::$body;
		
		$html = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<!DOCTYPE html>
<html lang="{$language}">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
{$meta}

<!--		<link rel="canonical" href="{$canonical}"> -->
		<meta name="robots" content="{$robots}">
		
		<title>{$title}</title>
		<meta name="description" content="{$description}">
		<meta name="keywords" content="{$keywords}">
		
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon}">
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
		return($html);
	}//}}}
	
}

