<?php namespace Vespakoen\Epi\Exceptions;

use Exception;

/**
 * Thrown when an API call returns an exception.
 *
 * @author Koen Schmeets <hello@koenschmeets.nl>
 */
class ApiException extends Exception
{
	/**
	 * The result from the API server that represents the exception information.
	 */
	protected $result;

	/**
	 * Make a new API Exception with the given result.
	 *
	 * @param array $result The result from the API server
	 */
	public function __construct($result, $code)
	{
		$this->result = $result;

		if (isset($result->message))
		{
			$message = $result->message;
		}
		else
		{
			$message = 'Unknown Error.';
		}

		parent::__construct($message, $code);
	}

	/**
	 * Return the associated result object returned by the API server.
	 *
	 * @return array The result from the API server
	 */
	public function getResult()
	{
		return $this->result;
	}

}
