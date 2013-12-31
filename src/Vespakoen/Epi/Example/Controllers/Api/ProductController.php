<?php namespace Vespakoen\Epi\Example\Controllers;

use Vespakoen\Epi\Controllers\EpiController;
use Vespakoen\Epi\Example\Models\Product;

class ProductController extends EpiController {

	/**
	 * Scopes to execute by default
	 *
	 * @var array
	 */
	protected $scopes = array();

	/**
	 * The relations to eager load on every query.
	 *
	 * @var array
	 */
	protected $with = array();

	/**
	 * Validation rules used when getting a list of resources
	 *
	 * @var array
	 */
	protected $indexRules = array(
		'filter.type' => 'in:simple,configurable,grouped',
		'filter.weight' => 'numeric'
	);

	/**
	 * Validation rules used when storing a resource
	 *
	 * @var array
	 */
	protected $storeRules = array(
		'type' => 'required',
	);

	/**
	 * Validation rules used when getting a resource
	 *
	 * @var array
	 */
	protected $showRules = array(
		'format' => 'in:json,xml'
	);

	/**
	 * Validation rules used when updating a resource
	 *
	 * @var array
	 */
	protected $updateRules = array(
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
