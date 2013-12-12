<?php namespace Vespakoen\Epi\Relations;

class Relation {

	public function __construct($app)
	{
		$this->app = $app;
		$this->safeTableName = $app['epi::helpers.safetablename'];
	}

	protected function safe($table = null, $extraUnique = false)
	{
		return $this->safeTableName->getForRelationIdentifier($this->relationIdentifier, $table, $extraUnique);
	}

}
