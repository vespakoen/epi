<?php namespace Vespakoen\Epi\Relations;

use Vespakoen\Epi\Facades\Join;
use Vespakoen\Epi\Interfaces\RelationInterface;

use Illuminate\Database\Eloquent\Relations\Relation as LaravelRelation;

class BelongsTo extends Relation implements RelationInterface {

	public function make($parent = null, LaravelRelation $relation = null, $relationIdentifier)
	{
		$this->parent = $parent;
		$this->relation = $relation;
		$this->relationIdentifier = $relationIdentifier;

		return $this;
	}

	public function getJoins()
	{
		$relationIdentifier = $this->relationIdentifier;
		$firstTable = $this->getFirstTable();
		$firstColumn = $this->getFirstColumn();
		$secondTable = $this->getSecondTable();
		$secondColumn = $this->getSecondColumn();

		$join = $this->app->make('epi::manipulators.join');
		return array(
			$join->make($relationIdentifier, $firstTable, $firstColumn, '=', $secondTable, $secondColumn)
		);
	}

	public function getTable()
	{
		return $this->getSecondTable();
	}

	protected function getFirstTable()
	{
		$table = $this->relation->getParent()
			->getTable();

		return $table;
	}

	protected function getFirstColumn()
	{
		$key = $this->relation->getForeignKey();

		return $key;
	}

	protected function getSecondTable()
	{
		$table = $this->relation->getModel()
			->getTable();

		return $table;
	}

	protected function getSecondColumn()
	{
		$key = $this->relation->getParent()
			->getKeyName();

		return $key;
	}

}
