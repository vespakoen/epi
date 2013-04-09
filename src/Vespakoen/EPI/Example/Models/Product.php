<?php namespace Vespakoen\EPI\Example\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Product extends Eloquent {

	public $sequence = 'products_id_seq';

	public $table = 'products';

}
