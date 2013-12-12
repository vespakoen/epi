<?php

use Illuminate\Container\Container;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model as Eloquent;

use Orchestra\Testbench\TestCase;

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

class EpiTests extends TestCase {

	protected function getPackageProviders()
	{
	    return array('Vespakoen\Epi\EpiServiceProvider');
	}

	public function setUp()
	{
		parent::setUp();

		$this->app['epi::epi']->make(new User, array());
	}

}
