<?php
$SERVER = var_export($_SERVER, true);
$request_headers = var_export(apache_request_headers(), true);
file_put_contents("php://stderr", "\n\n{$SERVER}\n{$request_headers}");
