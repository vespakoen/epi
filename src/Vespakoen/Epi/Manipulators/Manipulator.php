<?php namespace Vespakoen\Epi\Manipulators;

use Vespakoen\Epi\Interfaces\Manipulators\ManipulatorInterface;

abstract class Manipulator implements ManipulatorInterface {

	public function __construct($epi, $config)
	{
		$this->epi = $epi;
		$this->config = $config;
	}

}
