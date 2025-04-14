<?php

function counter(int $current, int $length)
{//{{{//

	$time = time();
	if($time > $_SERVER['argc']) {
		echo("\r {$current} of {$length}\r");
		$_SERVER['argc'] = $time;
	}

}//}}}//

