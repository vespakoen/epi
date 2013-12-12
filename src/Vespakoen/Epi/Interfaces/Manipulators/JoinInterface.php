<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface JoinInterface extends ManipulatorInterface {

	public function make($relationIdentifier, $firstTable, $firstColumn, $operator, $secondTable, $secondColumn);

}
