<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Interfaces\Extractors\JoinExtractorInterface;

use Illuminate\Support\Str;

class JoinExtractor extends Extractor implements JoinExtractorInterface {

	public function extract(array $filters, array $sorters)
	{
		$manipulators = array_merge($filters, $sorters);

		$uniqueRelationIdentifiers = $this->getUniqueRelationIdentifiersForManipulators($manipulators);

		$relationIdentifiers = $this->getRelationIdentifiersToLoad($uniqueRelationIdentifiers);

		$joins = array();
		foreach($relationIdentifiers as $relationIdentifier)
		{
			$allParts = explode('.', $relationIdentifier);
			$parts = array();
			foreach($allParts as $part)
			{
				$parts[] = $part;

				$unifiedRelation = $this->relationUnifier->get(implode('.', $parts));

				$joins = array_merge($joins, $unifiedRelation->getJoins());
			}
		}

		return $joins;
	}

	protected function getUniqueRelationIdentifiersForManipulators($manipulators)
	{
		$relationIdentifiers = array();

		foreach($manipulators as $manipulator)
		{
			$relationIdentifiers[] = $manipulator->getRelationIdentifier();
		}

		return array_unique($relationIdentifiers);
	}

	protected function getRelationIdentifiersToLoad($relationIdentifiers)
	{
		$relationIdentifiersToLoad = array();
		foreach ($relationIdentifiers as $relationIdentifier)
		{
			$skip = false;
			foreach ($relationIdentifiersToLoad as $i => $relationIdentifierToLoad)
			{
				if(Str::startsWith($relationIdentifierToLoad, $relationIdentifier))
				{
					$skip = true;
					break;
				}

				if(Str::startsWith($relationIdentifier, $relationIdentifierToLoad))
				{
					$relationIdentifiersToLoad[$i] = $relationIdentifier;
					$skip = true;
					break;
				}
			}

			if($skip || empty($relationIdentifier))
			{
				continue;
			}

			$relationIdentifiersToLoad[] = $relationIdentifier;
		}

		return $relationIdentifiersToLoad;
	}

}
