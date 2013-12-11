<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface JoinInterface extends ManipulatorInterface {

	public static function make($table, $first, $operator, $second);

}
