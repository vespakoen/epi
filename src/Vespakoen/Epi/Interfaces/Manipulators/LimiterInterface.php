<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface LimiterInterface extends ManipulatorInterface {

	public function make($skip, $take);

}
