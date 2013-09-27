<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Interfaces\Extractors\LimiterExtractorInterface;

class LimiterExtractor implements LimiterExtractorInterface {

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function extract(array $input)
	{
		return array();
	}

}
