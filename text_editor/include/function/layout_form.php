<?php

function layout_form(
	string $src,
	string $contents,
	string $form_additional_parameters = ''
) {
	$src = htmlentities($src);

	if(!defined('CSRF_TOKEN')) {
		trigger_error("CSRF_TOKEN not defined", E_USER_ERROR);
		exit(255);
	}
	$csrf_token = htmlentities(CSRF_TOKEN);
	
	$form_additional_parameters = trim($form_additional_parameters);
	
	$html = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<form action="{$src}" method="post" enctype="multipart/form-data" {$form_additional_parameters}>
	<input name="csrf_token" value={$csrf_token} type="hidden" />
{$contents}
</form>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

	return($html);
}

