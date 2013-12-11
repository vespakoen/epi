<?php namespace Vespakoen\Epi\Example\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Product extends Eloquent {

	public $sequence = 'products_id_seq';

	public $table = 'products';

	public function getCreatedAtAttribute($value)
	{
		if(is_string($value))
		{
			return $value;
		}

		return $value->format("Y-m-d H:i:s");
	}

	public function getUpdatedAtAttribute($value)
	{
		if(is_string($value))
		{
			return $value;
		}

		return $value->format("Y-m-d H:i:s");
	}

}
