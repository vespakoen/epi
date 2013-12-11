<?php namespace Vespakoen\Epi\Relations;

use Vespakoen\Epi\Manipulators\Join;
use Vespakoen\Epi\Interfaces\RelationInterface;

use Illuminate\Database\Eloquent\Relations\Relation as LaravelRelation;

class HasOne extends Relation implements RelationInterface {

	public function make($parent = null, LaravelRelation $relation = null, $relationIdentifier)
	{
		$this->parent = $parent;
		$this->relation = $relation;
		$this->relationIdentifier = $relationIdentifier;

		return $this;
	}

	public function getJoins()
	{
		$table = $this->relation
			->getModel()
			->getTable();

		$firstTable = $this->parent
			->getTable();
		$firstColumn = $this->parent->getKeyName();

		$secondTableAndColumn = $this->relation
			->getForeignKey();

		return array(
			Join::make($table, $firstTable.'.'.$firstColumn, '=', $secondTableAndColumn)
		);
	}

	public function getTable()
	{
		return $this->relation
			->getModel()
			->getTable();
	}

}
