<?php namespace Vespakoen\Epi;

use Vespakoen\Epi\Epi;
use Vespakoen\Epi\Extractors\FilterExtractor;
use Vespakoen\Epi\Extractors\SorterExtractor;
use Vespakoen\Epi\Extractors\LimiterExtractor;
use Vespakoen\Epi\Extractors\JoinExtractor;
use Vespakoen\Epi\Manipulators\Filter;
use Vespakoen\Epi\Manipulators\Sorter;
use Vespakoen\Epi\Manipulators\Limiter;
use Vespakoen\Epi\Manipulators\Join;

use Illuminate\Support\ServiceProvider;

class EpiServiceProvider extends ServiceProvider {

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
		$this->registerExtractors();
		$this->registerEpi();
		$this->registerManipulators();
	}

	protected function registerExtractors()
	{
		$this->app->bind('epi::extractors.filter', function($app)
		{
			return new FilterExtractor($app['config']['epi::epi']);
		});

		$this->app->bind('epi::extractors.sorter', function($app)
		{
			return new SorterExtractor($app['config']['epi::epi']);
		});

		$this->app->bind('epi::extractors.limiter', function($app)
		{
			return new LimiterExtractor($app['config']['epi::epi']);
		});

		$this->app->bind('epi::extractors.join', function($app)
		{
			return new JoinExtractor($app['config']['epi::epi']);
		});
	}

	protected function registerManipulators()
	{
		$this->app->bind('epi::manipulators.filter', function($app)
		{
			return new Filter($app['epi::epi'], $app['config']['epi::epi']);
		});

		$this->app->bind('epi::manipulators.sorter', function($app)
		{
			return new Sorter($app['epi::epi'], $app['config']['epi::epi']);
		});

		$this->app->bind('epi::manipulators.limiter', function($app)
		{
			return new Limiter($app['epi::epi'], $app['config']['epi::epi']);
		});

		$this->app->bind('epi::manipulators.join', function($app)
		{
			return new Join($app['epi::epi'], $app['config']['epi::epi']);
		});
	}

	protected function registerEpi()
	{
		$this->app->singleton('epi::epi', function($app)
		{
			return new Epi($app['epi::extractors.filter'], $app['epi::extractors.sorter'], $app['epi::extractors.limiter'], $app['epi::extractors.join']);
		});
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
