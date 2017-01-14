<?php

/**
* Returns configuration array 
*/
return [
	/*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    */
	'db' => [
		'driver' => env('DB_DRIVER', 'mysql'),
		'host' => env('DB_HOST', 'localhost'),
		'port' => env('DB_PORT', 3306),
		'database' => env('DB_DATABASE', 'jsondb'),
		'username' => env('DB_USERNAME', 'jsondb'),
		'password' => env('DB_PASSWORD', 'password'),
		'charset' => env('DB_CHARSET', 'utf8'),
		'collation' => env('DB_COLLATION', 'utf8_unicode_ci')
	]
];