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

/**
 * The EpiController is a basic implementation for laravel's resource controller.
 *
 * The simplest and recommended way to use Epi is by creating a controller for all your
 * resources that extend the EpiController.
 *
 * You can very easily change the validator or the validation rules for any resource action
 *
 * by setting the properies, or extending the methods on your controller.
 *
 * To add authentication, you can use laravel's filters, extend the resource method
 * and call the parent resource method after authenticating the user, or by listening
 * to the events the EpiController calls for you when it is in an action.
 *
 * The available events are:
 * 	before.index	gets called before doing anything in the index method
 * 	after.index	gets called after the results are found, arguments passed through the event are ($results)
 * 	before.store	gets called before doing anything in the store method
 * 	after.store	gets called after the object is stored, arguments passed through the event are ($model, $input)
 * 	before.show	gets called before doing anything in the show method, arguments passed through the event are ($id)
 * 	after.show	gets called after the model is found, arguments passed through the event are ($id, $model)
 * 	before.update	gets called before doing anything in the update method, arguments passed through the event are ($id)
 * 	after.update	gets called after the object is updated, arguments passed through the event are ($id, $model, $input)
 * 	before.destroy	gets called before doing anything in the destroy method, arguments passed through the event are ($id)
 * 	after.destroy	gets called after the model is destroyed, arguments passed through the event are ($id, $model)
 */
class EpiController extends Controller {

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
	protected $indexRules = array();

	/**
	 * Validation rules used when storing a resource
	 *
	 * @var array
	 */
	protected $storeRules = array();

	/**
	 * Validation rules used when getting a resource
	 *
	 * @var array
	 */
	protected $showRules = array();

	/**
	 * Validation rules used when updating a resource
	 *
	 * @var array
	 */
	protected $updateRules = array();

	/**
	 * Input to filter out in the index method
	 *
	 * @var array
	 */
	protected $indexInputFilter = array(
		'filter',
		'sort',
		'skip',
		'take',
		'with',
		'scopes',
		'format',
		'response'
	);

	/**
	 * Input to filter out in the show method
	 *
	 * @var array
	 */
	protected $showInputFilter = array(
		'with',
		'scopes',
		'format',
		'response'
	);

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->fire('before.index');

		$input = $this->getInput($this->indexInputFilter);

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

		$results = $this->makeEpi($input)
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

		$input = $this->getInput($this->showInputFilter);

		$cleanInput = Epi::getCleanInput($input);

		$validator = $this->getShowValidator($cleanInput);
		if($validator->fails())
		{
			$errors = $validator->messages()
				->getMessages();

			return $this->respond(array(
				'message' => 'Validation failed',
				'errors' => $errors
			), 422);
		}

		$model = $this->makeEpi($input)
			->addInput('filter', array(
				'id' => $id
			))
			->first();

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

	/**
	 * Get the model to use for Epi
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	protected function getModel()
	{
		return $this->model;
	}

	/**
	 * Get the input
	 *
	 * @return array
	 */
	protected function getInput($filter = null)
	{
		$input = Input::all();

		return ! is_null($filter) ? array_only($input, $filter) : $input;
	}

	/**
	 * Get the scopes
	 *
	 * @return array
	 */
	protected function getScopes()
	{
		return $this->scopes;
	}

	/**
	 * Get the eagerloads
	 *
	 * @return array
	 */
	protected function getEagerloads()
	{
		return $this->with;
	}

	/**
	 * Get the validator object for the index action
	 *
	 * @return \Illuminate\Support\Contracts\MessageProviderInterface
	 */
	protected function getIndexValidator($input)
	{
		$validator = Validator::make($input, $this->getIndexRules());

		return $validator;
	}

	/**
	 * Get the rules for the index validator
	 *
	 * @return array
	 */
	protected function getIndexRules()
	{
		return $this->indexRules;
	}

	/**
	 * Get the validator object for the store action
	 *
	 * @return \Illuminate\Support\Contracts\MessageProviderInterface
	 */
	protected function getStoreValidator($input)
	{
		$validator = Validator::make($input, $this->getStoreRules());

		return $validator;
	}

	/**
	 * Get the rules for the store validator
	 *
	 * @return array
	 */
	protected function getStoreRules()
	{
		return $this->storeRules;
	}

