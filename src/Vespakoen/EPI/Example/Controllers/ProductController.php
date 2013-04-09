<?php namespace Vespakoen\EPI\Example\Controllers;

use Vespakoen\EPI\Controllers\EPIController;
use Vespakoen\EPI\Example\Models\Product;

class ProductController extends EPIController {

	/**
	 * $eagerLoad == ->with(array('relation', 's'))
	 * @var array
	 */
	public $eagerLoad = array(
	);

	/**
	 * $storeRules Validation rules used when storing the product
	 * @var array
	 */
	public $storeRules = array(
		'type' => 'required',
	);

	/**
	 * $storeRules Validation rules used when updating the product
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

		// We can bind to EPI specific events
		Event::listen('after.update', function($modelName, $id, $model, $input)
		{
			
		});

		// We can bind to EPI specific events for a certain model
		Event::listen('after.update: Vespakoen\EPI\Example\Models\Product', function($id, $model, $input)
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