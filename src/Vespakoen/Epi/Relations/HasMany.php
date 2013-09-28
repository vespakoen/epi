<?php namespace Vespakoen\Epi\Relations;

use Vespakoen\Epi\Interfaces\RelationInterface;

use Illuminate\Database\Eloquent\Relations\Relation;

class HasMany implements RelationInterface {

	public function __construct(RelationInterface $parent = null, Relation $relation = null, $relationIdentifier)
	{
		$this->parent = $parent;
		$this->relation = $relation;
		$this->relationIdentifier = $relationIdentifier;
	}

	public function getJoins()
	{
		dd($this);
	}

	public function getTable()
	{
		return $this->relation->getTable();
	}

}
