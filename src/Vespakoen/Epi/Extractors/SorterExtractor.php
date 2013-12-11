<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Manipulators\Sorter;
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
			list($relationIdentifier, $column) = $this->extractRelationIdentifierAndColumn($input[$key]);
			$direction = 'asc';

			$table = $this->getSafeAliasedTableName($relationIdentifier);

			$sorters[] = Sorter::make($relationIdentifier, $table, $column, $direction);

			return $sorters;
		}

		foreach($input[$key] as $rawRelationIdentifierAndColumn => $direction)
		{
			list($relationIdentifier, $column) = $this->extractRelationIdentifierAndColumn($rawRelationIdentifierAndColumn);

			$table = $this->getSafeAliasedTableName($relationIdentifier);

			$sorters[] = Sorter::make($relationIdentifier, $table, $column, $direction);
		}

		return $sorters;
	}

}
