<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Facades\Filter;
use Vespakoen\Epi\Interfaces\Extractors\FilterExtractorInterface;

use Illuminate\Support\Str;

class FilterExtractor implements FilterExtractorInterface {

	protected $operators = array(
		'<=',
		'>=',
		'<>',
		'!=',
		'=',
		'<',
		'>'
	);

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function extract(array $input)
	{
		// where in the input should we look?
		$key = $this->config['keys']['filter'];

		// is there a filter at all?
		if( ! array_key_exists($key, $input))
		{
			return array();
		}

		// make some filters!
		$filters = array();
		foreach($input[$key] as $rawRelationIdentifierAndColumn => $rawOperatorAndValue)
		{
			list($relationIdentifier, $column) = $this->extractRelationIdentifierAndColumn($rawRelationIdentifierAndColumn);
			list($operator, $value) = $this->extractOperatorAndValue($rawOperatorAndValue);

			$filters[] = Filter::make($relationIdentifier, $column, $operator, $value);
		}

		return $filters;
	}

	protected function extractRelationIdentifierAndColumn($rawRelationIdentifierAndColumn)
	{
		$relationNames = explode('.', $rawRelationIdentifierAndColumn);
		$column = array_pop($relationNames);
		$relationIdentifier = implode('.', $relationNames);

		return array(
			$relationIdentifier,
			$column
		);
	}

	protected function extractOperatorAndValue($rawOperatorAndValue)
	{
		foreach($this->operators as $operator)
		{
			if(Str::startsWith($rawOperatorAndValue, $operator))
			{
				$value = substr($rawOperatorAndValue, strlen($operator));
				break;
			}
		}

		if(Str::startsWith($rawOperatorAndValue, '%') || Str::endsWith($rawOperatorAndValue, '%'))
		{
			$operator = 'LIKE';
		}

		return array(
			$operator,
			$value
		);
	}

}
