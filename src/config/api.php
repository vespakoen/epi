<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Resource prefix
	|--------------------------------------------------------------------------
	|
	| This option tells the API class how to prefix resources.
	| Example: 'api/'
	|
	*/

	'prefix' => 'api/',

	/*
	|--------------------------------------------------------------------------
	| Resource domain
	|--------------------------------------------------------------------------
	|
	| This option tells the API class how to prefix the resource when
	| making an actual HTTP request
	|
	*/

	'domain' => 'http://localhost:8080/',

	/*
	|--------------------------------------------------------------------------
	| Local API
	|--------------------------------------------------------------------------
	|
	| If the API server resides on the same laravel installation, you can have
	| Laravel forward the API call instead of making an HTTP request.
	|
	*/

	'local' => false

);