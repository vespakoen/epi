<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Interfaces\Extractors\SorterExtractorInterface;

class SorterExtractor implements SorterExtractorInterface {

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function extract(array $input)
	{
		return array();
	}

}
