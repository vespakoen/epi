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
use Vespakoen\Epi\Relations\HasMany;
use Vespakoen\Epi\Relations\HasOne;
use Vespakoen\Epi\Relations\BelongsToMany;
use Vespakoen\Epi\Helpers\RelationUnifier;
use Vespakoen\Epi\Helpers\SafeTableName;

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
		$this->registerRelations();
		$this->registerHelpers();
		$this->registerEpi();
	}

	protected function registerExtractors()
	{
		$this->app->bind('epi::extractors.filter', function($app)
		{
			return new FilterExtractor($app['epi::helpers.safetablename'], $app['config']['epi::epi']);
		});

		$this->app->bind('epi::extractors.sorter', function($app)
		{
			return new SorterExtractor($app['epi::helpers.safetablename'], $app['config']['epi::epi']);
		});

		$this->app->bind('epi::extractors.limiter', function($app)
		{
			return new LimiterExtractor($app['epi::helpers.safetablename'], $app['config']['epi::epi']);
		});

		$this->app->bind('epi::extractors.join', function($app)
		{
			return new JoinExtractor($app['epi::helpers.safetablename'], $app['epi::helpers.relationunifier']);
		});
	}

	protected function registerRelations()
	{
		$this->app->bind('epi::relations.belongsto', function($app)
		{
			return new BelongsTo;
		});

		$this->app->bind('epi::relations.belongstomany', function($app)
		{
			return new BelongsToMany;
		});

		$this->app->bind('epi::relations.hasmany', function($app)
		{
			return new HasMany;
		});

		$this->app->bind('epi::relations.hasone', function($app)
		{
			return new HasOne;
		});

		$this->app->bind('epi::relations.morphmany', function($app)
		{
			return new MorphOne;
		});
	}

	protected function registerHelpers()
	{
		$this->app->singleton('epi::helpers.relationunifier', function($app)
		{
			return new RelationUnifier($app);
		});

		$this->app->singleton('epi::helpers.safetablename', function($app)
		{
			return new SafeTableName($app['epi::helpers.relationunifier']);
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
