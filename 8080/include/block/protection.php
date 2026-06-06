<?php

if(!(
	isset($_SERVER["HTTP_SEC_FETCH_SITE"])
	&& is_string($_SERVER["HTTP_SEC_FETCH_SITE"])
	&& (
		$_SERVER["HTTP_SEC_FETCH_SITE"] == 'same-origin'
		|| $_SERVER["HTTP_SEC_FETCH_SITE"] == 'none'
	)
)) {
	http_response_code(403);
	trigger_error("Incorrect Sec-Fetch-Site header", E_USER_ERROR);
	exit(255);
}

