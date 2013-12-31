<?php namespace Vespakoen\Epi\Interfaces\Extractors;

interface EagerloadExtractorInterface {

    /**
     * Extracts eagerloads from the given input
     *
     * @param  array
     * @return array of Epi\Manipulators\Eagerload
     */
    public function extract(array $input);

}
