<?php namespace Vespakoen\Epi\Interfaces;

use Illuminate\Database\Eloquent\Relations\Relation;

interface RelationInterface {

	public function make($parent = null, Relation $relation = null, $relationIdentifier);

	public function getJoins();

	public function getTable();

}
