<?php

class php_cleaner
{
	/// Usage
	/* {{{
	
		$source_file_path = '/var/www/index.php';
		$source = file_get_contents($source_file_path);
		if(!is_string($source)) {
			trigger_error("Can't open source file", E_USER_ERROR);
			exit(255);
		}
		$source = php_cleaner::main($source);
		if(!is_string($source)) {
			trigger_error("Can't make clean php source", E_USER_WARNING);
			return(false);
		}
		echo($source);
	
	}}}*/
	
	static function main(string $source)
	{//{{{//
		
		$TOKEN = token_get_all($source);
		$TOKEN = self::strip_comments_and_whitespaces($TOKEN);

		$return = self::add_required_whitespaces($TOKEN);
		if(!is_array($return)) {
			trigger_error("Can't add required whitespaces", E_USER_WARNING);
			return(false);
		}
		$TOKEN = $return;
		$return = self::self_correct_check($source, $TOKEN);
		if(!$return) {
			trigger_error("Incorrect added required whitespaces", E_USER_WARNING);
			return(false);
		}

		$TOKEN = self::add_newlines($TOKEN);
		$return = self::self_correct_check($source, $TOKEN);
		if(!$return) {
			trigger_error("Incorrect addedv new lines", E_USER_WARNING);
			return(false);
		}

		$result = self::convert_tokens_to_source($TOKEN);
		
		return($result);
		
	}//}}}//
	
	static function strip_comments_and_whitespaces(array $TOKEN)
	{//{{{//
	
		$result = [];
		
		foreach($TOKEN as $token) {
		
			if(
				is_array($token)
				&& isset($token[0])
				&& is_int($token[0])
				&& (
					$token[0] == T_COMMENT
					|| $token[0] == T_DOC_COMMENT
					|| $token[0] == T_WHITESPACE
				)
			) continue;
			
			array_push($result, $token);
			
		}// foreach($TOKEN as $token)
		
		return($result);
		
	}//}}}//

