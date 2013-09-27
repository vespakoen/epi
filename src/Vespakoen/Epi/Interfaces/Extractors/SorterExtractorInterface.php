<?php namespace Vespakoen\Epi\Interfaces\Extractors;

interface SorterExtractorInterface {

	/**
	 * Extracts sorter conditions from the given input
	 *
	 * @param  array
	 * @return array of Epi\Manipulators\Sorter
	 */
	public function extract(array $input);

}
