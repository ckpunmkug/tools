<?php

// Usage
/*
function main()
{
	$variable = false;
	if(!eval(C::$B.='$variable')) return(false);
	$variable = 0;
	if(!eval(C::$I.='$variable')) return(false);
	$variable = 0.0;
	if(!eval(C::$F.='$variable')) return(false);
	$variable = '';
	if(!eval(C::$S.='$variable')) return(false);
	$variable = [];
	if(!eval(C::$A.='$variable')) return(false);
	return(true);
}
if(!main()) {
	trigger_error("Main call returned an error", E_USER_ERROR);
	exit(255);
}
*/

C::$B = C::B;
C::$I = C::I;
C::$F = C::F;
C::$S = C::S;
C::$A = C::A;
class C
{
	static $b = false;
	static $i = 0;
	static $f = 0.0;
	static $s = '';
	static $a = [];
	static $r = NULL;
	
	static $B = '';
	const B =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
C::$a = explode("\n", C::$B);
C::$s = array_pop(C::$a);
C::$s = substr(C::$s, 2);
C::$B = C::B;
C::$r = eval('return(isset('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not set', E_USER_WARNING);
	return(false);
}
C::$r = eval('return(is_bool('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not bool', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	static $I = '';
	const I =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
C::$a = explode("\n", C::$I);
C::$s = array_pop(C::$a);
C::$s = substr(C::$s, 2);
C::$I = C::I;
C::$r = eval('return(isset('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not set', E_USER_WARNING);
	return(false);
}
C::$r = eval('return(is_int('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not int', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	static $F = '';
	const F =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
C::$a = explode("\n", C::$F);
C::$s = array_pop(C::$a);
C::$s = substr(C::$s, 2);
C::$F = C::F;
C::$r = eval('return(isset('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not set', E_USER_WARNING);
	return(false);
}
C::$r = eval('return(is_float('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not float', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	static $S = '';
	const S =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
C::$a = explode("\n", C::$S);
C::$s = array_pop(C::$a);
C::$s = substr(C::$s, 2);
C::$S = C::S;
C::$r = eval('return(isset('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not set', E_USER_WARNING);
	return(false);
}
C::$r = eval('return(is_string('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not string', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	static $A = '';
	const A =
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
C::$a = explode("\n", C::$A);
C::$s = array_pop(C::$a);
C::$s = substr(C::$s, 2);
C::$A = C::A;
C::$r = eval('return(isset('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not set', E_USER_WARNING);
	return(false);
}
C::$r = eval('return(is_array('.C::$s.'));');
if(!C::$r) {
	trigger_error(C::$s.' is not array', E_USER_WARNING);
	return(false);
}
return(true);
//
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
}

