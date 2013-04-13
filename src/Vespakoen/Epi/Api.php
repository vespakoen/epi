<?php namespace Vespakoen\Epi;

use Vespakoen\Epi\Exceptions\ApiException;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Api {

	/**
	 * The where constraints for the query.
	 *
	 * @var array
	 */
	public $wheres;

	/**
	 * The orderings for the query.
	 *
	 * @var array
	 */
	public $orders;

	/**
	 * The maximum number of records to return.
	 *
	 * @var int
	 */
	public $limit;

	/**
	 * The number of records to skip.
	 *
	 * @var int
	 */
	public $offset;

	/**
	 * $config the configuration
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'<=', '>=', '<>', '!=', '=', '<', '>'
	);

	public function __construct($resource)
	{
		$this->resource = $resource;
		$this->config = Config::get('epi::api');
	}

	public static function resource($resource)
	{
		return new static($resource);
	}

	/**
	 * Add a basic where clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @param  string  $boolean
	 * @return Vespakoen\Epi\Call
	 */
	public function where($column, $operator = null, $value = null)
	{
		// If the given operator is not found in the list of valid operators we will
		// assume that the developer is just short-cutting the '=' operators and
		// we will set the operators to '=' and set the values appropriately.
		if ( ! in_array(strtolower($operator), $this->operators, true))
		{
			list($value, $operator) = array($operator, '=');
		}

		$this->wheres[] = compact('column', 'operator', 'value');

		return $this;
	}

	/**
	 * Add an "order by" clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $direction
	 * @return Vespakoen\Epi\Call
	 */
	public function orderBy($column, $direction = 'asc')
	{
		$this->orders[] = compact('column', 'direction');

		return $this;
	}

	/**
	 * Set the "offset" value of the query.
	 *
	 * @param  int  $value
	 * @return Vespakoen\Epi\Call
	 */
	public function skip($value)
	{
		$this->offset = $value;

		return $this;
	}

	/**
	 * Set the "limit" value of the query.
	 *
	 * @param  int  $value
	 * @return Vespakoen\Epi\Call
	 */
	public function take($value)
	{
		$this->limit = $value;

		return $this;
	}

	/**
	 * Set the limit and offset for a given page.
	 *
	 * @param  int  $page
	 * @param  int  $perPage
	 * @return Vespakoen\Epi\Call
	 */
	public function forPage($page, $perPage = 15)
	{
		return $this->skip(($page - 1) * $perPage)->take($perPage);
	}

	public function find($id)
	{
		return $this->fastRequest($this->resource.'/'.$id, 'GET', $this->getInput());
	}

	public function store($input)
	{
		return $this->fastRequest($this->resource, 'POST', $input);
	}

	public function update($id, $input)
	{
		return $this->fastRequest($this->resource.'/'.$id, 'PUT', $input);
	}

	public function destroy($id)
	{
		return $this->fastRequest($this->resource.'/'.$id, 'DELETE');
	}

	public function get()
	{
		$input = $this->getInput();

		return $this->fastRequest($this->resource, 'GET', $input);
	}

	protected function getInput()
	{
		$input = array();

		if( ! is_null($this->wheres))
		{
			foreach ($this->wheres as $where)
			{
				$input['filter'][$where['column']] = $where['operator'].$where['value'];
			}
		}

		if( ! is_null($this->orders))
		{
			foreach ($this->orders as $order)
			{
				$input['sort'][$order['column']] = $order['direction'];
			}
		}

		if( ! is_null($this->offset))
		{
			$input['offset'] = $this->offset;
		}

		if( ! is_null($this->limit))
		{
			$input['limit'] = $this->limit;
		}

		return $input;
	}

	protected function fastRequest($uri, $method, $input = array())
	{
		$uri = $this->config['prefix'].$uri;
		$method = strtoupper($method);

		if($this->config['local'])
		{
			$oldInput = Input::all();
			Input::replace($input);
			$request = Request::create($uri, $method, array());
			$response = Route::dispatch($request);
			$content = $response->getContent();
			$code = $response->getStatusCode();
			$results = json_decode($content);
			Input::replace($oldInput);
		}
		else
		{
			if($method === 'GET')
			{
				$uri .= '?'.http_build_query($input);
			}

			$client = new Client($this->config['domain']);
			$request = $client->createRequest($method, $uri, array(), $input)
				->addHeader('Content-Type', 'application/json');
			try
			{
				$response = $request->send();
			}
			catch(ClientErrorResponseException $e)
			{
				$response = $e->getResponse();
			}

			$code = $response->getStatusCode();
			$results = $response->json();
		}

		if( ! Str::startsWith((string) $code, "2"))
		{
			throw new ApiException($results, $code);
		}

		return $results;
	}

	public static function __callStatic($method, $arguments)
	{
		return new static($method);
	}

}