	/**
	 * Get the validator object for the show action
	 *
	 * @return \Illuminate\Support\Contracts\MessageProviderInterface
	 */
	protected function getShowValidator($input)
	{
		$validator = Validator::make($input, $this->getShowRules());

		return $validator;
	}

	/**
	 * Get the rules for the show validator
	 *
	 * @return array
	 */
	protected function getShowRules()
	{
		return $this->showRules;
	}

	/**
	 * Get the validator object for the update action
	 *
	 * @return \Illuminate\Support\Contracts\MessageProviderInterface
	 */
	protected function getUpdateValidator($input)
	{
		$validator = Validator::make($input, $this->getUpdateRules());

		return $validator;
	}

	/**
	 * Get the rules for the update validator
	 *
	 * @return array
	 */
	protected function getUpdateRules()
	{
		return $this->updateRules;
	}

	/**
	 * Get the adaptor to use based on the input
	 * with a fallback to the config
	 *
	 * @return string The adaptor to use
	 */
	protected function getAdaptor()
	{
		$input = $this->getInput();

		$defaultAdaptor = Config::get('epi::epi.adaptor');
		$adaptorKey = Config::get('epi::epi.keys.adaptor');

		return array_key_exists($adaptorKey, $input) ? $input[$adaptorKey] : $defaultAdaptor;
	}

	/**
	 * Get the format to use based on the input
	 * with a fallback to the config
	 *
	 * @return string The format to use
	 */
	protected function getFormat()
	{
		$input = $this->getInput();

		$defaultFormat = Config::get('epi::epi.format');
		$formatKey = Config::get('epi::epi.keys.format');

		return array_key_exists($formatKey, $input) ? $input[$formatKey] : $defaultFormat;
	}

	/**
	 * Get an epi instance for the model set on this controller.
	 * It will automatically apply default scopes and with's that
	 * might be set on the controller
	 *
	 * @param  array $input
	 * @return Vespakoen\Epi\Epi
	 */
	protected function makeEpi($input)
	{
		$model = $this->getModel();

		return Epi::make($model, $input)
			->addInput('scopes', $this->getScopes())
			->addInput('with', $this->getEagerloads());
	}

	/**
	 * Adapt the result
	 *
	 * \Illuminate\Database\Eloquent\Model|Illuminate\Support\Collection $result
	 * @param  string $adaptor the adaptor name within the epi::adaptors namespace
	 *
	 * @return \Illuminate\Database\Eloquent\Model|Illuminate\Support\Collection
	 */
	protected function adaptResult($result, $adaptor)
	{
		if( ! $adaptor)
		{
			return $result;
		}

		$adaptor = App::make('epi::adaptors.'.$adaptor);

		return $adaptor->adapt($result);
	}

	/**
	 * Format the result
	 *
	 * @param  \Illuminate\Database\Eloquent\Model|Illuminate\Support\Collection $result
	 *
	 * @return \Illuminate\Database\Eloquent\Model|Illuminate\Support\Collection
	 */
	protected function getFormatter()
	{
		$format = $this->getFormat();

		return App::make('epi::formats.'.$format);
	}

	/**
	 * Response method that adapts and formats the output
	 *
	 * @param  \Illuminate\Database\Eloquent\Model|Illuminate\Support\Collection|array  $result The result
	 * @param  integer $status The HTTP status code
	 *
	 * @return Illuminate\Support\Facades\Response
	 */
	protected function respond($result, $status = 200)
	{
		if($result instanceof Collection || $result instanceof Model)
		{
			$result = $this->adaptResult($result, $this->getAdaptor());
		}

		$formatter = $this->getFormatter();

		$result = $formatter->prepare($result);

		return $formatter->respond($result, $status);
	}

	/**
	 * Helper for firing 2 events, with a different name and arguments
	 *
	 * @param  string $event     The name of the event to fire
	 * @param  array  $arguments The arguments for the event
	 *
	 * @return Void
	 */
	protected function fire($event, $arguments = array())
	{
		$model = get_class($this->model);

		Event::fire($event.': '.$model, $arguments);

		array_unshift($arguments, $model);
		Event::fire($event, $arguments);
	}

}
