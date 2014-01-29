<?php namespace Vespakoen\Epi\Adaptors;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class Ember extends Adaptor {

	public $response = array();

	public function getIdentifierFromModel($model, $parentModel = null)
	{
		$modelName = get_class($model);
		$parts = explode('\\', $modelName);

        if(get_class($model) == get_class($parentModel))
        {
            return null;
        }

		if($model instanceof Pivot)
		{
			return $model->getTable();
		}

		return strtolower(array_pop($parts));
	}

	public function process($data, $parentIdentifier = null, $parentModel = null, $isCollection = false)
	{
		$ids = array();
		if($data instanceof Collection)
		{
			foreach($data as $model)
			{
				$this->process($model, $parentIdentifier, $parentModel, true);
			}
        }
		elseif($data instanceof Model)
		{
			$key = $data->getKey();

			foreach($data->getRelations() as $relationIdentifier => $collection)
			{
				$this->process($collection, $relationIdentifier, $data);
			}

			$resolvedIdentifier = $this->getIdentifierFromModel($data, $parentModel);
            $identifier = is_null($resolvedIdentifier) ? $parentIdentifier : $resolvedIdentifier;
			if( ! isset($this->response[$identifier][$key]))
			{
				$this->response[$identifier][$key] = $data->toArray();
			}
			elseif( ! is_null($parentModel))
			{
                if($isCollection)
                {
                    $ids = array($key);
                    $column = $resolvedIdentifier.'_ids';
                }
                else
                {
                    $ids = $key;
                    $column = $resolvedIdentifier.'_id';
                }

                if($isCollection && isset($this->response[$identifier][$key][$column]))
                {
                    $this->response[$identifier][$key][$column] = array_merge($this->response[$identifier][$key][$column], array($ids));
                }
                elseif( ! isset($this->response[$identifier][$key][$column]))
                {
                    $this->response[$identifier][$key][$column] = $ids;
                }
			}

            if( ! is_null($parentModel))
            {
                $parentModel->setRelation($parentIdentifier, false);
            }

            if( ! is_null($parentModel) && ! $isCollection)
            {
                $column = $parentIdentifier.'_id';
                $parentModel->$column = $ids;
            }
        }
        else
        {
            if( ! is_null($parentModel))
            {
                $parentModel->setRelation($parentIdentifier, false);

                $column = Str::singular($parentIdentifier).'_id';
                $parentModel->$column = null;
            }
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
