<?php namespace Vespakoen\Epi\Relations;

class HasOne extends Relation {

	public function __construct($parent, $table, $key, $foreignTable, $foreignKey)
	{
		$this->parent = $parent;
		$this->table = $table;
		$this->key = $key;
		$this->foreignTable = $foreignTable;
		$this->foreignKey = $foreignKey;
	}

	public function applyJoin($query)
	{
		$firstTable = is_null($this->parent) ? $this->table : $this->parent->getAliased($this->table);
		$first = $firstTable.'.'.$this->key;
		$secondTable = $this->getAliased($this->foreignTable);
		$second = $secondTable.'.'.$this->foreignKey;

		$query->join($this->foreignTable.' AS '.$secondTable, $first, '=', $second);
	}

	public function getTable()
	{
		return $this->getAliased($this->foreignTable);
	}

}