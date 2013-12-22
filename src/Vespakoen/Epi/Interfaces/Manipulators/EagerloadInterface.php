<?php namespace Vespakoen\Epi\Interfaces\Manipulators;

interface EagerloadInterface extends ManipulatorInterface {

    public function make($relationIdentifier);

}
