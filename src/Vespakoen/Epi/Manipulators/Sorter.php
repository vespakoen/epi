<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\SorterInterface;

class Sorter implements SorterInterface {

	public $relationIdentifier;

	public $column;

	public $direction;

	public function __construct($relationIdentifier, $table, $column, $direction)
	{
		$this->relationIdentifier = $relationIdentifier;
		$this->table = $table;
		$this->column = $column;
		$this->direction = $direction;
	}

	public static function make($relationIdentifier, $table, $column, $direction)
	{
		return new static($relationIdentifier, $table, $column, $direction);
	}

	public function applyTo($query)
	{
		return $query->orderBy($this->table.'.'.$this->column, $this->direction);
	}

	public function getRelationIdentifier()
	{
		return $this->relationIdentifier;
	}

}
