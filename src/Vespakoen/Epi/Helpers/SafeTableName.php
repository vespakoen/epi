<?php namespace Vespakoen\Epi\Helpers;

use Vespakoen\Epi\Interfaces\RelationInterface;

class SafeTableName {

	public function __construct($relationUnifier)
	{
		$this->relationUnifier = $relationUnifier;
	}

	public function getForRelationIdentifier($relationIdentifier, $customTable = null)
	{
		$relation = $this->relationUnifier->get($relationIdentifier);

		$table = $relation->getTable();

		if($customTable)
		{
			$table = $customTable;
		}

		$prefix = ltrim('safe_', str_repeat('safe_', count(explode('.', $relationIdentifier))));

		return $prefix.$table;
	}

}
