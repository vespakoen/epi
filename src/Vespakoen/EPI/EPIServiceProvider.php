<?php namespace Vespakoen\EPI;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class EPIServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('vespakoen/epi');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		Route::resource('epi/product', 'Vespakoen\EPI\Example\Controllers\ProductController');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}