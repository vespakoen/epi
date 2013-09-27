<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Interfaces\Extractors\JoinExtractorInterface;

class JoinExtractor implements JoinExtractorInterface {

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function extract(array $filters, array $sorters)
	{
		$relationIdentifiers = array();

		foreach(array_merge($filters, $sorters) as $manipulator)
		{
			$relationIdentifiers[] = $manipulator->getRelationIdentifier();
		}

		$relationIdentifiers = array_unique($relationIdentifiers);

		return array();
	}

}
