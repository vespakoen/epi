<?php namespace Vespakoen\Epi\Interfaces\Extractors;

interface FilterExtractorInterface {

	/**
	 * Extracts sorter conditions from the given input
	 *
	 * @param  array
	 * @return array of Epi\Manipulators\Filter
	 */
	public function extract(array $input);

}
