<?php namespace Vespakoen\Epi\Helpers;

use Vespakoen\Epi\Interfaces\RelationInterface;

use Illuminate\Database\Eloquent\Model;

class SafeTableName {

	public function __construct($app)
	{
		$this->app = $app;
		$this->relationUnifier = $app['epi::helpers.relationunifier'];
	}

	public function getForRelationIdentifier($relationIdentifier, $customTable = null, $extraUnique = false)
	{
		$relation = $this->relationUnifier->get($relationIdentifier);

		// if($relation->parent instanceof Model)
		// {
		// 	$parentTable = $relation->parent->getTable();
		// }
		// else
		// {
		//
		if($relation->parent)
		{
			$parentTable = $relation->parent->getTable();
		}
		else
		{
			$parentTable = null;
		}
		//}

		$table = $relation->getTable();

		if($customTable)
		{
			$table = $customTable;
		}

		$count = count(explode('.', $relationIdentifier)) - 1;

		if($parentTable == $table && $extraUnique)
		{
			$count++;
		}

		$prefix = str_repeat('safe_', $count);

		return $prefix.$table;
	}

}
