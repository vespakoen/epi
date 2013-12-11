<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface LimiterInterface extends ManipulatorInterface {

	public static function make($skip, $take);

}
