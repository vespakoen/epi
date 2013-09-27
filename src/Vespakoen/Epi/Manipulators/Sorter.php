<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\SorterInterface;
use Vespakoen\Epi\Helpers\SafeTableName;

class Sorter extends Manipulator implements SorterInterface {

	public $relationIdentifier;

	public $column;

	public $direction;

	public function make($relationIdentifier, $column, $direction)
	{
		$this->relationIdentifier = $relationIdentifier;
		$this->column = $column;
		$this->direction = $direction;
	}

	public function applyTo($query)
	{
		$table = SafeTableName::getForRelationIdentifier($relation);

		return $query->orderBy($table.'.'.$this->column, $this->direction);
	}

	public function getRelationIdentifier()
	{
		return $this->relationIdentifier;
	}

}
