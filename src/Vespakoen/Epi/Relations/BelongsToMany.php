<?php namespace Vespakoen\Epi\Relations;

use Vespakoen\Epi\Manipulators\Join;
use Vespakoen\Epi\Interfaces\RelationInterface;

use Illuminate\Database\Eloquent\Relations\Relation as LaravelRelation;

class BelongsToMany extends Relation implements RelationInterface {

	public function make($parent = null, LaravelRelation $relation = null, $relationIdentifier = null)
	{
		$this->parent = $parent;
		$this->relation = $relation;
		$this->relationIdentifier = $relationIdentifier;

		return $this;
	}

	public function getJoins()
	{
		$firstTable = $this->getFirstTable();
		$firstForeign = $this->getFirstForeign();
		$firstOther = $this->getFirstOther();

		$secondTable = $this->getSecondTable();
		$secondForeign = $this->getSecondForeign();
		$secondOther = $this->getSecondOther();

		return array(
			Join::make($firstTable, $firstForeign, '=', $firstOther),
			Join::make($secondTable, $secondForeign, '=', $secondOther)
		);
	}

	public function getTable()
	{
		$table = $this->relation
			->getModel()
			->getTable();

		return $table;
	}

	protected function getFirstTable()
	{
		$table = $this->relation->getTable();

		return $this->safe($table);
	}

	protected function getSecondTable()
	{
		$table = $this->relation
			->getModel()
			->getTable();

		return $this->safe($table);
	}

	protected function getFirstForeign()
	{
		$table = $this->parent->getTable();
		$safeTable = $this->safe($table);

		$key = $this->parent->getKeyName();

		return $safeTable.'.'.$key;
	}

	protected function getSecondForeign()
	{
		$tableAndKey = $this->relation->getOtherKey();
		list($table, $key) = explode('.', $tableAndKey);

		$safeTable = $this->safe($table);

		return $safeTable.'.'.$key;
	}

	protected function getFirstOther()
	{
		$tableAndKey = $this->relation->getForeignKey();
		list($table, $key) = explode('.', $tableAndKey);

		$safeTable = $this->safe($table);

		return $safeTable.'.'.$key;
	}

	protected function getSecondOther()
	{
		$table = $this->relation->getModel()
			->getTable();
		$safeTable = $this->safe($table);

		$key = $this->relation->getModel()
			->getKeyName();

		return $safeTable.'.'.$key;
	}


}
