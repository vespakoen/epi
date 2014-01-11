<?php namespace Vespakoen\Epi\Controllers;

use Vespakoen\Epi\Api;
use Vespakoen\Epi\Exceptions\ApiException;

use App;
use View;
use Redirect;
use Controller;
use Input;
use Str;

class EpiResourceController extends Controller {

	/**
	 * The package to load views from
	 *
	 * @var string
	 */
	protected $package;

	/**
	 * The name of the resource
	 *
	 * @var string
	 */
	protected $resource;

	/**
	 * The singular name of the resource
	 *
	 * @var string
	 */
	protected $resourceSingular;

	/**
	 * The path to load views from
	 *
	 * @var string
	 */
	protected $viewPath;

	/**
	 * Send input through to API
	 *
	 * @var boolean
	 */
	protected $passthrough = true;

	/**
	 * Get the view prefixed with the package and resource
	 *
	 * @param  string $view
	 *
	 * @return string
	 */
	public function getViewFor($view)
	{
		$prefix = (is_null($this->package) ? '' : $this->package.'::');

		$resource = is_null($this->viewPath) ? $this->resource : $this->viewPath;

		return $prefix.$resource.'.'.$view;
	}

	/**
	 * Get the input
	 *
	 * @return array
	 */
	public function getInput()
	{
		return Input::all();
	}

	/**
	 * Get resources for the index action
	 *
	 * @return array
	 */
	public function getIndexResources()
	{
		return array(
			$this->resource => function($api)
			{
				return $api->get();
			}
		);
	}

	/**
	 * Get resources for the show action
	 *
	 * @return array
	 */
	public function getShowResources()
	{
		return array(
			$this->getSingularResource() => function($api, $id)
			{
				return $api->find($id);
			}
		);
	}

	/**
	 * Get resources for the create action
	 *
	 * @return array
	 */
	public function getCreateResources()
	{
		return array();
	}

	/**
	 * Get resources for the store action
	 *
	 * @return array
	 */
	public function getStoreResources()
	{
		return array(
			$this->getSingularResource() => function($api, $input)
			{
				return $api->insert($input);
			}
		);
	}

	/**
	 * Get resources for the update action
	 *
	 * @return array
	 */
	public function getUpdateResources()
	{
		return array(
			$this->getSingularResource() => function($api, $input)
			{
				return $api->update($input);
			}
		);
	}

