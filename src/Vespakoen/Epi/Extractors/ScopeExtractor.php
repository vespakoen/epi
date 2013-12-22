<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Interfaces\Extractors\ScopeExtractorInterface;

class ScopeExtractor extends Extractor implements ScopeExtractorInterface {

	public function extract(array $input)
	{
		// where in the input should we look?
		$key = $this->config['keys']['scopes'];

		// is there a sort at all?
		if( ! array_key_exists($key, $input))
		{
			return array();
		}

		// make some scopes!
		$scopes = array();
		foreach($input[$key] as $relationIdentifier => $scopeName)
		{
			if(is_int($relationIdentifier))
			{
				$input[$key][''][] = $scopeName;

				unset($input[$key][$relationIdentifier]);
			}
		}

		foreach($input[$key] as $relationIdentifier => $scopeNames)
		{
			$relationIdentifier = $relationIdentifier == '' ? null : $relationIdentifier;
			$relation = $this->relationUnifier->get($relationIdentifier);
			$model = $relation->getModel();

			$scopeNames = (array) $scopeNames;
			foreach($scopeNames as $scopeName)
			{
				$scope = $this->app->make('epi::manipulators.scope');
				$scopes[] = $scope->make($model, $scopeName);
			}
		}

		return $scopes;
	}

}
