<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Relations\MorphMany;
use Vespakoen\Epi\Relations\MorphOne;

class MorphFilterExtractor extends Extractor {

	public function extract(array $input)
	{
		$currentFilters = $this->manipulatorStore->get('filters');

		$filters = array();
		foreach ($currentFilters as $currentFilter)
		{
		 	$relationIdentifier = $currentFilter->getRelationIdentifier();

			$relation = $this->relationUnifier->get($relationIdentifier);
			$table = $relation->getTable();

			if($relation instanceof MorphMany || $relation instanceof MorphOne)
			{
				$morphType = $relation->relation->getMorphType();
				$parts = explode('.', $morphType);

				$column = end($parts);
				$value = get_class($relation->parent);

				$filter = $this->app->make('epi::manipulators.filter');
				$filters[] = $filter->make($relationIdentifier, $table, $column, '=', $value);
			}
		}

		return $filters;
	}

}
