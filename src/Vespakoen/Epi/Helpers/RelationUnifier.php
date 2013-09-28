<?php namespace Vespakoen\Epi\Helpers;

use Illuminate\Database\Eloquent\Model;

class RelationUnifier {

	protected $model;

	protected $cached = array();

	public function __construct($app)
	{
		$this->app = $app;
	}

	public function unify($parent, $relation, $currentRelationIdentifier)
	{
		// take the last part in the relationship class and cast it to lowercase
		$parts = explode('\\', get_class($relation));
		$relationKey = strtolower(end($parts));

		// get the unified relationship
		$unifiedRelation = $this->app['epi::relations.'.$relationKey];

		// set some properties on it
		return $unifiedRelation->make($parent, $relation, $currentRelationIdentifier);
	}

	/**
	 * Get a unified relationship by it's identifier
	 *
	 * @param  string $relationIdentifier
	 * @return RelationInterface
	 */
	public function get($relationIdentifier)
	{
		// got cache?
		if(array_key_exists($relationIdentifier, $this->cached))
		{
			return $this->cached[$relationIdentifier];
		}

		// split up the identifier into the relation names
		$relationNames = explode('.', $relationIdentifier);

		// we need to set some values for the first run
		$model = $this->app['epi::epi']->getModel();

		$lastRelation = $model;

		// loop over the parts in the relationidentifier to extract the stuff we need
		foreach ($relationNames as $i => $relationName)
		{
			// get the relation off the current model
			$relation = $model->$relationName();

			// set the current model for the next run of this loop
			$model = $relation->getModel();

			// get the relationidentifier of the current relation
			$currentRelationIdentifier = implode('.', array_slice($relationNames, 0, $i + 1));

			// get te unified relation object
			$unifiedRelation = $this->unify($lastRelation, $relation, $currentRelationIdentifier);

			// store the result in cache
			$this->cached[$currentRelationIdentifier] = $lastRelation = $unifiedRelation;
		}

		// return the relationship we are looking for
		return $unifiedRelation;
	}

}
