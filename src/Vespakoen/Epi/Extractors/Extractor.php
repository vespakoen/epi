<?php namespace Vespakoen\Epi\Extractors;

class Extractor {

	public function __construct($safeTableName, array $config)
	{
		$this->safeTableName = $safeTableName;
		$this->config = $config;
	}

	public function getSafeAliasedTableName($relationIdentifier)
	{
		return $this->safeTableName->getForRelationIdentifier($relationIdentifier);
	}

}
