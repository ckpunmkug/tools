<?php

// loader --help
/*{{{

Description: HTTP Loader (3arpy3ka)

Parameters: 

  -h  --help
	Show help text and exit

  -U  --url  <URL>
	Make http request to URL

  -M  --method  <http_method>
	GET, POST, HEAD or other HTTP method

  -H  --request-headers  <path_to_file>
	Load http request headers from file

  -B  --request-body  <path_to_file>
	Load http request body from file

  -P  --http-proxy  <IP:port>
	Transmit data via http(s) proxy

  -L  --memory-limit  <number_of_megabytes>
	Set memory limit for script, default 4

  -T  --timeout  <seconds>
	Set timeout in seconds, default 10

  -R  --redirect  <number>
	Number of redirects to follow, default 0

  -A  --user-agent  <user_agent_string>
	Set http header User-Agent

  -O  --head
	Output with response headers

  -J  --json
	Output result in json

  -v  --verbose
	Allow verbose messages to stderr

  -q  --quiet
	Prevent output to stdout

  -d  --debug
	Run in debug mode

}}}*/

class Loader
{
		/*
		'user_agent' => escapeshellarg('Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:135.0) Gecko/20100101 Firefox/135.0'),
		];
		$command = '/usr/local/bin/loader'
			." --url {$_['url']}"
			." --method GET"
			." --http-proxy 127.0.0.1:8118"
			." --user-agent {$_['user_agent']}"
			." --redirect 3"
			." --head"
			." --json"
		;
		*/
}

