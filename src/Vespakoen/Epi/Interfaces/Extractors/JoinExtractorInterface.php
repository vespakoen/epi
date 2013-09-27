<?php namespace Vespakoen\Epi\Interfaces\Extractors;

interface JoinExtractorInterface {

	/**
	 * Extracts sorter conditions from the given input
	 *
	 * @param  array of Epi\Manipulators\Filter
	 * @param  array of Epi\Manipulators\Sorter
	 * @return array of Epi\Manipulators\Join
	 */
	public function extract(array $filters, array $sorters);

}
