<?php {{namespace}}

use {{baseController}};

use {{model}};

class {{controller}} extends {{baseControllerName}} {

    /**
     * Create a new {{controller}} instance.
     *
     * @param  {{model}}  $model
     * @return void
     */
    public function __construct({{modelName}} $model)
    {
        $this->model = $model;
    }

    /**
     * $eagerLoad Relations to eagerload by default
     *
     * @var array
     */
    public $eagerLoad = array();

    /**
     * $indexRules Validation rules used when getting a list of resources
     *
     * @var array
     */
    public $indexRules = array();

    /**
     * $storeRules Validation rules used when storing a resource
     *
     * @var array
     */
    public $storeRules = array();

    /**
     * $updateRules Validation rules used when updating a resource
     *
     * @var array
     */
    public $updateRules = array();

}
