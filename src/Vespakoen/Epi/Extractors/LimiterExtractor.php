<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Manipulators\Limiter;
use Vespakoen\Epi\Interfaces\Extractors\LimiterExtractorInterface;

class LimiterExtractor extends Extractor implements LimiterExtractorInterface {

	public function extract(array $input)
	{
		// where in the input should we look?
		$skipKey = $this->config['keys']['skip'];
		$takeKey = $this->config['keys']['take'];

		// is there a filter at all?
		if( ! array_key_exists($skipKey, $input) && ! array_key_exists($takeKey, $input))
		{
			return array();
		}

		$limiters = array();

		$limiters[] = Limiter::make(array_get($input, $skipKey, 0), array_get($input, $takeKey, 25));

		return $limiters;
	}

}
