<?php namespace Vespakoen\Epi\Formats;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class Json extends Format {

	public function respond($result, $status)
	{
		if($result instanceof Model || $result instanceof Collection)
		{
			$result = $result->toArray();
		}

		if(Input::has('prettyprint'))
		{
			return Response::make(json_encode($result, JSON_PRETTY_PRINT), $status, array('content-type' => 'application/json'));
		}

		return Response::json($result, $status);
	}

}
