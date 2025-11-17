<?php 

/// Технические настройки

define('DEBUG', true); // Вывод отладочных сообщений
define('VERBOSE', true); // Вывод дополнительных сообщений 
define('QUIET', false); // Не выводить сообщения об ошибках 

/// Базовые настройки сайта

define('CONFIG', [
	"cpservm" => [
		"ClientId" => '',
		"ClientSecret" => '',
		"ref" => '',
	],
	"anthropic" => [
		"url" => 'https://api.anthropic.com/v1/messages',
		"api_key" => '',
	],
]);

/// Параметры для подключения к базе данных

define('DB', [
	"host" => 'localhost',
	"user" => 'controller',
	"password" => '',
	"database" => 'controller',
]);

