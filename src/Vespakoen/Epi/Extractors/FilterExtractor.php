<?php namespace Vespakoen\Epi\Extractors;

use Vespakoen\Epi\Manipulators\Filter;
use Vespakoen\Epi\Interfaces\Extractors\FilterExtractorInterface;

use Illuminate\Support\Str;

class FilterExtractor extends Extractor implements FilterExtractorInterface {

	protected $operators = array(
		'<=',
		'>=',
		'<>',
		'!=',
		'=',
		'<',
		'>'
	);

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

			$table = $this->getSafeAliasedTableName($relationIdentifier);

			$filters[] = Filter::make($relationIdentifier, $table, $column, $operator, $value);
		}

		return $filters;
	}

	public function getOperators()
	{
		return $this->operators;
	}

	protected function extractOperatorAndValue($value)
	{
		$matchedOperator = '=';

		foreach($this->operators as $operator)
		{
			if(Str::startsWith($value, $operator))
			{
				$matchedOperator = $operator;
				$value = substr($value, strlen($operator));
				break;
			}
		}

		if(Str::startsWith($value, '%') || Str::endsWith($value, '%'))
		{
			$matchedOperator = 'LIKE';
		}

		return array(
			$matchedOperator,
			$value
		);
	}

}
