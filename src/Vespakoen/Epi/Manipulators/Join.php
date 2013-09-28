<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\JoinInterface;

class Join implements JoinInterface {

	public $table;

	public $first;

	public $operator;

	public $second;

	public function make($table, $first, $operator, $second)
	{
		$this->table = $table;
		$this->first = $first;
		$this->operator = $operator;
		$this->second = $second;

		return $this;
	}

	public function applyTo($query)
	{
		return $query->join($this->table, $this->first, $this->operator, $this->second);
	}

}
