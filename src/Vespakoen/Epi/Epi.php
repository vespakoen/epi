<?php namespace Vespakoen\Epi;

use Vespakoen\Epi\Collections\ExtractorCollection;
use Vespakoen\Epi\Stores\ManipulatorStore;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Epi {

	public function __construct(ExtractorCollection $extractors)
	{
		$this->extractors = $extractors;
	}

	public function make(Model $model, array $input)
	{
		$this->model = $model;
		$this->input = $input;

		return $this;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function get()
	{
		$query = $this->getQuery();

		return $query->get();
	}

	public function first()
	{
		$query = $this->getQuery();

		return $query->first();
	}

	public function addInput($key, $value)
	{
		$key = Config::get('epi::epi.keys.'.$key);

		if(array_key_exists($key, $this->input))
		{
			$this->input[$key] = array_merge($this->input[$key], $value);
		}
		else
		{
			$this->input[$key] = $value;
		}

		return $this;
	}

	public function getCleanInput($input = array())
	{
		$operators = $this->extractors->get('filters')
			->getOperators();

		$filters = array_get($input, 'filter', array());
		foreach ($filters as $key => $value)
		{
			list($operator, $value) = $this->extractors->get('filters')
				->extractOperatorAndValue($value);

			if(is_string($value))
			{
				$value = trim($value, '%');
			}

			$input['filter'][$key] = $value;
		}

		return $input;
	}

	public function getInput()
	{
		return $this->input;
	}

	protected function getQuery()
	{
		$table = $this->getModel()
			->getTable();

		$query = $this->model->newInstance()
			->distinct()
			->select($table.'.*');

		$manipulators = $this->getManipulators();
		foreach($manipulators as $manipulator)
		{
			$query = $manipulator->applyTo($query);
		}

		return $query;
	}

	protected function getManipulators()
	{
		$manipulatorStore = new ManipulatorStore();
		foreach($this->extractors as $type => $extractor)
		{
			$manipulators = $extractor->setManipulatorStore($manipulatorStore)
				->extract($this->input);

			$manipulatorStore->add($type, $manipulators);
		}

		return $manipulatorStore->all();
	}

}
