<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface SorterInterface extends ManipulatorInterface {

	public function make($relationIdentifier, $column, $direction);

	public function getRelationIdentifier();

}