	/**
	 * Get resources for the destroy action
	 *
	 * @return array
	 */
	public function getDestroyResources()
	{
		return array(
			$this->getSingularResource() => function($api, $id)
			{
				$api->destroy($id);
			}
		);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$input = $this->getInput();

		$errors = $results = array();
		foreach($this->getIndexResources() as $resource => $resolver)
		{
			if(is_int($resource))
			{
				$resource = $resolver;

				$resolver = function($api)
				{
					return $api->get();
				};
			}

			$api = $this->getApiForResource($resource);

			try
			{
				$results[$resource] = $resolver($api, $input);
			}
			catch(ApiException $e)
			{
				if($e->getCode() == 404)
				{
					return App::abort(404, $e->getMessage());
				}

				$errors = array_merge($errors, $e->getErrors());
			}
		}

		if(count($errors) > 0)
		{
			return Redirect::back()
				->withErrors($errors);
		}

		return View::make($this->getViewFor('index'), $results);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$input = $this->getInput();

		$errors = $results = array();
		foreach($this->getCreateResources() as $resource => $resolver)
		{
			if(is_int($resource))
			{
				$resource = $resolver;

				$resolver = function($api)
				{
					return $api->get();
				};
			}

			$api = $this->getApiForResource($resource);

			try
			{
				$results[$resource] = $resolver($api, $input);
			}
			catch(ApiException $e)
			{
				if($e->getCode() == 404)
				{
					return App::abort(404, $e->getMessage());
				}

				$errors = array_merge($errors, $e->getErrors());
			}
		}

		if(count($errors) > 0)
		{
			return Redirect::back()
				->withErrors($errors);
		}

		return View::make($this->getViewFor('create'), $results);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = $this->getInput();

		$errors = $results = array();
		foreach($this->getStoreResources() as $resource => $resolver)
		{
			if(is_int($resource))
			{
				$resource = $resolver;

				$resolver = function($api)
				{
					return $api->get();
				};
			}

			$api = $this->getApiForResource($resource);

			try
			{
				$results[$resource] = $resolver($api, $input);
			}
			catch(ApiException $e)
			{
				if($e->getCode() == 404)
				{
					return App::abort(404, $e->getMessage());
				}

				$errors = array_merge($errors, $e->getErrors());
			}
		}

		if(count($errors) > 0)
		{
			return Redirect::back()
				->withErrors($errors);
		}

		return View::make($this->getViewFor('store'), $results);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$input = $this->getInput();

		$errors = $results = array();
		foreach($this->getShowResources() as $resource => $resolver)
		{
			if(is_int($resource))
			{
				$resource = $resolver;

				$resolver = function($api)
				{
					return $api->get();
				};
			}

			$api = $this->getApiForResource($resource);

			try
			{
				$results[$resource] = $resolver($api, $id, $input);
			}
			catch(ApiException $e)
			{
				if($e->getCode() == 404)
				{
					return App::abort(404, $e->getMessage());
				}

				$errors = array_merge($errors, $e->getErrors());
			}
		}

		if(count($errors) > 0)
		{
			return Redirect::back()
				->withErrors($errors);
		}

		return View::make($this->getViewFor('show'), $results);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$input = $this->getInput();

		$errors = $results = array();
		foreach($this->getShowResources() as $resource => $resolver)
		{
			if(is_int($resource))
			{
				$resource = $resolver;

				$resolver = function($api)
				{
					return $api->get();
				};
			}

			$api = $this->getApiForResource($resource);

			try
			{
				$results[$resource] = $resolver($api, $id, $input);
			}
			catch(ApiException $e)
			{
				if($e->getCode() == 404)
				{
					return App::abort(404, $e->getMessage());
				}

				$errors = array_merge($errors, $e->getErrors());
			}
		}

		if(count($errors) > 0)
		{
			return Redirect::back()
				->withErrors($errors);
		}

		return View::make($this->getViewFor('edit'), $results);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = $this->getInput();

		$errors = $results = array();
		foreach($this->getUpdateResources() as $resource => $resolver)
		{
			if(is_int($resource))
			{
				$resource = $resolver;

				$resolver = function($api)
				{
					return $api->get();
				};
			}

			$api = $this->getApiForResource($resource);

			try
			{
				$results[$resource] = $resolver($api, $id, $input);
			}
			catch(ApiException $e)
			{
				if($e->getCode() == 404)
				{
					return App::abort(404, $e->getMessage());
				}

				$errors = array_merge($errors, $e->getErrors());
			}
		}

		if(count($errors) > 0)
		{
			return Redirect::back()
				->withErrors($errors);
		}

		return View::make($this->getViewFor('update'), $results);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$input = $this->getInput();

		$errors = $results = array();
		foreach($this->getDestroyResources() as $resource => $resolver)
		{
			if(is_int($resource))
			{
				$resource = $resolver;

				$resolver = function($api)
				{
					return $api->get();
				};
			}

			$api = $this->getApiForResource($resource);

			try
			{
				$results[$resource] = $resolver($api, $id, $input);
			}
			catch(ApiException $e)
			{
				if($e->getCode() == 404)
				{
					return App::abort(404, $e->getMessage());
				}

				$errors = array_merge($errors, $e->getErrors());
			}
		}

		if(count($errors) > 0)
		{
			return Redirect::back()
				->withErrors($errors);
		}

		return View::make($this->getViewFor('destroy'), $results);
	}

	/**
	 * Get an API instance for a resource
	 *
	 * @return \Vespakoen\Epi\Api
	 */
	protected function getApiForResource($resource)
	{
		$api = Api::resource($resource);

		if($this->passthrough)
		{
			$api->passthrough();
		}

		return $api;
	}

	/**
	 * Get the singular resource name
	 *
	 * @return string
	 */
	protected function getSingularResource()
	{
		return is_null($this->resourceSingular) ? Str::singular($this->resource) : $resource;
	}


}
