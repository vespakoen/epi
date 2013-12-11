<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface FilterInterface extends ManipulatorInterface {

	public static function make($relationIdentifier, $table, $column, $operator, $value);

	public function getRelationIdentifier();

}
