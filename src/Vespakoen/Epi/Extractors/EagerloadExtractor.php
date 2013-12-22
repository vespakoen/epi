<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Interfaces\Extractors\EagerloadExtractorInterface;

class EagerloadExtractor extends Extractor implements EagerloadExtractorInterface {

    public function extract(array $input)
    {
        // where in the input should we look?
        $key = $this->config['keys']['eagerloads'];

        // is there a sort at all?
        if( ! array_key_exists($key, $input))
        {
            return array();
        }

        // make some eagerloads!
        $eagerloads = array();
        foreach($input[$key] as $relationIdentifier)
        {
            $eagerload = $this->app->make('epi::manipulators.eagerload');
            $eagerloads[] = $eagerload->make($relationIdentifier);
        }

        return $eagerloads;
    }

}
