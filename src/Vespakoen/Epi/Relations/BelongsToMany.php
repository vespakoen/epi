<?php namespace Vespakoen\Epi\Relations;

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
		$relationIdentifier = $this->relationIdentifier;
		$firstTable = $this->getFirstTable();
		$firstColumn = $this->getFirstColumn();
		$secondTable = $this->getSecondTable();
		$secondColumn = $this->getSecondColumn();
		$otherFirstTable = $this->getOtherFirstTable();
		$otherFirstColumn = $this->getOtherFirstColumn();
		$otherSecondTable = $this->getOtherSecondTable();
		$otherSecondColumn = $this->getOtherSecondColumn();

		$firstJoin = $this->app->make('epi::manipulators.join');
		$secondJoin = $this->app->make('epi::manipulators.join');
		return array(
			$firstJoin->make($relationIdentifier, $firstTable, $firstColumn, '=', $secondTable, $secondColumn),
			$secondJoin->make($relationIdentifier, 'safe_'.$otherFirstTable, $otherFirstColumn, '=', $otherSecondTable, $otherSecondColumn)
		);
	}

	public function getTable()
	{
		return $this->getOtherSecondTable();
	}

	protected function getFirstTable()
	{
		$table = $this->relation->getParent()
			->getTable();

		return $table;
	}

	protected function getFirstColumn()
	{
		$table = $this->relation->getParent()
			->getKeyName();

		return $table;
	}

	protected function getSecondTable()
	{
		$tableAndKey = $this->relation->getForeignKey();
		list($table, $key) = explode('.', $tableAndKey);

		return $table;
	}

	protected function getSecondColumn()
	{
		$tableAndKey = $this->relation->getForeignKey();
		list($table, $key) = explode('.', $tableAndKey);

		return $key;
	}

	protected function getOtherFirstTable()
	{
		$table = $this->relation->getTable();

		return $table;
	}

	protected function getOtherFirstColumn()
	{
		$tableAndKey = $this->relation->getOtherKey();
		list($table, $key) = explode('.', $tableAndKey);

		return $key;
	}

	protected function getOtherSecondTable()
	{
		$table = $this->relation->getModel()
			->getTable();

		return $table;
	}

	protected function getOtherSecondColumn()
	{
		$key = $this->relation->getModel()
			->getKeyName();

		return $key;
	}

}
