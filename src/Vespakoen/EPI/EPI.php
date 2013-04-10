<?php namespace Vespakoen\EPI;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

use Vespakoen\EPI\Relations\BelongsToMany;
use Vespakoen\EPI\Relations\HasOne;
use Vespakoen\EPI\Relations\HasMany;

class EPI {

	/**
	 * The relationships to eagerload
	 * 
	 * @var array
	 */
	public $eagerLoad = array();

	/**
	 * The columns to load
	 * 
	 * @var array
	 */
	private $select = array();

	/**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'<=', '>=', '<>', '!=', '=', '<', '>'
	);

	/**
	 * Create a new EPI instance
	 * 
	 * @param Illuminate\Database\Eloquent\Model $model The Eloquent model
	 */
	public function __construct($model)
	{
		$this->model = $model;
		$this->query = $this->model->newQuery();
	}

	/**
	 * Create a new EPI instance statically
	 * 
	 * @param string $model An Eloquent model's name (including namespace)
	 */
	public static function model($model)
	{
		return new static(new $model);
	}

	/**
	 * Create a new EPI instance statically
	 * 
	 * @param Illuminate\Database\Eloquent\Model $model An Eloquent model instance
	 */
	public static function modelInstance($model)
	{
		return new static($model);
	}

	/**
	 * Eagerload relationships
	 * 
	 * @param  array $relations The relation identifiers to load
	 * @return Vespakoen\EPI\EPI
	 */
	public function with($relations)
	{
		$this->eagerLoad = $relations;

		return $this;
	}

	/**
	 * Get the results
	 * 
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function get()
	{
		$this->applyEagerLoads();
		$this->applyJoins();
		$this->applyFilters();
		$this->applySorters();
		$this->applyRestrictions();
		
		return $this->query->get();
	}

	/**
	 * Apply the eagerloads to the query
	 * 
	 * @return Vespakoen\EPI\EPI
	 */
	protected function applyEagerLoads()
	{
		$this->query->with($this->eagerLoad);
	}

	/**
	 * Turn filters input into standardized array
	 * 
	 * @return array the filters
	 */
	protected function getFilters()
	{
		$rawFilters = Input::get('filter', array());

		$i = 0;
		$filters = array();
		foreach ($rawFilters as $rawFilter => $value)
		{
			$parts = explode('.', $rawFilter);
			$column = array_pop($parts);
			$identifier = implode('.', $parts);

			if(empty($identifier))
			{
				$identifier = $i;
				$i++;
			}

			$filters[$identifier] = array(
				$column,
				$value
			);
		}

		return $filters;
	}

	/**
	 * Turn sorters input into standardized array
	 * 
	 * @return array the sorters
	 */	
	protected function getSorters()
	{
		$rawSorters = (array) Input::get('sort', array());

		$i = 0;
		$sorters = array();
		foreach ($rawSorters as $rawSorter => $order)
		{
			if(is_int($rawSorter))
			{
				$rawSorter = $order;
				$order = 'ASC';
			}

			$parts = explode('.', $rawSorter);
			$column = array_pop($parts);
			$identifier = implode('.', $parts);

			if(empty($identifier))
			{
				$identifier = $i;
				$i++;
			}

			$sorters[$identifier] = array(
				$column,
				$order
			);
		}

		return $sorters;
	}


	/**
	 * Collect the relationships that have to be joined to allow filtering and sorting
	 * 
	 * @return array The relationidentifiers
	 */
	protected function getRelationIdentifiers()
	{
		$filters = $this->getFilters();
		$sorters = $this->getSorters();

		$relationIdentifiers = array();
		foreach ($filters as $identifier => $info)
		{
			if(is_int($identifier))
			{
				continue;
			}

			$relationIdentifiers[] = $identifier;
		}

		foreach ($sorters as $identifier => $info)
		{
			if(is_int($identifier))
			{
				continue;
			}

			$relationIdentifiers[] = $identifier;
		}

		return array_unique($relationIdentifiers);
	}

	/**
	 * Filter the relationships that are already part of another relation
	 * For example "categories" will not get added in
	 * case "categories.translation" is already present.
	 * 
	 * @return array The relationidentifiers to load
	 */
	protected function getRelationIdentifiersToLoad()
	{
		$relationIdentifiers = $this->getRelationIdentifiers();

		$relationIdentifiersToLoad = array();
		foreach ($relationIdentifiers as $relationIdentifier)
		{
			$skip = false;
			foreach ($relationIdentifiersToLoad as $i => $relationIdentifierToLoad)
			{
				if(Str::startsWith($relationIdentifierToLoad, $relationIdentifier))
				{
					$skip = true;
					break;
				}

				if(Str::startsWith($relationIdentifier, $relationIdentifierToLoad))
				{
					$relationIdentifiersToLoad[$i] = $relationIdentifier;
					$skip = true;
					break;
				}
			}

			if($skip || empty($relationIdentifier))
			{
				continue;
			}

			$relationIdentifiersToLoad[] = $relationIdentifier;
		}

		return $relationIdentifiersToLoad;
	}

