<?php namespace Vespakoen\Epi\Interfaces;

use Illuminate\Database\Eloquent\Relations\Relation as LaravelRelation;

interface RelationInterface {

	public function make($parent = null, LaravelRelation $relation = null, $relationIdentifier);

	public function getJoins();

	public function getTable();

}
