<?php namespace Vespakoen\Epi\Relations;

use Vespakoen\Epi\Interfaces\RelationInterface;

use Illuminate\Database\Eloquent\Relations\Relation;

class HasMany implements RelationInterface {

	public function __construct(RelationInterface $parent = null, Relation $relation)
	{
		$this->parent = $parent;
		$this->relation = $relation;
	}

	public function applyJoins($query)
	{

	}

	public function getTable()
	{
		return $this->relation->getTable();
	}

}
