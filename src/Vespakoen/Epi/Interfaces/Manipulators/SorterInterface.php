<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface SorterInterface extends ManipulatorInterface {

	public static function make($relationIdentifier, $table, $column, $direction);

	public function getRelationIdentifier();

}
