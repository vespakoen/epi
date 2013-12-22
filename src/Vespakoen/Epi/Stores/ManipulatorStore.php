<?php namespace Vespakoen\Epi\Stores;

class ManipulatorStore {

	protected $manipulators = array();

	public function add($key, $manipulators)
	{
		$this->manipulators[$key] = $manipulators;
	}

	public function get($key)
	{
		return $this->has($key) ? $this->manipulators[$key] : null;
	}

	public function has($key)
	{
		return array_key_exists($key, $this->manipulators);
	}

	public function all()
	{
		$results = array();
		foreach($this->manipulators as $manipulators)
		{
			$results = array_merge($results, $manipulators);
		}

		return $results;
	}

	public function getManipulatorsForJoinExtractor()
	{
		$filters = $this->get('filters');
		$sorters = $this->get('sorters');

		return array_merge($filters, $sorters);
	}

}
