<?php namespace Vespakoen\Epi\Example\Controllers;

use Vespakoen\Epi\Controllers\EpiController;
use Vespakoen\Epi\Example\Models\Product;

class ProductController extends EpiController {

	/**
	 * $eagerLoad == ->with(array('relation', 's'))
	 * @var array
	 */
	public $eagerLoad = array(
	);

	/**
	 * $indexRules Validation rules used when getting a list of products
	 * @var array
	 */
	public $indexRules = array(
		'filter.type' => 'in:simple,configurable,grouped',
		'filter.weight' => 'numeric'
	);

	/**
	 * $storeRules Validation rules used when storing a product
	 * @var array
	 */
	public $storeRules = array(
		'type' => 'required',
	);

	/**
	 * $updateRules Validation rules used when updating a product
	 * @var array
	 */
	public $updateRules = array(
		'type' => 'required',
	);

	/**
	 * Let the reflection do it's work
	 * @param Product $model the Eloquent model
	 */
	public function __construct(Product $model)
	{
		$this->model = $model;

		// We can bind to Epi specific events
		Event::listen('after.update', function($modelName, $id, $model, $input)
		{
			
		});

		// We can bind to Epi specific events for a certain model
		Event::listen('after.update: Vespakoen\Epi\Example\Models\Product', function($id, $model, $input)
		{
			
		});
	}

	/**
	 * We can override a method to add, for example, authorisation
	 */
	public function update($id)
	{
		if(Authority::cannot('update', 'product', $id))
		{
			return Response::json(array(
				'message' => 'You are not allowed to update this product'
			), 401);
		}

		parent::update($id);
	}

}