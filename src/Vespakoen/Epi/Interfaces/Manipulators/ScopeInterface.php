<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface ScopeInterface extends ManipulatorInterface {

	public function make($model, $scope);

}
