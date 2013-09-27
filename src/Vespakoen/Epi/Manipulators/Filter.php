<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\FilterInterface;

use Vespakoen\Epi\Helpers\SafeTableName;

class Filter extends Manipulator implements FilterInterface {

	public $relationIdentifier;

	public $column;

	public $operator;

	public $value;

	public function make($relationIdentifier, $column, $operator, $value)
	{
		$this->relationIdentifier = $relationIdentifier;
		$this->column = $column;
		$this->operator = $operator;
		$this->value = $value;
	}

	public function applyTo($query)
	{
		$safeTableNameProvider = $this->epi->getSafeTableNameProvider();

		$table = $safeTableNameProvider->getForRelationIdentifier($relation);

		return $query->where($table.'.'.$this->column, $this->operator, $this->value);
	}

	public function getRelationIdentifier()
	{
		return $this->relationIdentifier;
	}

}
