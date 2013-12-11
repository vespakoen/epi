<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\FilterInterface;

class Filter implements FilterInterface {

	public $relationIdentifier;

	public $column;

	public $operator;

	public $value;

	public function __construct($relationIdentifier, $table, $column, $operator, $value)
	{
		$this->relationIdentifier = $relationIdentifier;
		$this->table = $table;
		$this->column = $column;
		$this->operator = $operator;
		$this->value = $value;
	}

	public static function make($relationIdentifier, $table, $column, $operator, $value)
	{
		return new static($relationIdentifier, $table, $column, $operator, $value);
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
