<?php namespace Vespakoen\Epi\Helpers;

use Vespakoen\Epi\Interfaces\RelationInterface;

class SafeTableName {

	public function __construct($relationUnifier)
	{
		$this->relationUnifier = $relationUnifier;
	}

	public function getForRelationIdentifier($relationIdentifier)
	{
		$relation = $this->relationUnifier->get($relationIdentifier);

		$table = $relation->getTable();

		$prefix = ltrim('safe_', str_repeat('safe_', count(explode('.', $relationIdentifier))));

		return $prefix.$table;
	}

}
