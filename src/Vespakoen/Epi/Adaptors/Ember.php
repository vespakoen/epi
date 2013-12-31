<?php namespace Vespakoen\Epi\Adaptors;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class Ember extends Adaptor {

	public $response = array();

	public function getIdentifierFromModel($model)
	{
		$modelName = get_class($model);
		$parts = explode('\\', $modelName);
		return strtolower(array_pop($parts));
	}

	public function process($data, $relationIdentifier = null, $parentModel = null)
	{
		$ids = array();
		if($data instanceof Collection)
		{
			foreach($data as $model)
			{

				$ids[] = $model->getKey();

				$this->process($model);
			}
		}
		elseif($data instanceof Model)
		{
			$key = $data->getKey();
			$ids = $key;

			foreach($data->getRelations() as $identifier => $collection)
			{
				if($identifier == 'pivot') continue;

				$this->process($collection, $identifier, $data);
			}

			$identifier = $this->getIdentifierFromModel($data);
			if( ! isset($this->response[$identifier][$key]))
			{
				$this->response[$identifier][$key] = $data->toArray();
			}
			elseif( ! is_null($parentModel))
			{
				if(isset($this->response[$identifier][$key][$this->getIdentifierFromModel($parentModel)]))
				{
					$this->response[$identifier][$key][$this->getIdentifierFromModel($parentModel)] = array_merge($this->response[$identifier][$key][$this->getIdentifierFromModel($parentModel)], $ids);
				}
				else
				{
					$this->response[$identifier][$key][$this->getIdentifierFromModel($parentModel)] = $ids;
				}
			}
		}

		if( ! is_null($parentModel))
		{
			$parentModel->setRelation($relationIdentifier, false);
			$identifier = $this->getIdentifierFromModel($data).'Ids';
			$parentModel->$identifier = $ids;
		}

		return $this->response;
	}

	public function adapt($data)
	{
		$response = $this->process($data);

		foreach($response as $identifier => $models)
		{
			$response[$identifier] = array_values($models);
		}

		return $response;
	}

}
