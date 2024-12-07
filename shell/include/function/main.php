<?php

function main(callable $handle_get_request, callable $handle_post_request)
{//{{{

	$request_method = @strval($_SERVER["REQUEST_METHOD"]);
	switch($request_method) {
		case('GET'):
			$return = $handle_get_request();
			if($return !== true) {
				trigger_error("Handle get request failed", E_USER_ERROR);
			}
			exit(0);
		case('POST'):
			$return = $handle_post_request();
			if($return !== true) {
				trigger_error("Handle post request failed", E_USER_ERROR);
			}
			exit(0);
		default:
			trigger_error("Unsupported http request method", E_USER_ERROR);
	}

}//}}}

