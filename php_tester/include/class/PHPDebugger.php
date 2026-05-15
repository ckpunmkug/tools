<?php

class PHPDebugger
{

// Usage
/*{{{

	$PHPDebugger = new PHPDebugger('/var/www/html/index.php', '/var/www/html', NULL, 10, true);

	$commands = 
<<<'HEREDOC'
break ZEND_EXIT
run
ev var_dump($_SERVER);
continue
quit
HEREDOC;
	$COMMAND = explode("\n", $commands);

	foreach($COMMAND as $command) {
		$PHPDebugger->send($command);
	}

}}}*/

// Excerpts from the pages of help.
/* {{{
	-c      -c/my/php.ini       Set php.ini file to load
	-n                          Disable default php.ini
	-q                          Suppress welcome banner
	-b                          Disable colour
	-i      -imy.init           Set .phpdbginit file
	-I                          Ignore default .phpdbginit
	-O      -Omy.oplog          Sets oplog output file
	-p      -p, -p=func, -p*    Output opcodes and quit
  
	set prompt abcd
	set quiet on
	set pagination off

	break my_function#14 - Break at the opline #14 of the function my_function
	break \my\class::method#2 - Break at the opline #2 of the method \my\class::method
	break test.php:#3 - Break at opline #3 in test.php
	break ZEND_ADD - Break on any occurrence of the opcode ZEND_ADD
			

	run       attempt execution
	continue  continue execution
}}} */

	var $phpdbg = '/usr/bin/phpdbg';
	
	var $process = NULL;
	var $pid = 0;
	var $PIPE = NULL;
	var $prompt = '';
	
	var $timeout = NULL;
	var $verbose = NULL;
	
	function __construct(
		string $file, // full path to php source file
		string $cwd, // current working directory
		array $env = NULL, // shell variables
		int $timeout = 30, // running script timeout
		bool $verbose = true // output stdout and stderr of phpdbg
	) {//{{{
		
		$this->timeout = $timeout;
		$this->verbose = $verbose;
		
		$return = file_exists($this->phpdbg);
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$this->phpdbg' => $this->phpdbg]);
			throw new Exception("File 'phpdbg' not exists");
		}
	
		$command = "{$this->phpdbg} -q -b -I {$file}";
		if($this->verbose) { 
			file_put_contents('php://stdout', "{$command}\n");
		}
		
		$descriptorspec = [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']];
		
		$return = proc_open($command, $descriptorspec, $this->PIPE, $cwd, $env);
		if(!is_resource($return)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
			throw new Exception("Can't open 'phpdbg' process");
		}
		$this->process = $return;
		
		$return = proc_get_status($this->process);
		if(!is_array($return)) {
			throw new Exception("Can't get 'phpdbg' process status");
		}
		$this->pid = $return['pid'];
		
		$this->prompt = uniqid();
		stream_set_blocking($this->PIPE[1], false);
		stream_set_blocking($this->PIPE[2], false);

		$return = $this->send("set prompt {$this->prompt}");
		if(!is_string($return)) {
			throw new Exception("can't set prompt");
		}
		
		$this->send('set quiet on');
		$this->send('set pagination off');
		
	}//}}}
	
	function __destruct()
	{//{{{
		if($this->process === NULL) return(false);
		
		fwrite($this->PIPE[0], "quit\n");
		
		fclose($this->PIPE[0]);
		fclose($this->PIPE[1]);
		fclose($this->PIPE[2]);
		
		$return = proc_close($this->process);
		$this->process = NULL;
	}//}}}

	function send(string $command)
	{//{{{
	
		if($this->process === NULL) {
			trigger_error("phpdbg is closed", E_USER_WARNING);
			return(false);
		}
	
		$command = trim($command);
		if(substr($command, 0, 1) == '#') return('');
		
		if($this->verbose) {
			file_put_contents('php://stdout', $command."\n");
		}
		
		if($command == 'quit' || $command == 'q') {
			$this->__destruct();
			return('');
		}
		
		$command .= "\n";
		fwrite($this->PIPE[0], $command);
		
		$timeout = time() + $this->timeout;
		
		$stdout = '';
		$buffer = '';
		
		while(true) {
			$time = time();
			if($time >= $timeout) {
				trigger_error("command execution timeout", E_USER_WARNING);
				$this->emergency_halt();
				return(false);
			}
			
			$buffer = stream_get_contents($this->PIPE[1]);
			if(!is_string($buffer)) {
				trigger_error("can't read process stdout", E_USER_WARNING);
				return(false);
			}
			if(strlen($buffer) == 0) {
				usleep(100000);
				continue;
			}
			$stdout .= $buffer;
			
			if($this->verbose) {
				file_put_contents('php://stdout', $buffer);
			}
			
			$pattern = '/'.$this->prompt.'/';
			$return = preg_match($pattern, $stdout);
			if($return == 1) break;
		}
		
		return($stdout);
	}//}}}
	
	function emergency_halt()
	{//{{{
		fclose($this->PIPE[0]);
		fclose($this->PIPE[1]);
		fclose($this->PIPE[2]);
		
		system("/usr/bin/pkill -TERM -P {$this->pid}");
		
		proc_close($this->process);
		$this->process = NULL;
	}//}}}

}

