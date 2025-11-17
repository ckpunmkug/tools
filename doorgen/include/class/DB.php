<?php 

class DB
{
	static $mysqli = NULL;
	
	static function open(string $host, string $user, string $password, string $database)
	{//{{{
		$mysqli = &DB::$mysqli;

		try {
			$mysqli = @new mysqli($host, $user, $password, $database);
			$errno = mysqli_connect_errno();
			if($errno !== 0) {
				trigger_error("Can't connect to database because: {$mysqli->connect_error}", E_USER_WARNING);
				return(false);
			}
		} 
		catch(Exception $Exception) {
			if(defined('DEBUG') && DEBUG) var_dump([
				'$host' => $host
				,'$user' => $user
				,'$password' => $password
				,'$database' => $database
			]);
			$message = $Exception->getMessage();
			trigger_error("Can't connect to database because: {$message}", E_USER_WARNING);
			return(false);
		}

		try {
			$return = $mysqli->set_charset("utf8");
			$errno = mysqli_connect_errno();
			if($errno !== 0) {
				trigger_error("Can't set database client character because: {$mysqli->connect_error}", E_USER_WARNING);
				return(false);
			}
		}
		catch(Exception $Exception) {
			$message = $Exception->getMessage();
			trigger_error("Can't set database client character because: {$message}", E_USER_WARNING);
			return(false);
		}

		$mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, TRUE);

		register_shutdown_function(function () {
			if(is_object(DB::$mysqli) && isset(DB::$mysqli->server_info)) {
				$return = DB::$mysqli->close();
				if(!$return) {
					trigger_error("Can't close database connection", E_USER_WARNING);
					return(false);
				}
				return(true);
			}
			return(NULL);
		} );
		
		return(true);
	}//}}}
	
	function __construct()
	{//{{{
		$mysqli = &DB::$mysqli;
		if (!is_object($mysqli)) {
			throw new Exception("Connection to database is not open");
		}
		return(NULL);
	}//}}}

	function query(string $sql)
	{//{{{
		$mysqli = &DB::$mysqli;
		
		$mysqli_result = $mysqli->query($sql);
		if ($mysqli_result === false) {
			trigger_error($mysqli->error, E_USER_WARNING);
			return(false);
		}
		
		if($mysqli_result === true) {
			return(true);
		}
		
		$result = [];
		while(true) {
			$row = $mysqli_result->fetch_assoc();
			if($row === NULL) break;
			array_push($result, $row);
		}
		$mysqli_result->free();
		
		return($result);
	}//}}}

	function id()
	{//{{{
		$id = DB::$mysqli->insert_id;
		return($id);
	}//}}}

	function queries(string $sql)
	{//{{{
		$mysqli = &DB::$mysqli;
		
		$mysqli_result = $mysqli->multi_query($sql);
		if ($mysqli_result === false) {
			trigger_error($mysqli->error, E_USER_WARNING);
			return(false);
		}
		
		while($mysqli->more_results()) {
			$mysqli_result = $mysqli->next_result();
			if ($mysqli_result === false) {
				trigger_error($mysqli->error, E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
	}//}}}
	
	function escape(string $variable)
	{//{{{
		$variable = preg_replace("/([^\r\n\t\x20-\xFF])/", '', $variable);
		$string = DB::$mysqli->real_escape_string($variable);
		return($string);
	}//}}}

	function name(string $name)
	{//{{{
		$name = addcslashes($name, "`\\");
		return($name);
	}//}}}
	
	function int($variable)
	{//{{{
		$number = intval($variable, 10);
		$number = strval($number);
		return($number);
	}//}}}

	function float($variable)
	{//{{{
		$number = floatval($variable);
		$number = strval($number);
		return($number);
	}//}}}

	function encode($variable)
	{//{{{
		$json = json_encode($variable, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if(!is_string($json)) {
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			return(false);
		}
		
		$string = $this->escape($json);
		return($string);
	}//}}}

	function decode(string $json)
	{//{{{
		$variable = json_decode($json, true);
		$error = json_last_error();
		if($variable === NULL && $error !== JSON_ERROR_NONE) {
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			return(false);
		}
		
		return($variable);
	}//}}}
	
}

