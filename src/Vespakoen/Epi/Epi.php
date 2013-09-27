<?php namespace Vespakoen\Epi;

use Vespakoen\Epi\Interfaces\Extractors\FilterExtractorInterface;
use Vespakoen\Epi\Interfaces\Extractors\SorterExtractorInterface;
use Vespakoen\Epi\Interfaces\Extractors\LimiterExtractorInterface;
use Vespakoen\Epi\Interfaces\Extractors\JoinExtractorInterface;

use Illuminate\Database\Eloquent\Model;

class Epi {

	public $eagerLoads = array();

	public function __construct(FilterExtractorInterface $filterExtractor, SorterExtractorInterface $sorterExtractor, LimiterExtractorInterface $limiterExtractor, JoinExtractorInterface $joinExtractor)
	{
		$this->filterExtractor = $filterExtractor;
		$this->sorterExtractor = $sorterExtractor;
		$this->limiterExtractor = $limiterExtractor;
		$this->joinExtractor = $joinExtractor;
	}

	public function make(Model $model, array $input)
	{
		$this->model = $model;
		$this->input = $input;

		return $this;
	}

	public function setModel(Model $model)
	{
		$this->model = $model;

		return $this;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function setInput(array $input)
	{
		$this->input = $input;

		return $this;
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

	protected function getQuery()
	{
		$query = $this->model->with($this->eagerLoads);

		$manipulators = $this->getManipulators();
		foreach($manipulators as $manipulator)
		{
			$query = $manipulator->applyTo($query);
		}

		return $query;
	}

	protected function getManipulators()
	{
		$filters = $this->filterExtractor->extract($this->input);
		$sorters = $this->sorterExtractor->extract($this->input);
		$limiters = $this->limiterExtractor->extract($this->input);
		$joins = $this->joinExtractor->extract($filters, $sorters);

		return array_merge($joins, $filters, $sorters, $limiters);
	}

}