	static function self_correct_check(string $source, array $TOKEN)
	{//{{{//
	
		$old = token_get_all($source);
		$old = self::strip_comments_and_whitespaces($old);
		
		$source = self::convert_tokens_to_source($TOKEN);
		$new = token_get_all($source);
		$new = self::strip_comments_and_whitespaces($new);
		
		if(count($old) != count($new)) {
			
			if(defined('DEBUG') && DEBUG) var_dump([
				'count($old)' => count($old),
				'count($new)' => count($new),
			]);
			
			if(defined('DEBUG') && DEBUG) {
			
				foreach($old as $index => $token_old) {
				
					$token_new = $new[$index];
					if(
						is_array($token_old)
						&& is_array($token_new)
						&& $token_old[0] == $token_new[0]
						&& trim($token_old[1]) == trim($token_new[1])
					) { 
						echo(
							token_name($token_old[0])." = ".trim($token_old[1])
							."\t".
							token_name($token_new[0])." = ".trim($token_new[1])
							."\n"
						);
						continue;
					}
					if(
						is_string($token_old)
						&& is_string($token_new)
						&& $token_old == $token_new
					) {
						echo($token_old."\t".$token_new."\n");
						continue;
					}
					
					$result = [];
					
					if(is_array($token_old)) {
						$result['$token_old'] = token_name($token_old[0])." = ".trim($token_old[1]).' : '.$token_old[2];
					}
					else {
						$result['$token_old'] = $token_old;
					}
					if(is_array($token_new)) {
						$result['$token_new'] = token_name($token_new[0])." = ".trim($token_new[1]);
					}
					else {
						$result['$token_new'] = $token_new;
					}
					
					var_dump($result);
					break;
					
				}// foreach($old as $index => $token_old)
				
			}// if(defined('DEBUG') && DEBUG)
			
			trigger_error("Count of tokens not equal", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//

	static function convert_tokens_to_source(array $TOKEN)
	{//{{{//
	
		$source = '';
		
		foreach($TOKEN as $token) {
		
			if (is_array($token)) {
				$source .= $token[1];
			} else {
				$source .= $token;
			}
			
		}// foreach ($TOKEN as $token)
		
		return($source);
		
	}//}}}//
	
	static function add_required_whitespaces(array $TOKEN)
	{//{{{//
	
		$result = [];

		$whitespace = [ T_WHITESPACE, " ", 0 ];
		$newline = [ T_WHITESPACE, "\n", 0 ];

		$no_whitespace = [
			T_STRING, T_LNUMBER, T_CONSTANT_ENCAPSED_STRING, T_DIR, T_FILE, T_LINE, T_FUNC_C, T_METHOD_C, T_DOUBLE_COLON, T_VARIABLE, T_VARIABLE, T_SL, T_DEC, T_INLINE_HTML, T_ENCAPSED_AND_WHITESPACE, T_CURLY_OPEN, T_INC, T_OBJECT_OPERATOR, T_DNUMBER, T_DOC_COMMENT, T_NUM_STRING, T_CLASS_C, T_NS_SEPARATOR, T_SR, T_DEFAULT, T_IF, T_WHILE, T_ARRAY, T_FOR, T_FOREACH, T_UNSET, T_EMPTY, T_SWITCH, T_ISSET, T_DEFAULT, T_LIST, T_EVAL, T_EXIT, T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, T_ENDIF, T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, T_ATTRIBUTE, T_NAME_FULLY_QUALIFIED, T_ENDFOREACH, T_DECLARE, T_NAME_QUALIFIED, T_ENDWHILE, T_ENDFOR, T_ENDSWITCH
			,'$', '~', '|', '&', '%', '"', "'", '{', '}', '/', '*', '+', '-', '!', ']', '[', '.', '^', '@', '(', ')', ';', ':', ','
		];

		$right_whitespace = [
			T_REQUIRE_ONCE, T_ELSE, T_CLASS, T_VAR, T_FUNCTION, T_GLOBAL, T_ECHO, T_DO, T_RETURN, T_ELSEIF, T_INCLUDE, T_NEW, T_INCLUDE_ONCE, T_CASE, T_PUBLIC, T_PRIVATE, T_STATIC, T_USE, T_PROTECTED, T_CONST, T_THROW, T_ABSTRACT, T_PRINT, T_REQUIRE, T_TRY, T_CATCH, T_CLONE, T_FINAL, T_NAMESPACE, T_ELLIPSIS, T_CALLABLE, T_CONTINUE, T_BREAK, T_GOTO, T_YIELD
		];

		$between_whitespaces = [
			T_IS_EQUAL, T_CONCAT_EQUAL, T_AS, T_COALESCE, T_LOGICAL_AND, T_LOGICAL_AND, T_LOGICAL_OR, T_IS_NOT_EQUAL, T_BOOLEAN_AND, T_DOUBLE_ARROW, T_IS_NOT_IDENTICAL, T_IS_GREATER_OR_EQUAL, T_IS_SMALLER_OR_EQUAL, T_IS_IDENTICAL, T_BOOLEAN_OR, T_PLUS_EQUAL, T_OR_EQUAL, T_INSTANCEOF, T_EXTENDS, T_MINUS_EQUAL, T_SR_EQUAL, T_AND_EQUAL, T_IMPLEMENTS, T_XOR_EQUAL, T_MOD_EQUAL, T_MUL_EQUAL, T_SL_EQUAL, T_INTERFACE, T_POW, T_FINALLY, T_LOGICAL_XOR, T_DIV_EQUAL
			,'?', '>', '<', '=',
		];

		$CAST = [
		T_STRING_CAST => '(string)', T_DOUBLE_CAST => '(float)', T_BOOL_CAST => '(bool)', T_ARRAY_CAST => '(array)', T_INT_CAST => '(int)', T_OBJECT_CAST => '(object)'
		];

		foreach($TOKEN as $token) {

			if(is_array($token)) 
				$name = $token[0];		
			else
				$name = $token;
				
			if(in_array($name, $no_whitespace)) {
				array_push($result, $token);
				continue;
			}
			if(in_array($name, $right_whitespace)) {
				array_push($result, $token, $whitespace);
				continue;
			}
			if(in_array($name, $between_whitespaces)) {
				array_push($result, $whitespace, $token, $whitespace);
				continue;
			}

			if($name == T_OPEN_TAG) {
				$token[1] = trim($token[1])." ";
				array_push($result, $token);
				continue;
			}
			if($name == T_CLOSE_TAG) {
				array_push($result, $whitespace,$token);
				continue;
			}
			
			if($name == T_START_HEREDOC) {
				$token[1] = rtrim($token[1])."\n";
				array_push($result, $newline, $token);
				continue;
			}
			if($name == T_END_HEREDOC) {
				array_push($result, $newline, $token, $newline);
				continue;
			}
			
			if(key_exists($name, $CAST)) {
				$token[1] = $CAST[$name];
				array_push($result, $token, $whitespace);
				continue;
			}
			
			if(is_int($name)) {
				if(defined('DEBUG') && DEBUG) var_dump([
					'$name' => $name,
					'token_name($name)' => @token_name($name),
				]);
			}
			else {
				if(defined('DEBUG') && DEBUG) var_dump(['$name' => $name]);
			}
			trigger_error("Unsupported token", E_USER_WARNING);
			return(false);
		}

		return($result);
		
	}//}}}//

	static function add_newlines(array $TOKEN)
	{//{{{//

		$result = [];
		
		$count = count($TOKEN);
		$tab = 0;
		$string = "\n";
		$flag = false;

		for($index = 0; $index < $count; $index++) {

			if(
				@$TOKEN[$index][0] == T_STRING_VARNAME ||
				@$TOKEN[$index][0] == T_CURLY_OPEN
			) {
				$flag = true;
				array_push($result, $TOKEN[$index]);
				continue;
			}

			if(@$TOKEN[$index] == '{') {
				$tab += 1;
				$string = "\n".str_repeat("\t", $tab);
				$whitespace = [T_WHITESPACE, $string, 0];
				array_push($result, $TOKEN[$index], $whitespace);
				
				continue;
			}
			
			if(@$TOKEN[$index] == '}') {
				if($flag) {
					$flag = false;
					array_push($result, $TOKEN[$index]);
					continue;
				}
				
				if(@$result[count($result)-1][0] == T_WHITESPACE) {
					array_pop($result);
				}
					
				$tab -= 1;
				$string = "\n".str_repeat("\t", $tab);
				$whitespace = [T_WHITESPACE, $string, 0];
				//array_push($result, $whitespace, $TOKEN[$index], $whitespace);
				array_push($result, $TOKEN[$index], $whitespace);

				continue;
			}
			
			if (
				@$TOKEN[$index] == ';' || 
				@$TOKEN[$index] == ':' || 
				@$TOKEN[$index][0] == T_OPEN_TAG
			){
				array_push($result, @$TOKEN[$index], [T_WHITESPACE, $string, 0]);
				continue;
			}
			
			array_push($result, @$TOKEN[$index]);
			
		}// for($index = 0; $index < $count; $index++)

		return($result);
		
	}//}}}//
}

