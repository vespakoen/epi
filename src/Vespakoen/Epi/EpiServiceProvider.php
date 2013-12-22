<?php namespace Vespakoen\Epi;

use Vespakoen\Epi\Epi;

use Vespakoen\Epi\Collections\ExtractorCollection;

use Vespakoen\Epi\Extractors\FilterExtractor;
use Vespakoen\Epi\Extractors\MorphFilterExtractor;
use Vespakoen\Epi\Extractors\SorterExtractor;
use Vespakoen\Epi\Extractors\LimiterExtractor;
use Vespakoen\Epi\Extractors\JoinExtractor;
use Vespakoen\Epi\Extractors\ScopeExtractor;
use Vespakoen\Epi\Extractors\EagerloadExtractor;

use Vespakoen\Epi\Manipulators\Filter;
use Vespakoen\Epi\Manipulators\Sorter;
use Vespakoen\Epi\Manipulators\Limiter;
use Vespakoen\Epi\Manipulators\Join;
use Vespakoen\Epi\Manipulators\Scope;
use Vespakoen\Epi\Manipulators\Eagerload;

use Vespakoen\Epi\Relations\HasOne;
use Vespakoen\Epi\Relations\HasMany;
use Vespakoen\Epi\Relations\BelongsTo;
use Vespakoen\Epi\Relations\BelongsToMany;
use Vespakoen\Epi\Relations\MorphOne;
use Vespakoen\Epi\Relations\MorphMany;

use Vespakoen\Epi\Helpers\RelationUnifier;
use Vespakoen\Epi\Helpers\SafeTableName;

use Vespakoen\Epi\Formats\Json;

use Vespakoen\Epi\Commands\GenerateEpiControllerCommand;

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
		$this->registerHelpers();
		$this->registerExtractors();
		$this->registerExtractorCollection();
		$this->registerRelations();
		$this->registerManipulators();
		$this->registerFormats();
		$this->registerCommands();
		$this->registerEpi();
	}

	protected function registerExtractors()
	{
		$this->app->bind('epi::extractors.filter', function($app)
		{
			return new FilterExtractor($app);
		});

		$this->app->bind('epi::extractors.morphfilter', function($app)
		{
			return new MorphFilterExtractor($app);
		});

		$this->app->bind('epi::extractors.sorter', function($app)
		{
			return new SorterExtractor($app);
		});

		$this->app->bind('epi::extractors.limiter', function($app)
		{
			return new LimiterExtractor($app);
		});

		$this->app->bind('epi::extractors.join', function($app)
		{
			return new JoinExtractor($app);
		});

		$this->app->bind('epi::extractors.scope', function($app)
		{
			return new ScopeExtractor($app);
		});

		$this->app->bind('epi::extractors.eagerload', function($app)
		{
			return new EagerloadExtractor($app);
		});
	}

	protected function registerExtractorCollection()
	{
		$this->app->bind('epi::extractors', function($app)
		{
			$extractors = array(
				'filters' => $app->make('epi::extractors.filter'),
				'morphfilters' => $app->make('epi::extractors.morphfilter'),
				'sorters' => $app->make('epi::extractors.sorter'),
				'limiters' => $app->make('epi::extractors.limiter'),
				'joins' => $app->make('epi::extractors.join'),
				'scopes' => $app->make('epi::extractors.scope'),
				'eagerloads' => $app->make('epi::extractors.eagerload'),
			);

			return new ExtractorCollection($extractors);
		});
	}

	protected function registerManipulators()
	{
		$this->app->bind('epi::manipulators.filter', function($app)
		{
			return new Filter($app);
		});

		$this->app->bind('epi::manipulators.join', function($app)
		{
			return new Join($app);
		});

		$this->app->bind('epi::manipulators.limiter', function($app)
		{
			return new Limiter($app);
		});

		$this->app->bind('epi::manipulators.sorter', function($app)
		{
			return new Sorter($app);
		});

		$this->app->bind('epi::manipulators.scope', function($app)
		{
			return new Scope($app);
		});

		$this->app->bind('epi::manipulators.eagerload', function($app)
		{
			return new Eagerload($app);
		});
	}

	protected function registerRelations()
	{
		$this->app->bind('epi::relations.belongsto', function($app)
		{
			return new BelongsTo($app);
		});

		$this->app->bind('epi::relations.belongstomany', function($app)
		{
			return new BelongsToMany($app);
		});

		$this->app->bind('epi::relations.hasone', function($app)
		{
			return new HasOne($app);
		});

		$this->app->bind('epi::relations.hasmany', function($app)
		{
			return new HasMany($app);
		});

		$this->app->bind('epi::relations.morphone', function($app)
		{
			return new MorphOne($app);
		});

		$this->app->bind('epi::relations.morphmany', function($app)
		{
			return new MorphMany($app);
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
			return new SafeTableName($app);
		});
	}

	protected function registerFormats()
	{
		$this->app->bind('epi::formats.json', function($app)
		{
			return new Json($app);
		});
	}

	protected function registerEpi()
	{
		$this->app->singleton('epi::epi', function($app)
		{
			return new Epi($app['epi::extractors']);
		});
	}

	protected function registerCommands()
	{
		$this->app->bind('epi::command.generate.epicontroller', function($app)
		{
			return new GenerateEpiControllerCommand;
		});

		$this->commands(
			'epi::command.generate.epicontroller'
		);
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
