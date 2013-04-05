<?php

use Illuminate\Support\Facades\Input;

use Vespakoen\EPI\EPI;

class EPITest extends PHPUnit_Framework_TestCase {

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

		$epi = EPI::modelInstance($modelMock)
			->with(array('something'))
			->get();
	}

}