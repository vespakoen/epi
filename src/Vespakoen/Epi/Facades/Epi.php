<?php namespace Vespakoen\Epi\Facades;

use Illuminate\Support\Facades\Facade;

class Epi extends Facade {

    /**
    * Get the registered component.
    *
    * @return object
    */
    protected static function getFacadeAccessor()
    {
        return 'epi::epi';
    }

}
