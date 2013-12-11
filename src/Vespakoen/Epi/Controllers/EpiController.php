<?php namespace Vespakoen\Epi\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use Controller;
use Vespakoen\Epi\Facades\Epi;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class EpiController extends Controller {

	/**
	 * $eagerLoad Relations to eagerload by default
	 *
	 * @var array
	 */
	public $eagerLoad = array();

	/**
	 * $htore Path to hstore columns for filtering support
	 *
	 * @var array
	 */
	public $hstore = array();

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

	public function getInput()
	{
		return Input::all();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->fire('before.index');

		$validator = Validator::make($this->getInput(), $this->indexRules);
		if($validator->fails())
		{
			$errors = $validator->messages()
				->getMessages();

			$response = Response::json(array(
				'message' => 'Validation failed',
				'errors' => $errors
			), 422);

			return $this->respond($response);
		}

		$input = $this->getInput();
		$model = $this->getModel();

		$results = Epi::make($model, $input)
			->setEagerloads($this->eagerLoad)
			->get();

		$this->fire('after.index', array($results));

		return $this->respond($results);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$this->fire('before.store');

		if( ! $input = Input::all())
		{
			$response = Response::json(array(
				'message' => 'Problems reading input'
			), 400);

			return $this->respond($response);
		}

		$validator = Validator::make($input, $this->storeRules);
		if($validator->fails())
		{
			$errors = $validator->messages()
				->getMessages();

			$response = Response::json(array(
				'message' => 'Validation failed',
				'errors' => $errors
			), 422);

			return $this->respond($response);
		}

		if( ! $model = $this->model->create($input))
		{
			$response = Response::json(array(
				'message' => 'Something went wrong, please try it again later'
			), 500);

			return $this->respond($response);
		}

		$this->fire('after.store', array($model, $input));

		return $this->respond($model);
	}

	/**
	 * Display the specified resource.
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$this->fire('before.show', array($id));

		$model = $this->model
			->with($this->eagerLoad)
			->find($id);

		if( ! $model)
		{
			$response = Response::json(array(
				'message' => 'The resource you are trying to update does not exist'
			), 404);

			return $this->respond($response);
		}

		$this->fire('after.show', array($id, $model));

		return $this->respond($model);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @return Response
	 */
	public function update($id)
	{
		$this->fire('before.update', array($id));

		$model = $this->model->find($id);

		if( ! $model)
		{
			$response = Response::json(array(
				'message' => 'The resource you are trying to update does not exist'
			), 404);

			return $this->respond($response);
		}

		if( ! $input = Input::all())
		{
			$response = Response::json(array(
				'message' => 'Problems reading input'
			), 400);

			return $this->respond($response);
		}

		$validator = Validator::make($input, $this->updateRules);
		if($validator->fails())
		{
			$errors = $validator->messages()
				->getMessages();

			$response = Response::json(array(
				'message' => 'Validation failed',
				'errors' => $errors
			), 422);

			return $this->respond($response);
		}

		if( ! $model->fill($input)->save())
		{
			$response = Response::json(array(
				'message' => 'Something went wrong, please try it again later'
			), 500);

			return $this->respond($response);
		}

		$this->fire('after.update', array($id, $model, $input));

		return $this->respond($model);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->fire('before.destroy', array($id));

		$model = $this->model->find($id);

		if( ! $model)
		{
			$response = Response::json(array(
				"message" => "The resource you are trying to delete does not exist"
			), 404);

			return $this->respond($response);
		}

		$model->delete();

		$this->fire('after.destroy', array($id, $model));

		return Response::json(null, 204);
	}

	/**
	 * Helper for firing 2 events, with a different name and arguments
	 *
	 * @param  string $event     The name of the event to fire
	 * @param  array  $arguments The arguments for the event
	 * @return Void
	 */
	protected function fire($event, $arguments = array())
	{
		$model = get_class($this->model);

		Event::fire($event.': '.$model, $arguments);

		array_unshift($arguments, $model);
		Event::fire($event, $arguments);
	}

	/**
	 * Helper for prettyprinting the response
	 *
	 * @param  result The eloquent collection or model
	 * @return string The JSON
	 */
	protected function respond($result)
	{
		if(Input::has('prettyprint'))
		{
			if($result instanceof Model || $result instanceof Collection)
			{
				$result = $result->toArray();
			}

			$result = Response::make(json_encode($result, JSON_PRETTY_PRINT), 200, array('content-type' => 'application/json'));
		}

		return $result;
	}

	protected function getModel()
	{
		return $this->model;
	}

}
