<?php namespace Vespakoen\Epi\Manipulators;

use Exception;

use Vespakoen\Epi\Interfaces\Manipulators\EagerloadInterface;

class Eagerload extends Manipulator implements EagerloadInterface {

    public $relationIdentifier;

    public function make($relationIdentifier)
    {
        $this->relationIdentifier = $relationIdentifier;

        return $this;
    }

    public function applyTo($query)
    {
        return $query->with($this->relationIdentifier);
    }

}
