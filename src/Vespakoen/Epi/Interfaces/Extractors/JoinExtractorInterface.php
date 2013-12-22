<?php namespace Vespakoen\Epi\Interfaces\Extractors;

interface JoinExtractorInterface {

/**
	 * Extracts joins from sorters and filters
	 *
	 * @param  array
	 * @return array of Epi\Manipulators\Join
	 */
	public function extract(array $input);

}
