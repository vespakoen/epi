<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\FilterInterface;

class Filter implements FilterInterface {

	public $relationIdentifier;

	public $column;

	public $operator;

	public $value;

	public function make($relationIdentifier, $table, $column, $operator, $value)
	{
		$this->relationIdentifier = $relationIdentifier;
		$this->table = $table;
		$this->column = $column;
		$this->operator = $operator;
		$this->value = $value;

		return $this;
	}

	public function applyTo($query)
	{
		return $query->where($this->table.'.'.$this->column, $this->operator, $this->value);
	}

	public function getRelationIdentifier()
	{
		return $this->relationIdentifier;
	}

}
