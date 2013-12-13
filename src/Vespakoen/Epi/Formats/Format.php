<?php namespace Vespakoen\Epi\Formats;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Input;

class Format {

	public function __construct($app)
	{
		$this->app = $app;
	}

	public function prepare($result)
	{
		if(Input::has('serialize'))
		{
			return $this->serialize($result);
		}

		return $result;
	}

	protected function serialize($results, $toArray = true)
	{
		if($results instanceof Model)
		{
			$results->serialized_type = 'model';
			$results->serialized_class = get_class($results);

			foreach($results->getRelations() as $key => $relation)
			{
				$results->setRelation($key, $this->serialize($relation, false));
			}

			return $toArray ? $results->toArray() : $results;
		}

		if($results instanceof Collection)
		{
			$serialized = array(
				'serialized_type' => 'collection',
				'serialized_class' => get_class($results),
				'items' => array()
			);

			foreach($results as $result)
			{
				$serialized['items'][] = $this->serialize($result);
			}

			return $serialized;
		}

		return $results;
	}

}
