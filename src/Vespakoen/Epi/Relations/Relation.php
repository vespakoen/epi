<?php namespace Vespakoen\Epi\Relations;

class Relation {

	public function __construct($safeTableName)
	{
		$this->safeTableName = $safeTableName;
	}

	protected function safe($table = null)
	{
		return $this->safeTableName->getForRelationIdentifier($this->relationIdentifier, $table);
	}

}
