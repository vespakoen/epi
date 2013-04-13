<?php

use Illuminate\Support\Facades\Input;

use Vespakoen\Epi\Epi;

class EpiTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		Mockery::close();
	}

	public function testModelCanBeSet()
	{
		$queryMock = Mockery::mock('EloquentQuery');

		$queryMock->shouldReceive('with')
			->once()
			->andReturn('foo');

		$modelMock = Mockery::mock('EloquentModel');

		$modelMock->shouldReceive('newQuery')
			->once()
			->andReturn($queryMock);

		$modelMock->shouldReceive('with')
			->once()
			->andReturn('foo');

		Input::shouldReceive('fefe');

		$epi = Epi::modelInstance($modelMock)
			->with(array('something'))
			->get();
	}

}