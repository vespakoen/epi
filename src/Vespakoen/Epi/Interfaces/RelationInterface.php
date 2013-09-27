<?php namespace Vespakoen\Epi\Interfaces;

use Illuminate\Database\Eloquent\Relations\Relation;

interface RelationInterface {

	public function __construct(RelationInterface $parent = null, Relation $relation);

	public function applyJoins($query);

	public function getTable();

}
