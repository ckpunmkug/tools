<?php

if(!true) // simple test GET POST HEAD
{//{{{//
	
	echo('TEKCT');
	
}//}}}//

if(!true) // redirect testing
{//{{{//
	
	if(@is_string($_GET["cd"])) {
		$cd = intval($_GET["cd"]);
		$str = "CountDown = {$cd}\n";
		$cd -= 1;
		if($cd > 0) {
			http_response_code(301);
			header("Location: /index.php?cd={$cd}");
		}
		echo($str);
	}
	
}//}}}//

if(!true) // timeout test
{//{{{//
	
	sleep(20);
	echo("TEKCT\n");
	
}//}}}//

if(!true) // test memory limit
{//{{{//

	$text = '';
	$size = 0x100000;
	for($index = 0; $index < $size; $index += 1) $text .= ' ';
	echo($text);
		
}//}}}//

if(!true) // user agent test
{//{{{//

	$string = @strval($_SERVER['HTTP_USER_AGENT']);
	echo("{$string}\n");
	
}//}}}//

if(!true) // request headers test
{//{{{//
	
	var_dump(apache_request_headers());
	
}//}}}//

if(!true) // POST data test
{//{{{//
	
	$return = file_get_contents('php://input');
	var_dump($return);
	
}//}}}//

