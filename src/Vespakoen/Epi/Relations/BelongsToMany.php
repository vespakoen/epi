<?php namespace Vespakoen\Epi\Relations;

use Vespakoen\Epi\Interfaces\RelationInterface;

use Illuminate\Database\Eloquent\Relations\Relation;

class BelongsToMany implements RelationInterface {

	public function make($parent = null, Relation $relation = null, $relationIdentifier = null)
	{
		$this->parent = $parent;
		$this->relation = $relation;
		$this->relationIdentifier = $relationIdentifier;

		return $this;
	}

	public function getJoins()
	{
		$table = $this->parent->getTable();
		$key = $this->parent->getKeyName();
dd($key);
		$intermediateTable = $eloquentRelation->getTable();
		$intermediateKey = str_replace($intermediateTable.'.', '', $eloquentRelation->getForeignKey());
		$intermediateOtherKey = str_replace($intermediateTable.'.', '', $eloquentRelation->getOtherKey());

		$foreignTable = $eloquentRelation->getRelated()->getTable();
		$foreignKey = $eloquentRelation->getRelated()->getKeyName();

		$relation = new BelongsToMany($parent, $table, $key, $intermediateTable, $intermediateKey, $intermediateOtherKey, $foreignTable, $foreignKey);

		return array(
			Join::make(),
			Join::make()
		);
	}

	public function getTable()
	{
		return $this->relation->getTable();
	}

}



				// case 'Illuminate\Database\Eloquent\Relations\BelongsToMany':

				// break;
				// case 'Illuminate\Database\Eloquent\Relations\HasOne':
				// 	$table = $eloquentRelation->getParent()->getTable();
				// 	$key = $eloquentRelation->getParent()->getKeyName();

				// 	$foreignTable = $eloquentRelation->getRelated()->getTable();
				// 	$foreignKey = $eloquentRelation->getForeignKey();

				// 	$relation = new HasOne($parent, $table, $key, $foreignTable, $foreignKey);
				// break;
				// case 'Illuminate\Database\Eloquent\Relations\HasMany':
				// 	$table = $eloquentRelation->getParent()->getTable();
				// 	$key = $eloquentRelation->getParent()->getKeyName();

				// 	$foreignTable = $eloquentRelation->getRelated()->getTable();
				// 	$parts = explode('.', $eloquentRelation->getForeignKey());
				// 	$foreignKey = array_pop($parts);

				// 	$relation = new HasMany($parent, $table, $key, $foreignTable, $foreignKey);
				// break;
				// case 'Illuminate\Database\Eloquent\Relations\BelongsTo':
				// 	$table = $eloquentRelation->getParent()->getTable();
				// 	$key = $eloquentRelation->getForeignKey();

				// 	$foreignTable = $eloquentRelation->getRelated()->getTable();
				// 	$foreignKey = $eloquentRelation->getRelated()->getKeyName();

				// 	$relation = new BelongsTo($parent, $table, $key, $foreignTable, $foreignKey);
				// break;
				// case 'Illuminate\Database\Eloquent\Relations\MorphMany':
				// 	$table = $eloquentRelation->getParent()->getTable();
				// 	$key = $eloquentRelation->getParent()->getKeyName();

				// 	$foreignTable = $eloquentRelation->getRelated()->getTable();

				// 	$parts = explode('.', $eloquentRelation->getForeignKey());
				// 	$foreignKey = array_pop($parts);

				// 	$parts = explode('.', $eloquentRelation->getMorphType());
				// 	$morphType = array_pop($parts);

				// 	$morphClass = $eloquentRelation->getMorphClass();

				// 	$relation = new MorphMany($parent, $table, $key, $foreignTable, $foreignKey, $morphType, $morphClass);
				// break;
