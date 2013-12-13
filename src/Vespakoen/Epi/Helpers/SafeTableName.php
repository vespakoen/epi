<?php namespace Vespakoen\Epi\Helpers;

use Vespakoen\Epi\Interfaces\RelationInterface;

use Illuminate\Database\Eloquent\Model;

class SafeTableName {

	public function __construct($app)
	{
		$this->app = $app;
		$this->relationUnifier = $app['epi::helpers.relationunifier'];
	}

	public function getForRelationIdentifier($relationIdentifier, $customTable = null, $referencesParent = false)
	{
		$relation = $this->relationUnifier->get($relationIdentifier);

		$table = $relation->getTable();
		if($customTable)
		{
			$table = $customTable;
		}

		$count = count(explode('.', $relationIdentifier)) - 1;

		if( ! $referencesParent)
		{
			$count++;
		}

		$prefix = str_repeat('safe_', $count);

		return $prefix.$table;
	}

}
