<?php namespace Vespakoen\Epi\Interfaces\Extractors;

interface ScopeExtractorInterface {

	/**
	 * Extracts query scopes from the given input
	 *
	 * @param  array
	 * @return array of Epi\Manipulators\Scope
	 */
	public function extract(array $input);

}
