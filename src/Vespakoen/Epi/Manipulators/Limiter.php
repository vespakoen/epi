<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\LimiterInterface;

class Limiter extends Manipulator implements LimiterInterface {

	public $skip;

	public $take;

	public function make($skip = 0, $take = 25)
	{
		$this->skip = $skip;
		$this->take = $take;

		return $this;
	}

	public function applyTo($query)
	{
		return $query = $query
			->skip($this->skip)
			->take($this->take);
	}

}
