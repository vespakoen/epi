<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\LimiterInterface;

class Limiter implements LimiterInterface {

	public $skip;

	public $take;

	public function __construct($skip, $take)
	{
		$this->skip = $skip;
		$this->take = $take;
	}

	public static function make($skip = 0, $take = 25)
	{
		return new static($skip, $take);
	}

	public function applyTo($query)
	{
		return $query = $query
			->skip($this->skip)
			->take($this->take);
	}

}
