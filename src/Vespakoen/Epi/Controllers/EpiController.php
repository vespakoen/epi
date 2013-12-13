<?php namespace Vespakoen\Epi\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use Controller;
use Vespakoen\Epi\Facades\Epi;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;

class EpiController extends Controller {

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

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->fire('before.index');

		$input = $this->getInput();
		$cleanInput = Epi::getCleanInput($input);

		$validator = $this->getIndexValidator($cleanInput);
		if($validator->fails())
		{
			$errors = $validator->messages()
				->getMessages();

			return $this->respond(array(
				'message' => 'Validation failed',
				'errors' => $errors
			), 422);
		}

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

		if( ! $input = $this->getInput())
		{
			$response = array(
				'message' => 'Problems reading input'
			);

			return $this->respond($response, 400);
		}

		$validator = $this->getStoreValidator($input);
		if($validator->fails())
		{
			$errors = $validator->messages()
				->getMessages();

			$response = array(
				'message' => 'Validation failed',
				'errors' => $errors
			);

			return $this->respond($response, 422);
		}

		if( ! $model = $this->model->create($input))
		{
			$response = array(
				'message' => 'Something went wrong, please try it again later'
			);

			return $this->respond($response, 500);
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
			$response = array(
				'message' => 'The resource you are trying to update does not exist'
			);

			return $this->respond($response, 404);
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
			$response = array(
				'message' => 'The resource you are trying to update does not exist'
			);

			return $this->respond($response, 404);
		}

		if( ! $input = $this->getInput())
		{
			$response = array(
				'message' => 'Problems reading input'
			);

			return $this->respond($response, 400);
		}

		$validator = $this->getUpdateValidator($input);
		if($validator->fails())
		{
			$errors = $validator->messages()
				->getMessages();

			$response = array(
				'message' => 'Validation failed',
				'errors' => $errors
			);

			return $this->respond($response, 422);
		}

		if( ! $model->fill($input)->save())
		{
			$response = array(
				'message' => 'Something went wrong, please try it again later'
			);

			return $this->respond($response, 500);
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
			$response = array(
				"message" => "The resource you are trying to delete does not exist"
			);

			return $this->respond($response, 404);
		}

		$model->delete();

		$this->fire('after.destroy', array($id, $model));

		return $this->respond(null, 204);
	}

	protected function getInput()
	{
		return Input::all();
	}

	protected function getIndexValidator($input)
	{
		$validator = Validator::make($input, $this->getIndexRules());

		return $validator;
	}

	protected function getIndexRules()
	{
		return $this->indexRules;
	}

	protected function getStoreValidator($input)
	{
		$validator = Validator::make($input, $this->getStoreRules());

		return $validator;
	}

	protected function getStoreRules()
	{
		return $this->storeRules;
	}

	protected function getUpdateValidator($input)
	{
		$validator = Validator::make($input, $this->getUpdateRules());

		return $validator;
	}

	protected function getUpdateRules()
	{
		return $this->updateRules;
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

	protected function getModel()
	{
		return $this->model;
	}

	protected function respond($result, $status = 200)
	{
		$responseConfig = Config::get('epi::epi.response');
		$formatKey = Config::get('epi::epi.keys.format');

		$formatName = array_get($this->getInput(), $formatKey, $responseConfig['default']);

		$format = App::make('epi::formats.'.$formatName);

		$result = $format->prepare($result);
		$response = $format->respond($result, $status);

		return $response;
	}

}
