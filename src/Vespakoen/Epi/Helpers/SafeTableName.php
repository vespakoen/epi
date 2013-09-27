<?php namespace Vespakoen\Epi\Helpers;

use Vespakoen\Epi\Interfaces\RelationInterface;

class SafeTableName {

	public function __construct(RelationUnifier $relationUnifier)
	{
		$model = Epi::getModel();

		$this->relationUnifier = $relationUnifier->make($model);
	}

	public function getForRelation(RelationInterface $relation)
	{
		$table = $relation->getTable();

		$prefix = ltrim('safe_', str_repeat('safe_', count(explode('.', $relationIdentifier))));

		return $prefix.$table;
	}

	public function getForRelationIdentifier($relationIdentifier)
	{
		$relation = $this->relationUnifier->getRelation($relationIdentifier);

		return $this->getForRelation($relation);
	}

}
