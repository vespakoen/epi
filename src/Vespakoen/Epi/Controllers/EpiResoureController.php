<?php namespace Vespakoen\Epi\Controllers;

use Vespakoen\Epi\Api;
use Vespakoen\Epi\Exceptions\ApiException;

use App;
use View;
use Redirect;
use Controller;

class EpiResourceController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		try
		{
			$results = Api::resource($this->resource)
				->passthrough()
				->get();
		}
		catch(ApiException $e)
		{
			return App::abort(404, $e->getMessage());
		}

		return View::make($this->resource.'.index', compact('results'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make($this->resource.'.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = $this->getInput();

		try
		{
			$result = Api::resource($this->resource)
				->insert($input);
		}
		catch(ApiException $e)
		{
			if($e->getCode() == 404)
			{
				return App::abort(404, $e->getMessage());
			}

			return Redirect::back()
				->withErrors($e->getErrors());
		}

		return View::make($this->resource.'.store', compact('result'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try
		{
			$result = Api::resource($this->resource)
				->find($id);
		}
		catch(ApiException $e)
		{
			if($e->getCode() == 404)
			{
				return App::abort(404, $e->getMessage());
			}

			return Redirect::back()
				->withErrors($e->getErrors());
		}

		return View::make($this->resource.'.show', compact('result'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		try
		{
			$result = Api::resource($this->resource)
				->find($id);
		}
		catch(ApiException $e)
		{
			if($e->getCode() == 404)
			{
				return App::abort(404, $e->getMessage());
			}

			return Redirect::back()
				->withErrors($e->getErrors());
		}

		return View::make($this->resource.'.edit', compact('result'));
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

		try
		{
			$result = Api::resource($this->resource)
				->update($input);
		}
		catch(ApiException $e)
		{
			if($e->getCode() == 404)
			{
				return App::abort(404, $e->getMessage());
			}

			return Redirect::back()
				->withErrors($e->getErrors());
		}

		return Redirect::to($this->resource.'.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try
		{
			$result = Api::resource($this->resource)
				->delete($id);
		}
		catch(ApiException $e)
		{
			if($e->getCode() == 404)
			{
				return App::abort(404, $e->getMessage());
			}

			return Redirect::back()
				->withErrors($e->getErrors());
		}

		return View::make($this->resource.'.destroy');
	}

}
