<?php
$output_file_name = __DIR__.'/webmap.phar';
$Phar = new Phar($output_file_name);

/// Add files to phar {{{

$file_name = __DIR__.'/src/include/main.php';
$Phar->addFile($file_name, 'main.php');

$file_name = __DIR__.'/src/include/Check.php';
$Phar->addFile($file_name, 'Check.php');

$file_name = __DIR__.'/src/include/Settings.php';
$Phar->addFile($file_name, 'Settings.php');

$file_name = __DIR__.'/src/include/Data.php';
$Phar->addFile($file_name, 'Data.php');

$file_name = __DIR__.'/src/include/State.php';
$Phar->addFile($file_name, 'State.php');

$file_name = __DIR__.'/src/include/Prepare.php';
$Phar->addFile($file_name, 'Prepare.php');

$file_name = __DIR__.'/src/include/Action.php';
$Phar->addFile($file_name, 'Action.php');

$file_name = __DIR__.'/src/include/Process.php';
$Phar->addFile($file_name, 'Process.php');

$file_name = __DIR__.'/src/include/Parser.php';
$Phar->addFile($file_name, 'Parser.php');

/// }}} Add files to phar

$string = 
<<<'HEREDOC'
#!/usr/bin/env php
<?php
/// include sources
if(true) {//{{{
	set_include_path('phar:///'.__FILE__);
	
	require_once('Check.php');
	require_once('Data.php');
	require_once('Settings.php');
	require_once('State.php');
	
	require_once('Prepare.php');
	require_once('Action.php');
        require_once('Process.php'); 
	require_once('Parser.php');
	
	require_once('main.php');
}//}}}

$return = main($argv);
if($return !== true) {
	trigger_error("Script executed with error", E_USER_ERROR);
	exit(255);
}
exit(0);
__HALT_COMPILER(); ?>
HEREDOC;
$Phar->setStub($string);

$return = chmod($output_file_name, 0755);
if(!$return) {
	trigger_error("Can't change mode to phar file", E_USER_ERROR);
	exit(255);
}
