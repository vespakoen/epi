<?php namespace Vespakoen\Epi\Extractors;

class Extractor {

	protected $app;

	protected $relationUnifier;

	protected $config;

	protected $manipulatorStore;

	public function __construct($app)
	{
		$this->app = $app;
		$this->relationUnifier = $app['epi::helpers.relationunifier'];
		$this->config = $app['config']['epi::epi'];
	}

	public function setManipulatorStore($manipulatorStore)
	{
		$this->manipulatorStore = $manipulatorStore;

		return $this;
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
