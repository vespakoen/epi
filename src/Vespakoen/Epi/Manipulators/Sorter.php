<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\SorterInterface;

class Sorter extends Manipulator implements SorterInterface {

	public $relationIdentifier;

	public $column;

	public $direction;

	public function make($relationIdentifier, $table, $column, $direction)
	{
		$this->relationIdentifier = $relationIdentifier;
		$this->table = $table;
		$this->column = $column;
		$this->direction = $direction;

		return $this;
	}

	public function applyTo($query)
	{
		$safeTable = $this->safe($this->table);

		return $query->orderBy($safeTable.'.'.$this->column, $this->direction);
	}

	public function getRelationIdentifier()
	{
		return $this->relationIdentifier;
	}

}
