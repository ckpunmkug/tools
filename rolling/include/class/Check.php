<?php

// Usage
/*{{{

function main()
{
	$variable = false;
	if(!eval(Check::$bool.='$variable')) return(false);
	$variable = 0;
	if(!eval(Check::$int.='$variable')) return(false);
	$variable = 0.0;
	if(!eval(Check::$float.='$variable')) return(false);
	$variable = '';
	if(!eval(Check::$string.='$variable')) return(false);
	$variable = [];
	if(!eval(Check::$array.='$variable')) return(false);
	return(true);
}
if(!main()) {
	trigger_error("Main call returned an error", E_USER_ERROR);
	exit(255);
}

}}}*/

Check::$bool = Check::B;
Check::$int = Check::I;
Check::$float = Check::F;
Check::$string = Check::S;
Check::$array = Check::A;

class Check
{//{{{
	static $b = false;
	static $i = 0;
	static $f = 0.0;
	static $s = '';
	static $a = [];
	static $r = NULL;
	
	static $bool = '';
	const B =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
Check::$a = explode("\n", Check::$bool);
Check::$s = array_pop(Check::$a);
Check::$s = substr(Check::$s, 2);
Check::$bool = Check::B;
Check::$r = eval('return(isset('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not set', E_USER_WARNING);
	return(false);
}
Check::$r = eval('return(is_bool('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not bool', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	static $int = '';
	const I =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
Check::$a = explode("\n", Check::$int);
Check::$s = array_pop(Check::$a);
Check::$s = substr(Check::$s, 2);
Check::$int = Check::I;
Check::$r = eval('return(isset('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not set', E_USER_WARNING);
	return(false);
}
Check::$r = eval('return(is_int('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not int', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	static $float = '';
	const F =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
Check::$a = explode("\n", Check::$float);
Check::$s = array_pop(Check::$a);
Check::$s = substr(Check::$s, 2);
Check::$float = Check::F;
Check::$r = eval('return(isset('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not set', E_USER_WARNING);
	return(false);
}
Check::$r = eval('return(is_float('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not float', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	static $string = '';
	const S =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
Check::$a = explode("\n", Check::$string);
Check::$s = array_pop(Check::$a);
Check::$s = substr(Check::$s, 2);
Check::$string = Check::S;
Check::$r = eval('return(isset('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not set', E_USER_WARNING);
	return(false);
}
Check::$r = eval('return(is_string('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not string', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	static $array = '';
	const A =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
Check::$a = explode("\n", Check::$array);
Check::$s = array_pop(Check::$a);
Check::$s = substr(Check::$s, 2);
Check::$array = Check::A;
Check::$r = eval('return(isset('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not set', E_USER_WARNING);
	return(false);
}
Check::$r = eval('return(is_array('.Check::$s.'));');
if(!Check::$r) {
	trigger_error(Check::$s.' is not array', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

	static function file_readable(string $path)
	{//{{{//
		
		$return = realpath($path);
		if(!is_string($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['path' => $path]);
			trigger_error("Can't get real path for passed path", E_USER_WARNING);
			return(false);
		}
		$path = $return;
		
		if(!file_exists($path)) {
			if (defined('DEBUG') && DEBUG) var_dump(['path' => $path]);
			trigger_error("File not exists in given path", E_USER_WARNING);
			return(false);
		}
		
		if(!is_file($path)) {
			if (defined('DEBUG') && DEBUG) var_dump(['path' => $path]);
			trigger_error("File is not regular in given path", E_USER_WARNING);
			return(false);
		}
		
		if(!is_readable($path)) {
			if (defined('DEBUG') && DEBUG) var_dump(['path' => $path]);
			trigger_error("File is not readable in given path", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

}//}}}

