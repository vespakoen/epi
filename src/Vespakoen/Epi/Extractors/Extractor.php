<?php namespace Vespakoen\Epi\Extractors;

class Extractor {

	public function __construct($app)
	{
		$this->app = $app;
		$this->relationUnifier = $app['epi::helpers.relationunifier'];
		$this->config = $app['config']['epi::epi'];
	}

	protected function extractRelationIdentifierAndColumn($rawRelationIdentifierAndColumn)
	{
		$relationNames = explode('.', $rawRelationIdentifierAndColumn);
		$column = array_pop($relationNames);
		$relationIdentifier = implode('.', $relationNames);

		if($relationIdentifier == '')
		{
			$relationIdentifier = null;
		}

		return array(
			$relationIdentifier,
			$column
		);
	}

}
