<?php namespace Vespakoen\Epi;

use Vespakoen\Epi\Collections\ExtractorCollection;
use Vespakoen\Epi\Stores\ManipulatorStore;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EpiHTML {

	public static function sortUrl($columns)
	{
		$columns = (array) $columns;

		$input = Input::get();

		$key = Config::get('epi::epi.keys.sort');

		$sorters = array();
		foreach($columns as $column)
		{
			$input[$key][$column] = static::getDirection($column);
		}

		return URL::current().'?'.http_build_query($input, null, '&');
	}

	public static function getDirection($column)
	{
		$key = Config::get('epi::epi.keys.sort');

		$currentSorters = Input::get($key);

		$direction = 'asc';
		if(is_array($currentSorters) && array_key_exists($column, $currentSorters))
		{
			$currentDirection = strtolower($currentSorters[$column]);
			$direction = $currentDirection == 'asc' ? 'desc' : 'asc';
		}

		return $direction;
	}

	public static function sortLink($columns, $label)
	{
		$direction = static::getDirection(is_array($columns) ? $columns[0] : $columns);

		$drop = $direction == 'desc' ? '<span class="caret"></span>' : '<span class="dropup"><span class="caret"></span></span>';

		return '<a href="'.static::sortUrl($columns).'">'.trans($label).' '.$drop.'</a>';
	}

}
