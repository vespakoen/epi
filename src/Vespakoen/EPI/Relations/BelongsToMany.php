<?php namespace Vespakoen\EPI\Relations;

class BelongsToMany extends Relation {

	public function __construct($parent, $table, $key, $intermediateTable, $intermediateKey, $intermediateOtherKey, $foreignTable, $foreignKey)
	{
		$this->parent = $parent;
		$this->table = $table;
		$this->key = $key;
		$this->intermediateTable = $intermediateTable;
		$this->intermediateKey = $intermediateKey;
		$this->intermediateOtherKey = $intermediateOtherKey;
		$this->foreignTable = $foreignTable;
		$this->foreignKey = $foreignKey;
	}

	public function applyJoin($query)
	{
		$firstTable = is_null($this->parent) ? $this->table : $this->getAliased($this->table);
		$first = $firstTable.'.'.$this->key;
		$secondTable = $this->getAliased($this->intermediateTable);
		$second = $secondTable.'.'.$this->intermediateKey;

		$query->join($this->intermediateTable.' AS '.$secondTable, $first, '=', $second);

		// @todo this isn't necessary in all cases, optimize later
		$firstTable = $this->getAliased($this->intermediateTable);
		$first = $firstTable.'.'.$this->intermediateOtherKey;
		$secondTable = $this->getAliased($this->foreignTable);
		$second = $secondTable.'.'.$this->foreignKey;

		$query->join($this->foreignTable.' AS '.$secondTable, $first, '=', $second);
	}

	public function getTable()
	{
		return $this->getAliased($this->foreignTable);
	}

}