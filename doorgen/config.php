<?php 

/// Технические настройки

define('DEBUG', true); // Вывод отладочных сообщений
define('VERBOSE', true); // Вывод дополнительных сообщений 
define('QUIET', false); // Не выводить сообщения об ошибках 

/// Базовые настройки сайта

define('CONFIG', [
	"predictions_per_page" => 7,
	"seo_friendly_urls" => true,
]);

/// Параметры для подключения к базе данных

define('DB', [
	"host" => 'localhost',
	"user" => 'door',
	"password" => '',
	"database" => 'door',
]);

