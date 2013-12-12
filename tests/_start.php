<?php

use Illuminate\Container\Container;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model as Eloquent;

use Vespakoen\Epi\EpiServiceProvider;

class User extends Eloquent {

	public function roles()
	{
		return $this->belongsToMany('Role');
	}

	public function tags()
	{
		return $this->morphMany('Tag', 'taggable');
	}

	public function friend()
	{
		return $this->hasOne('User', 'friend_id');
	}

	public function friends()
	{
		return $this->hasMany('User', 'friend_id');
	}

}

class Role extends Eloquent {
}

class Tag extends Eloquent {
}

class EpiTests extends PHPUnit_Framework_TestCase {

	public $app;

	public function setup()
	{
		parent::setup();

		$app = $this->app = new Container();
		$app->bindIf('files', 'Illuminate\Filesystem\Filesystem');
		$app->singleton('config', function($app) {
			$fileLoader = new FileLoader($app['files'], __DIR__.'/../');

			return new Repository($fileLoader, 'config');
		});
		// $app->bind('config', function()
		// {
		// 	return array(
		// 		'epi::epi' => array(
		// 			'keys' => array(
		// 				'filter' => 'filter',
		// 				'sort' => 'sort',
		// 				'skip' => 'skip',
		// 				'take' => 'take'
		// 			)
		// 		),
		// 		'database' => array(
		// 			'fetch' => PDO::FETCH_CLASS,

		// 			/*
		// 			|--------------------------------------------------------------------------
		// 			| Default Database Connection Name
		// 			|--------------------------------------------------------------------------
		// 			|
		// 			| Here you may specify which of the database connections below you wish
		// 			| to use as your default connection for all database work. Of course
		// 			| you may use many connections at once using the Database library.
		// 			|
		// 			*/

		// 			'default' => 'mysql',


		// 			|--------------------------------------------------------------------------
		// 			| Database Connections
		// 			|--------------------------------------------------------------------------
		// 			|
		// 			| Here are each of the database connections setup for your application.
		// 			| Of course, examples of configuring each database platform that is
		// 			| supported by Laravel is shown below to make development simple.
		// 			|
		// 			|
		// 			| All database work in Laravel is done through the PHP PDO facilities
		// 			| so make sure you have the driver for your particular database of
		// 			| choice installed on your machine before you begin development.
		// 			|


		// 			'connections' => array(

		// 				'sqlite' => array(
		// 					'driver'   => 'sqlite',
		// 					'database' => __DIR__.'/../database/production.sqlite',
		// 					'prefix'   => '',
		// 				),

		// 				'mysql' => array(
		// 					'driver'    => 'mysql',
		// 					'host'      => 'localhost',
		// 					'database'  => 'vespakoen_dev',
		// 					'username'  => 'vespakoen_dev',
		// 					'password'  => 'vespakoen_dev',
		// 					'charset'   => 'utf8',
		// 					'collation' => 'utf8_unicode_ci',
		// 					'prefix'    => '',
		// 				),

		// 				'pgsql' => array(
		// 					'driver'   => 'pgsql',
		// 					'host'     => 'localhost',
		// 					'database' => 'database',
		// 					'username' => 'root',
		// 					'password' => '',
		// 					'charset'  => 'utf8',
		// 					'prefix'   => '',
		// 					'schema'   => 'public',
		// 				),

		// 				'sqlsrv' => array(
		// 					'driver'   => 'sqlsrv',
		// 					'host'     => 'localhost',
		// 					'database' => 'database',
		// 					'username' => 'root',
		// 					'password' => '',
		// 					'prefix'   => '',
		// 				),

		// 			),

		// 		)
		// 	);
		// });

		$serviceProvider = new EpiServiceProvider($app);
		$serviceProvider->register();

		$app['epi::epi']->make(new User, array());
	}

}
