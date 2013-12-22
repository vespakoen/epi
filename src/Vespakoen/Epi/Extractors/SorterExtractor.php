<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Interfaces\Extractors\SorterExtractorInterface;

class SorterExtractor extends Extractor implements SorterExtractorInterface {

	public function extract(array $input)
	{
		// where in the input should we look?
		$key = $this->config['keys']['sort'];

		// is there a sort at all?
		if( ! array_key_exists($key, $input))
		{
			return array();
		}

		// make some sorters!
		$sorters = array();
		if(is_string($input[$key]))
		{
			$sorters[] = $this->getSorter($input[$key], 'asc');

			return $sorters;
		}

		foreach($input[$key] as $rawRelationIdentifierAndColumn => $direction)
		{
			$sorters[] = $this->getSorter($rawRelationIdentifierAndColumn, $direction);
		}

		return $sorters;
	}

	protected function getSorter($rawRelationIdentifierAndColumn, $direction)
	{
		list($relationIdentifier, $column) = $this->extractRelationIdentifierAndColumn($rawRelationIdentifierAndColumn);

		$relation = $this->relationUnifier->get($relationIdentifier);
		$table = $relation->getTable();

		$sorter = $this->app->make('epi::manipulators.sorter');
		return $sorter->make($relationIdentifier, $table, $column, $direction);
	}

}
