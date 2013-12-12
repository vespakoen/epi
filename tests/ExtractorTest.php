<?php

require '_start.php';

use Vespakoen\Epi\Extractors\FilterExtractor;
use Vespakoen\Epi\Extractors\SorterExtractor;
use Vespakoen\Epi\Extractors\LimiterExtractor;
use Vespakoen\Epi\Extractors\JoinExtractor;

class Query {

	public $result;

	public function where($column, $operator = null, $value = null, $boolean = 'and')
	{
		$this->result = func_get_args();

		return $this;
	}

	public function orderBy($column, $direction = 'asc')
	{
		$this->result = func_get_args();

		return $this;
	}

	public function skip($skip)
	{
		$this->result[0] = $skip;

		return $this;
	}

	public function take($take)
	{
		$this->result[1] = $take;

		return $this;
	}

	public function getResult()
	{
		$result = $this->result;
		$this->result = null;

		return $result;
	}

}

class ExtractorTest extends EpiTests {

	public $filterExtractor;

	public function setup()
	{
		parent::setup();

		$app = $this->app;
		$this->filterExtractor = new FilterExtractor($app);
		$this->sorterExtractor = new SorterExtractor($app);
		$this->limiterExtractor = new LimiterExtractor($app);
		$this->joinExtractor = new JoinExtractor($app);
	}

	public function testCanExtractFilters()
	{
		$input = array(
			'filter' => array(
				'name' => 'koen'
			)
		);

		$filter = end($this->filterExtractor->extract($input));

		$this->assertEquals($filter->getRelationIdentifier(), null);

		$query = new Query();
		$filter->applyTo($query);
		list($tableAndColumn, $operator, $value) = $query->getResult();

		$this->assertEquals($tableAndColumn, 'users.name');
		$this->assertEquals($operator, '=');
		$this->assertEquals($value, 'koen');
	}

	public function testCanExtractSorters()
	{
		$input = array(
			'sort' => array(
				'name' => 'asc'
			)
		);

		$sorter = end($this->sorterExtractor->extract($input));

		$query = new Query();
		$sorter->applyTo($query);
		list($tableAndColumn, $direction) = $query->getResult();

		$this->assertEquals($tableAndColumn, 'users.name');
		$this->assertEquals($direction, 'asc');
	}

	public function testCanExtractLimiters()
	{
		$input = array(
			'skip' => 1,
			'take' => 3
		);

		$limiter = end($this->limiterExtractor->extract($input));

		$query = new Query();
		$limiter->applyTo($query);
		list($skip, $take) = $query->getResult();

		$this->assertEquals($skip, $input['skip']);
		$this->assertEquals($take, $input['take']);
	}

	// public function testCanExtractJoins()
	// {
	// 	$input = array(
	// 		'filter' => array(
	// 			'name' => 'koen'
	// 		)
	// 	);
	// 	$filters = $this->filterExtractor->extract($input);

	// 	$input = array(
	// 		'sort' => array(
	// 			'name' => 'asc'
	// 		)
	// 	);
	// 	$sorters = $this->sorterExtractor->extract($input);

	// 	$joins = $this->joinExtractor->extract($filters, $sorters);

	// 	dd($joins);
	// }

}
