<?php namespace Vespakoen\Epi\Manipulators;

class Manipulator {

	public function __construct($app)
	{
		$this->app = $app;
		$this->safeTableName = $app['epi::helpers.safetablename'];
	}

	public function safe($customTable, $referencesParent = false)
	{
		return $this->safeTableName->getForRelationIdentifier($this->relationIdentifier, $customTable, $referencesParent);
	}

	public function debug()
	{
		$this->app = null;
		$this->safeTableName = null;

		return $this;
	}

}
