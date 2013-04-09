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
	}

	/**
	 * You can override the methods if you like
	 */

}