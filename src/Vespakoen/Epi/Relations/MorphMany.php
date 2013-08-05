<?php namespace Vespakoen\Epi\Relations;

class MorphMany extends Relation {

	public function __construct($parent, $table, $key, $foreignTable, $foreignKey, $morphType, $morphClass)
	{
		$this->parent = $parent;
		$this->table = $table;
		$this->key = $key;
		$this->foreignTable = $foreignTable;
		$this->foreignKey = $foreignKey;
		$this->morphType = $morphType;
		$this->morphClass = $morphClass;
	}

	public function applyJoin($query)
	{
		$aliasedTable = is_null($this->parent) ? $this->table : $this->parent->getAliased($this->table);
		$key = $aliasedTable.'.'.$this->key;
		$aliasedForeignTable = $this->getAliased($this->foreignTable);
		$foreignKey = $aliasedForeignTable.'.'.$this->foreignKey;
		$morphType = $aliasedForeignTable.'.'.$this->morphType;
		$morphClass = $this->morphClass;

		$query->join($this->foreignTable.' AS '.$aliasedForeignTable, function($join) use ($foreignKey, $key, $morphType, $morphClass)
		{
			$join->on($foreignKey, '=', $key);

			$join->on($morphType, '=', \DB::raw("'".addslashes($morphClass)."'"));
		});
	}

	public function getTable()
	{
		return $this->getAliased($this->foreignTable);
	}

}