	/**
	 * Get information about a relationship by a identifier
	 * 
	 * @param  string $relationIdentifier The relation identifier (ex: categories.translation)
	 * @return array the relationships
	 */
	protected function getRelationsByIdentifier($relationIdentifier)
	{
		$parent = null;
		$partialRelationIdentifier = '';
		$relationSegments = explode('.', $relationIdentifier);
		
		$relations = array();
		foreach ($relationSegments as $i => $relationSegment)
		{
			$model = isset($lastEloquentRelation)
				?
					$lastEloquentRelation->getModel()
				:
					$this->model;

			$eloquentRelation = $model->$relationSegment();

			$class = get_class($eloquentRelation);
			switch ($class)
			{
				case 'Illuminate\Database\Eloquent\Relations\BelongsToMany':
					$table = $eloquentRelation->getParent()->getTable();
					$key = $eloquentRelation->getParent()->getKeyName();

					$intermediateTable = $eloquentRelation->getTable();
					$intermediateKey = str_replace($intermediateTable.'.', '', $eloquentRelation->getForeignKey());
					$intermediateOtherKey = str_replace($intermediateTable.'.', '', $eloquentRelation->getOtherKey());

					$foreignTable = $eloquentRelation->getRelated()->getTable();
					$foreignKey = $eloquentRelation->getRelated()->getKeyName();

					$relation = new BelongsToMany($parent, $table, $key, $intermediateTable, $intermediateKey, $intermediateOtherKey, $foreignTable, $foreignKey);
				break;
				case 'Illuminate\Database\Eloquent\Relations\HasOne':
					$table = $eloquentRelation->getParent()->getTable();
					$key = $eloquentRelation->getParent()->getKeyName();

					$foreignTable = $eloquentRelation->getRelated()->getTable();
					$foreignKey = $eloquentRelation->getForeignKey();

					$relation = new HasOne($parent, $table, $key, $foreignTable, $foreignKey);
				break;
				case 'Illuminate\Database\Eloquent\Relations\HasMany':

				break;
			}

			$lastEloquentRelation = $eloquentRelation;
			$parent = $relation;
			
			$partialRelationIdentifier .= (($i === 0) ? '' : '.').$relationSegment;

			$relations[$partialRelationIdentifier] = $relation;	
		}

		return $relations;
	}

	/**
	 * Load information about relationships that are needed for joins and filters
	 * 
	 * @return array The relations
	 */
	protected function getRelations()
	{
		$relationIdentifiersToLoad = $this->getRelationIdentifiersToLoad();

		$relations = array();
		foreach ($relationIdentifiersToLoad as $relationIdentifier)
		{
			$relations += $this->getRelationsByIdentifier($relationIdentifier);
		}

		return $relations;
	}

	/**
	 * Apply joins to the query
	 * 
	 * @return Void
	 */
	protected function applyJoins()
	{
		$relations = $this->getRelations();
		foreach ($relations as $relation)
		{
			$relation->applyJoin($this->query);
		}
	}

	/**
	 * Apply filters to the query
	 * 
	 * @return Void
	 */
	protected function applyFilters()
	{
		$relations = $this->getRelations();

		$filters = $this->getFilters();
		foreach ($filters as $relationIdentifier => $info)
		{
			list($column, $value) = $info;

			if( ! is_int($relationIdentifier))
			{
				$relation = $relations[$relationIdentifier];
				$table = $relation->getTable();
			}
			else
			{
				$table = $this->model->getTable();
			}

			if(Str::startsWith($value, '%') || Str::endsWith($value, '%'))
			{
				$operator = 'LIKE';
			}
			elseif(Str::startsWith($value, $this->operators))
			{
				foreach ($this->operators as $operator)
				{
					if(Str::startsWith($value, $operator))
					{
						break;
					}
				}

				$value = substr($value, strlen($operator));
			}
			else
			{
				$operator = '=';
			}

			$this->query->where($table.'.'.$column, $operator, $value);
		}
	}

	/**
	 * Apply sorters to the query
	 * 
	 * @return Void
	 */
	protected function applySorters()
	{
		$relations = $this->getRelations();

		$sorters = $this->getSorters();
		foreach ($sorters as $relationIdentifier => $info)
		{
			list($column, $order) = $info;

			if( ! is_int($relationIdentifier))
			{
				$relation = $relations[$relationIdentifier];
				$table = $relation->getTable();
			}
			else
			{
				$table = $this->model->getTable();
			}

			$this->select[] = $table.'.'.$column.' AS sort_'.$column;
			
			$this->query->orderBy($table.'.'.$column, $order);
		}
	}

	/**
	 * Apply restrictions to the query
	 * 
	 * @return Void
	 */
	protected function applyRestrictions()
	{
		$this->query->distinct();
		$this->query->select(array_merge($this->select, array($this->model->getTable().'.*')));

		if(Input::has('offset'))
		{
			$offset = Input::get('offset');
			$this->query->skip($offset);
		}

		if(Input::has('limit'))
		{
			$limit = Input::get('limit');
			$this->query->take($limit);
		}

		if(Input::has('page'))
		{
			$perPage = Input::get('perpage', 25);
			$page = Input::get('page');
			$this->query->skip($page);
		}

		if(Input::has('perpage'))
		{
			$perPage = Input::get('perpage');
			$this->query->take($perPage);
		}
	}

}