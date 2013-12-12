<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\JoinInterface;

class Join extends Manipulator implements JoinInterface {

	public $relationIdentifier;

	public $firstTable;

	public $firstColumn;

	public $operator;

	public $secondTable;

	public $secondColumn;

	public function make($relationIdentifier, $firstTable, $firstColumn, $operator, $secondTable, $secondColumn)
	{
		$this->relationIdentifier = $relationIdentifier;
		$this->firstTable = $firstTable;
		$this->firstColumn = $firstColumn;
		$this->operator = $operator;
		$this->secondTable = $secondTable;
		$this->secondColumn = $secondColumn;

		return $this;
	}

	public function applyTo($query)
	{
		$firstTable = $this->firstTable;
		$safeFirstTable = $this->safe($firstTable);
		$firstColumn = $this->firstColumn;
		$operator = $this->operator;
		$secondTable = $this->secondTable;
		$safeSecondTable = $this->safe($secondTable, true);
		$secondColumn = $this->secondColumn;

		if($secondTable !== $safeSecondTable)
		{
			$secondTable = $secondTable.' as '.$safeSecondTable;
		}

		return $query->join($secondTable, $safeFirstTable.'.'.$firstColumn, $operator, $safeSecondTable.'.'.$secondColumn);
	}

}
