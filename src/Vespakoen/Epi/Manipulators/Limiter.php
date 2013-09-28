<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\LimiterInterface;

class Limiter implements LimiterInterface {

	public $skip;

	public $take;

	public function make($skip, $take)
	{
		$this->skip = $skip;
		$this->take = $take;

		return $this;
	}

	public function applyTo($query)
	{
		return $query->skip($this->skip)
			->take($this->take);
	}

}
