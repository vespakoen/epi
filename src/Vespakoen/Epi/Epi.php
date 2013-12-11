<?php namespace Vespakoen\Epi;

use Vespakoen\Epi\Interfaces\Extractors\FilterExtractorInterface;
use Vespakoen\Epi\Interfaces\Extractors\SorterExtractorInterface;
use Vespakoen\Epi\Interfaces\Extractors\LimiterExtractorInterface;
use Vespakoen\Epi\Interfaces\Extractors\JoinExtractorInterface;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Epi {

	public $eagerLoads = array();

	public function __construct(FilterExtractorInterface $filterExtractor, SorterExtractorInterface $sorterExtractor, JoinExtractorInterface $joinExtractor, LimiterExtractorInterface $limiterExtractor, $otherExtractors = array())
	{
		$this->filterExtractor = $filterExtractor;
		$this->sorterExtractor = $sorterExtractor;
		$this->limiterExtractor = $limiterExtractor;
		$this->joinExtractor = $joinExtractor;
		$this->otherExtractors = $otherExtractors;
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
		$operators = $this->filterExtractor->getOperators();

		$clean = function($value) use ($operators)
		{
			foreach($operators as $operator)
			{
				$operatorLength = strlen($operator);
				if(substr($value, 0, $operatorLength) == $operator)
				{
					$value = substr($value, strlen($operator));
					break;
				}
			}

			if(Str::startsWith($value, '%') || Str::endsWith($value, '%'))
			{
				return trim($value, '%');
			}

			return $value;
        };

		$filters = array_get($input, 'filter');
		foreach ($filters as $key => $value)
		{
			$input['filter'][$key] = $clean($value);
		}

		return $input;
	}

	protected function getQuery()
	{
		$query = $this->model->newInstance()
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
		$filters = $this->filterExtractor->extract($this->input);
		$sorters = $this->sorterExtractor->extract($this->input);
		$joins = $this->joinExtractor->extract($filters, $sorters);

		$limiters = $this->limiterExtractor->extract($this->input);

		$manipulators = array();
		foreach($this->otherExtractors as $extractor)
		{
			$manipulators += $extractor->extract($this->input);
		}

		return array_merge($joins, $filters, $sorters, $limiters, $manipulators);
	}

}
