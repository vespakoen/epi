<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\FilterInterface;

class Filter extends Manipulator implements FilterInterface {

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
		$table = $this->table;
		$safeTable = $this->safe($table, true);
		$column = $this->column;
		$operator = $this->operator;
		$value = $this->value;

		return $query->where($safeTable.'.'.$column, $operator, $value);
	}

	public function getRelationIdentifier()
	{
		return $this->relationIdentifier;
	}

}
