<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Key configuration
	|--------------------------------------------------------------------------
	|
	| Your api takes arguments for filtering, sorting and limiting the results.
	| The keys that are looked for in the input are configurable below.
	|
	*/

	'keys' => array(
		'filter' => 'filter',
		'sort' => 'sort',
		'skip' => 'skip',
		'take' => 'take',
		'with' => 'with',
		'scopes' => 'scopes',
		'format' => 'format',
		'response' => 'response',
		'adaptor' => 'adaptor'
	),

	'format' => 'json',

	'adaptor' => false

);
