<?php namespace Vespakoen\Epi\Interfaces\Extractors;

interface LimiterExtractorInterface {

	/**
	 * Extracts sorter conditions from the given input
	 *
	 * @param  array
	 * @return array of Epi\Manipulators\Limiter
	 */
	public function extract(array $input);

}
