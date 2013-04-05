<?php namespace Vespakoen\EPI;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

use Vespakoen\EPI\Relations\BelongsToMany;
use Vespakoen\EPI\Relations\HasOne;
use Vespakoen\EPI\Relations\HasMany;

class EPI {

	public $eagerLoad = array();

	public $select = array();

	public function __construct($model)
	{
		$this->model = $model;
		$this->query = $this->model->newQuery();
	}

	public static function model($model)
	{
		return new static(new $model);
	}

	public static function modelInstance($model)
	{
		return new static($model);
	}

	public function with($relations)
	{
		$this->eagerLoad = $relations;

		return $this;
	}

	public function get()
	{
		$this->applyEagerLoads();
		$this->applyJoins();
		$this->applyFilters();
		$this->applySorters();
		$this->applyRestrictions();
		
		return $this->query->get();
	}

	protected function applyEagerLoads()
	{
		$this->query->with($this->eagerLoad);
	}

	protected function getFilters()
	{
		$rawFilters = Input::get('filter', array());

		$filters = array();
		foreach ($rawFilters as $rawFilter => $value)
		{
			$parts = explode('.', $rawFilter);
			$column = array_pop($parts);
			$identifier = implode('.', $parts);

			$filters[$identifier] = array(
				$column,
				$value
			);
		}

		return $filters;
	}

	protected function getSorters()
	{
		$rawSorters = Input::get('sort', array());

		$sorters = array();
		foreach ($rawSorters as $rawSorter => $order)
		{
			if(empty($order))
			{
				$order = 'ASC';
			}

			$parts = explode('.', $rawSorter);
			$column = array_pop($parts);
			$identifier = implode('.', $parts);

			$sorters[$identifier] = array(
				$column,
				$order
			);
		}

		return $sorters;
	}

	protected function getRelationIdentifiers()
	{
		$filters = $this->getFilters();
		$sorters = $this->getSorters();

		$relationIdentifiers = array();
		foreach ($filters as $identifier => $info)
		{
			$relationIdentifiers[] = $identifier;
		}

		foreach ($sorters as $identifier => $info)
		{
			$relationIdentifiers[] = $identifier;
		}

		return array_unique($relationIdentifiers);
	}

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

	protected function applyJoins()
	{
		$relations = $this->getRelations();
		foreach ($relations as $relation)
		{
			$relation->applyJoin($this->query);
		}
	}

	protected function applyFilters()
	{
		$relations = $this->getRelations();
		
		$rawFilters = Input::get('filter', array());
		foreach ($rawFilters as $rawFilter => $value)
		{
			$parts = explode('.', $rawFilter);
			$column = array_pop($parts);
			$relationIdentifier = implode('.', $parts);
			if( ! empty($relationIdentifier))
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
			else
			{
				$operator = '=';
			}

			$this->query->where($table.'.'.$column, $operator, $value);
		}
	}

	protected function applySorters()
	{
		$relations = $this->getRelations();

		$rawSorters = Input::get('sort', array());
		foreach ($rawSorters as $rawSorter => $order)
		{
			if(empty($order))
			{
				$order = 'ASC';
			}

			$parts = explode('.', $rawSorter);
			$column = array_pop($parts);
			$relationIdentifier = implode('.', $parts);
			if( ! empty($relationIdentifier))
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

}