<?php namespace Vespakoen\Epi;

use Vespakoen\Epi\Collections\ExtractorCollection;
use Vespakoen\Epi\Stores\ManipulatorStore;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Epi {

	public $eagerLoads = array();

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

	public function setEagerLoads($eagerLoads)
	{
		$this->eagerLoads = $eagerLoads;

		return $this;
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

	public function getCleanInput($input = array())
	{
		$operators = $this->extractors->get('filters')
			->getOperators();

		$filters = array_get($input, 'filter', array());
		foreach ($filters as $key => $value)
		{
			list($operator, $value) = $this->extractors->get('filters')
				->extractOperatorAndValue($value);

			$value = trim($value, '%');

			$input['filter'][$key] = $value;
		}

		return $input;
	}

	protected function getQuery()
	{
		$table = $this->getModel()
			->getTable();

		$query = $this->model->newInstance()
			->distinct()
			->select($table.'.*')
			->with($this->eagerLoads);

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
