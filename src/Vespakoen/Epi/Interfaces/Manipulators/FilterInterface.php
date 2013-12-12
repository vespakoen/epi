<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface FilterInterface extends ManipulatorInterface {

	public function make($relationIdentifier, $table, $column, $operator, $value);

	public function getRelationIdentifier();

}
